<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Asset;
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

        return view('dashboard', compact(
            'totalBalance',
            'monthlyIncome',
            'monthlyExpense',
            'totalAssetValue',
            'recentTransactions'
        ));
    }
}
