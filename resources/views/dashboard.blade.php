@extends('layouts.app')
@section('title', 'Dashboard')

@section('actions')
<a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
    <span class="hidden sm:inline">Add Transaction</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <p class="text-xs sm:text-sm font-medium text-slate-500">Total Balance</p>
            <h2 class="text-lg sm:text-2xl font-bold mt-1 text-slate-900">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h2>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <p class="text-xs sm:text-sm font-medium text-slate-500">Income</p>
            <h2 class="text-lg sm:text-2xl font-bold mt-1 text-emerald-600">+Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</h2>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <p class="text-xs sm:text-sm font-medium text-slate-500">Expense</p>
            <h2 class="text-lg sm:text-2xl font-bold mt-1 text-rose-600">-Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</h2>
        </div>
        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <p class="text-xs sm:text-sm font-medium text-slate-500">Asset Value</p>
            <h2 class="text-lg sm:text-2xl font-bold mt-1 text-indigo-600">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</h2>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
        <div class="px-4 sm:px-6 py-4 flex justify-between items-center border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Recent Transactions</h3>
            <a href="{{ route('transactions.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Date</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Category</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase hidden sm:table-cell">Account</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentTransactions as $t)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-4 sm:px-6 py-3.5 whitespace-nowrap text-sm text-slate-600">{{ $t->date->format('d M') }}</td>
                        <td class="px-4 sm:px-6 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $t->category->color }}"></div>
                                <span class="text-sm text-slate-800">{{ $t->category->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 sm:px-6 py-3.5 whitespace-nowrap text-sm text-slate-500 hidden sm:table-cell">{{ $t->account->name }}</td>
                        <td class="px-4 sm:px-6 py-3.5 whitespace-nowrap text-sm font-semibold text-right {{ $t->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $t->type === 'income' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">No transactions yet. Add your first one!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
