<?php

namespace App\Http\Controllers;

use App\Actions\Accounts\ApplyTransactionToAccount;
use App\Actions\Assets\ApplyTransactionToAsset;
use App\Actions\Transactions\CreateTransaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Account;
use App\Models\Asset;
use App\Models\AssetPriceHistory;
use App\Models\Category;
use App\Models\ExchangeRate;
use App\Models\SavingsGoal;
use App\Models\Tag;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['account', 'category', 'tags'])->latest('date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag_id);
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $search = '%'.strtolower($request->search).'%';
            $query->where(function ($q) use ($search) {
                // Using generic like for broader compatibility, but lowercase it
                $q->whereRaw('LOWER(description) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(notes) LIKE ?', [$search])
                    ->orWhereHas('category', function ($qc) use ($search) {
                        $qc->whereRaw('LOWER(name) LIKE ?', [$search]);
                    });
            });
        }

        $transactions = $query->paginate(20);
        $categories = Category::all();
        $accounts = Account::all();
        $tags = Tag::all();

        return view('transactions.index', compact('transactions', 'categories', 'accounts', 'tags'));
    }

    public function create(Request $request)
    {
        $categories = Category::all();
        $accounts = Account::all();
        $tags = Tag::all();
        $savingsGoals = SavingsGoal::where('status', 'active')->get();
        $selectedAsset = null;

        if ($request->filled('asset_id')) {
            $selectedAsset = Asset::find($request->asset_id);
        }

        return view('transactions.create', compact('categories', 'accounts', 'tags', 'savingsGoals', 'selectedAsset'));
    }

    public function transfer()
    {
        $accounts = Account::all();

        return view('transactions.transfer', compact('accounts'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('receipt')) {
            $disk = config('filesystems.receipts_disk');
            $path = $request->file('receipt')->store('receipts', $disk);
            $validated['receipt_path'] = $path;
        }

        DB::transaction(function () use ($validated) {
            $transaction = app(CreateTransaction::class)->handle($validated);

            $tags = $validated['tags'] ?? [];
            $transaction->tags()->sync($tags);

            if (! empty($validated['savings_goal_id'])) {
                SavingsGoal::adjustAmount((int) $validated['savings_goal_id'], $validated['type'], (float) $validated['amount']);
            }

            $account = Account::findOrFail($validated['account_id']);
            app(ApplyTransactionToAccount::class)->handle($account, $validated['type'], (float) $validated['amount']);

            if ($validated['type'] === 'transfer' && ! empty($validated['destination_account_id'])) {
                $destinationAccount = Account::findOrFail($validated['destination_account_id']);
                $convertedAmount = ExchangeRate::convert((float) $validated['amount'], $account->currency, $destinationAccount->currency);
                app(ApplyTransactionToAccount::class)->handle($destinationAccount, 'income', $convertedAmount);
            }

            $assetId = $validated['asset_id'] ?? null;
            $quantity = $validated['quantity'] ?? null;

            if ($assetId !== null && $quantity !== null) {
                $asset = Asset::findOrFail($assetId);

                app(ApplyTransactionToAsset::class)->handle(
                    $asset,
                    $validated['type'],
                    (float) $validated['amount'],
                    (float) $quantity,
                    $validated['date'],
                );
            }
        });

        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))->with('success', 'Transaction added successfully.');
        }

        return redirect()->route('transactions.index')->with('success', 'Transaction added successfully.');
    }

    public function edit(Transaction $transaction)
    {
        $categories = Category::all();
        $accounts = Account::all();
        $tags = Tag::all();
        $savingsGoals = SavingsGoal::all();

        return view('transactions.edit', compact('transaction', 'categories', 'accounts', 'tags', 'savingsGoals'));
    }

    public function update(StoreTransactionRequest $request, Transaction $transaction)
    {
        $validated = $request->validated();

        $disk = config('filesystems.receipts_disk');

        if ($request->boolean('delete_receipt')) {
            if ($transaction->receipt_path) {
                Storage::disk($disk)->delete($transaction->receipt_path);
            }
            $validated['receipt_path'] = null;
        } elseif ($request->hasFile('receipt')) {
            if ($transaction->receipt_path) {
                Storage::disk($disk)->delete($transaction->receipt_path);
            }
            $path = $request->file('receipt')->store('receipts', $disk);
            $validated['receipt_path'] = $path;
        }

        DB::transaction(function () use ($validated, $transaction) {
            // Reverse old savings goal contribution/withdrawal
            if ($transaction->savings_goal_id !== null) {
                SavingsGoal::adjustAmount($transaction->savings_goal_id, $transaction->type, (float) $transaction->amount, true);
            }

            // Reverse old balances
            $oldAccount = $transaction->account;
            $oldAmount = (float) $transaction->amount;

            if ($transaction->type === 'income') {
                $oldAccount->decrement('balance', $oldAmount);
            } else {
                $oldAccount->increment('balance', $oldAmount);
                if ($transaction->type === 'transfer' && $transaction->destination_account_id) {
                    $oldConvertedAmount = ExchangeRate::convert($oldAmount, $oldAccount->currency, $transaction->destinationAccount->currency);
                    $transaction->destinationAccount->decrement('balance', $oldConvertedAmount);
                }
            }

            // Reverse old asset quantities and prices if applicable
            $oldAssetId = $transaction->asset_id;
            $oldQuantity = (float) $transaction->quantity;
            $oldType = $transaction->type;

            if ($oldAssetId !== null && $oldQuantity > 0) {
                $oldAsset = Asset::find($oldAssetId);
                if ($oldAsset) {
                    if ($oldType === 'expense') {
                        $newQty = $oldAsset->quantity - $oldQuantity;
                        if ($newQty > 0) {
                            $oldAsset->purchase_price = (($oldAsset->quantity * $oldAsset->purchase_price) - $oldAmount) / $newQty;
                        } else {
                            $oldAsset->purchase_price = 0;
                        }
                        $oldAsset->quantity = $newQty;
                    } elseif ($oldType === 'income') {
                        $oldAsset->quantity += $oldQuantity;
                    }
                    $oldAsset->save();

                    // Clean up corresponding price history entry
                    AssetPriceHistory::where('asset_id', $oldAssetId)
                        ->where('date', $transaction->date->toDateString())
                        ->where('price', $oldAmount / $oldQuantity)
                        ->first()?->delete();
                }
            }

            // Update transaction
            $transaction->update(Arr::only($validated, [
                'account_id', 'destination_account_id', 'category_id', 'asset_id', 'quantity',
                'amount', 'type', 'date', 'description', 'notes', 'savings_goal_id', 'receipt_path',
            ]));

            $tags = $validated['tags'] ?? [];
            $transaction->tags()->sync($tags);

            // Apply new savings goal contribution/withdrawal
            if (! empty($validated['savings_goal_id'])) {
                SavingsGoal::adjustAmount((int) $validated['savings_goal_id'], $validated['type'], (float) $validated['amount']);
            }

            // Apply new balances
            $newAccount = Account::findOrFail($validated['account_id']);
            app(ApplyTransactionToAccount::class)->handle($newAccount, $validated['type'], (float) $validated['amount']);

            if ($validated['type'] === 'transfer' && ! empty($validated['destination_account_id'])) {
                $newDestination = Account::findOrFail($validated['destination_account_id']);
                $convertedAmount = ExchangeRate::convert((float) $validated['amount'], $newAccount->currency, $newDestination->currency);
                app(ApplyTransactionToAccount::class)->handle($newDestination, 'income', $convertedAmount);
            }

            // Apply new asset quantities and prices if applicable
            $newAssetId = $transaction->asset_id;
            $newQuantity = (float) $transaction->quantity;
            $newAmount = (float) $transaction->amount;
            $newType = $transaction->type;
            $newDate = $transaction->date->toDateString();

            if ($newAssetId !== null && $newQuantity > 0) {
                $newAsset = Asset::findOrFail($newAssetId);
                app(ApplyTransactionToAsset::class)->handle(
                    $newAsset,
                    $newType,
                    $newAmount,
                    $newQuantity,
                    $newDate
                );
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            if ($transaction->receipt_path) {
                Storage::disk(config('filesystems.receipts_disk'))->delete($transaction->receipt_path);
            }

            if ($transaction->savings_goal_id !== null) {
                SavingsGoal::adjustAmount($transaction->savings_goal_id, $transaction->type, (float) $transaction->amount, true);
            }

            $account = $transaction->account;

            if ($transaction->type === 'income') {
                $account->decrement('balance', (float) $transaction->amount);
            } else {
                $account->increment('balance', (float) $transaction->amount);

                if ($transaction->type === 'transfer' && $transaction->destination_account_id) {
                    $convertedAmount = ExchangeRate::convert((float) $transaction->amount, $account->currency, $transaction->destinationAccount->currency);
                    $transaction->destinationAccount->decrement('balance', $convertedAmount);
                }
            }

            // Reverse asset quantities and prices if applicable
            $assetId = $transaction->asset_id;
            $quantity = (float) $transaction->quantity;
            $amount = (float) $transaction->amount;
            $type = $transaction->type;

            if ($assetId !== null && $quantity > 0) {
                $asset = Asset::find($assetId);
                if ($asset) {
                    if ($type === 'expense') {
                        $newQty = $asset->quantity - $quantity;
                        if ($newQty > 0) {
                            $asset->purchase_price = (($asset->quantity * $asset->purchase_price) - $amount) / $newQty;
                        } else {
                            $asset->purchase_price = 0;
                        }
                        $asset->quantity = $newQty;
                    } elseif ($type === 'income') {
                        $asset->quantity += $quantity;
                    }
                    $asset->save();

                    // Clean up corresponding price history entry
                    AssetPriceHistory::where('asset_id', $assetId)
                        ->where('date', $transaction->date->toDateString())
                        ->where('price', $amount / $quantity)
                        ->first()?->delete();
                }
            }

            $transaction->delete();
        });

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted.');
    }
}
