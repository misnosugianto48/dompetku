<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecurringTransactionRequest;
use App\Http\Requests\UpdateRecurringTransactionRequest;
use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringTransaction;

class RecurringTransactionController extends Controller
{
    public function index()
    {
        $recurrings = RecurringTransaction::with(['account', 'category'])->get();
        $categories = Category::all();
        $accounts = Account::all();

        return view('recurring_transactions.index', compact('recurrings', 'categories', 'accounts'));
    }

    public function store(StoreRecurringTransactionRequest $request)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->has('is_active');
        RecurringTransaction::create($validated);

        return redirect()->route('recurring.index')->with('success', 'Recurring transaction created successfully.');
    }

    public function update(UpdateRecurringTransactionRequest $request, RecurringTransaction $recurring)
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->has('is_active');
        $recurring->update($validated);

        return redirect()->route('recurring.index')->with('success', 'Recurring transaction updated successfully.');
    }

    public function toggle(RecurringTransaction $recurring)
    {
        $recurring->update(['is_active' => ! $recurring->is_active]);

        return redirect()->route('recurring.index')->with('success', 'Status updated.');
    }

    public function destroy(RecurringTransaction $recurring)
    {
        $recurring->delete();

        return redirect()->route('recurring.index')->with('success', 'Recurring transaction deleted.');
    }
}
