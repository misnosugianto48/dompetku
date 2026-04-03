<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use App\Models\Category;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $budgets = Budget::with('category')->get();
        $categories = Category::all();

        return view('budgets.index', compact('budgets', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBudgetRequest $request)
    {
        $data = $request->validated();
        $data['period'] = 'monthly';
        Budget::create($data);

        return redirect()->back()->with('success', 'Budget created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBudgetRequest $request, Budget $budget)
    {
        $data = $request->validated();
        $budget->update($data);

        return redirect()->back()->with('success', 'Budget updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget)
    {
        $budget->delete();

        return redirect()->back()->with('success', 'Budget deleted successfully.');
    }
}
