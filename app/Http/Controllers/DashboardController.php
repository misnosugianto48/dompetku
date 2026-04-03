<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Asset;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $totalBalance = Account::sum('balance');

        $monthlyIncome = Transaction::where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $monthlyExpense = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $totalAssetValue = Asset::sum(\DB::raw('quantity * current_price'));

        $recentTransactions = Transaction::with(['category', 'account'])
            ->latest('date')
            ->take(10)
            ->get();

        // 1. 6-Month Income vs Expense Chart Data
        $months = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $months[] = $monthDate->format('M Y');

            $mStart = $monthDate->copy()->startOfMonth()->toDateString();
            $mEnd = $monthDate->copy()->endOfMonth()->toDateString();

            $incomeData[] = (float) Transaction::where('type', 'income')
                ->whereBetween('date', [$mStart, $mEnd])
                ->sum('amount');

            $expenseData[] = (float) Transaction::where('type', 'expense')
                ->whereBetween('date', [$mStart, $mEnd])
                ->sum('amount');
        }

        // 2. Expense Category Breakdown Donut Chart Data
        $categoryExpenses = Transaction::with('category')
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->select('category_id', \Illuminate\Support\Facades\DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();

        $expenseCategories = [];
        $expenseValues = [];
        $expenseColors = [];

        foreach ($categoryExpenses as $ce) {
            $cat = $ce->category;
            if ($cat) {
                $expenseCategories[] = $cat->name;
                $expenseValues[] = (float) $ce->total;
                $expenseColors[] = $cat->color ?: '#cbd5e1';
            }
        }

        // 3. Budgets Tracking (Current Month)
        $budgets = Budget::with('category')->get()->map(function ($budget) use ($categoryExpenses) {
            $expenseObj = $categoryExpenses->firstWhere('category_id', $budget->category_id);
            $spent = $expenseObj ? clone $expenseObj->total : 0;
            $budget->spent = (float) $spent;
            $budget->percentage = $budget->amount > 0 ? min(100, round(($budget->spent / $budget->amount) * 100)) : 0;

            return $budget;
        });

        // 4. Net Worth History (Last 6 Months Estimate)
        $runningNetWorth = $totalBalance + $totalAssetValue;
        $netWorthData = [];
        $netWorthLabels = [];

        for ($i = 0; $i <= 5; $i++) {
            $idx = 5 - $i;
            $monthDate = Carbon::now()->subMonths($i);
            $netWorthLabels[$idx] = $monthDate->format('M Y');

            if ($i == 0) {
                $netWorthData[$idx] = (float) $runningNetWorth;
            } else {
                $nextMonthDate = Carbon::now()->subMonths($i - 1);
                $mStartNext = $nextMonthDate->copy()->startOfMonth()->toDateString();
                $mEndNext = $nextMonthDate->copy()->endOfMonth()->toDateString();

                $mIncomeNext = (float) Transaction::where('type', 'income')->whereBetween('date', [$mStartNext, $mEndNext])->sum('amount');
                $mExpenseNext = (float) Transaction::where('type', 'expense')->whereBetween('date', [$mStartNext, $mEndNext])->sum('amount');
                $mNetCashflowNext = $mIncomeNext - $mExpenseNext;

                $runningNetWorth -= $mNetCashflowNext;
                $netWorthData[$idx] = (float) $runningNetWorth;
            }
        }
        ksort($netWorthData);
        ksort($netWorthLabels);

        $formCategories = Category::all();
        $formAccounts = Account::all();

        return view('dashboard', compact(
            'totalBalance',
            'monthlyIncome',
            'monthlyExpense',
            'totalAssetValue',
            'recentTransactions',
            'months',
            'incomeData',
            'expenseData',
            'expenseCategories',
            'expenseValues',
            'expenseColors',
            'budgets',
            'netWorthData',
            'netWorthLabels',
            'formCategories',
            'formAccounts'
        ));
    }
}
