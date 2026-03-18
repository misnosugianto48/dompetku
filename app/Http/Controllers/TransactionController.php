<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

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

    public function create()
    {
        $categories = Category::all();
        $accounts = Account::all();

        return view('transactions.create', compact('categories', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense',
            'date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        Transaction::create($validated);

        $account = Account::findOrFail($validated['account_id']);

        if ($validated['type'] === 'income') {
            $account->increment('balance', $validated['amount']);
        } else {
            $account->decrement('balance', $validated['amount']);
        }

        return redirect()->route('transactions.index')->with('success', 'Transaction added successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $account = $transaction->account;

        if ($transaction->type === 'income') {
            $account->decrement('balance', $transaction->amount);
        } else {
            $account->increment('balance', $transaction->amount);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted.');
    }
}
