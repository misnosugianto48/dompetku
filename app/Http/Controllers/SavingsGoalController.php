<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSavingsGoalRequest;
use App\Http\Requests\UpdateSavingsGoalRequest;
use App\Models\SavingsGoal;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SavingsGoalController extends Controller
{
    public function index(): View
    {
        $savingsGoals = SavingsGoal::withCount('transactions')->get();

        return view('savings_goals.index', compact('savingsGoals'));
    }

    public function store(StoreSavingsGoalRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['status'] = (($validated['current_amount'] ?? 0) >= $validated['target_amount']) ? 'completed' : 'active';

        SavingsGoal::create($validated);

        return redirect()->route('savings-goals.index')->with('success', 'Savings Goal created successfully.');
    }

    public function edit(SavingsGoal $savingsGoal): View
    {
        return view('savings_goals.edit', compact('savingsGoal'));
    }

    public function update(UpdateSavingsGoalRequest $request, SavingsGoal $savingsGoal): RedirectResponse
    {
        $validated = $request->validated();
        $validated['status'] = ($validated['current_amount'] >= $validated['target_amount']) ? 'completed' : 'active';

        $savingsGoal->update($validated);

        return redirect()->route('savings-goals.index')->with('success', 'Savings Goal updated successfully.');
    }

    public function destroy(SavingsGoal $savingsGoal): RedirectResponse
    {
        $savingsGoal->delete();

        return redirect()->route('savings-goals.index')->with('success', 'Savings Goal deleted.');
    }
}
