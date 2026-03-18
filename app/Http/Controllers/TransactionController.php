<?php

namespace App\Http\Controllers;

use App\Actions\Accounts\ApplyTransactionToAccount;
use App\Actions\Assets\ApplyTransactionToAsset;
use App\Actions\Transactions\CreateTransaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Account;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['account', 'category'])->latest('date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->paginate(20);
        $categories = Category::all();
        $accounts = Account::all();

        return view('transactions.index', compact('transactions', 'categories', 'accounts'));
    }

    public function create(Request $request)
    {
        $categories = Category::all();
        $accounts = Account::all();
        $selectedAsset = null;

        if ($request->filled('asset_id')) {
            $selectedAsset = Asset::find($request->asset_id);
        }

        return view('transactions.create', compact('categories', 'accounts', 'selectedAsset'));
    }

    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $transaction = app(CreateTransaction::class)->handle($validated);

            $account = Account::findOrFail($validated['account_id']);
            app(ApplyTransactionToAccount::class)->handle($account, $validated['type'], (float) $validated['amount']);

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

        return redirect()->route('transactions.index')->with('success', 'Transaction added successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $account = $transaction->account;

        if ($transaction->type === 'income') {
            $account->decrement('balance', (float) $transaction->amount);
        } else {
            $account->increment('balance', (float) $transaction->amount);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted.');
    }
}
