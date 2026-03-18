@extends('layouts.app')
@section('title', 'Reports')

@section('actions')
<a href="{{ route('reports.export-pdf', request()->query()) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 text-white text-sm font-medium rounded-xl hover:bg-rose-700 transition shadow-sm">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
    <span class="hidden sm:inline">Export PDF</span>
</a>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Period Filter -->
    <form method="GET" action="{{ route('reports.index') }}" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <select name="period" onchange="this.form.submit()" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Daily</option>
                <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Yearly</option>
            </select>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-900 transition">Apply</button>
        </div>
    </form>

    <!-- Summary -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-xs font-medium text-slate-500">Income</p>
            <h3 class="text-lg sm:text-xl font-bold text-emerald-600 mt-1">+Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-xs font-medium text-slate-500">Expense</p>
            <h3 class="text-lg sm:text-xl font-bold text-rose-600 mt-1">-Rp {{ number_format($totalExpense, 0, ',', '.') }}</h3>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-xs font-medium text-slate-500">Net Flow</p>
            <h3 class="text-lg sm:text-xl font-bold mt-1 {{ $netFlow >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ $netFlow >= 0 ? '+' : '' }}Rp {{ number_format($netFlow, 0, ',', '.') }}
            </h3>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-100">
            <p class="text-xs font-medium text-slate-500">Total Balance</p>
            <h3 class="text-lg sm:text-xl font-bold text-slate-900 mt-1">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expense Breakdown -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Expense by Category</h3>
            <div class="space-y-3">
                @forelse($expenseByCategory as $cat)
                @php $pct = $totalExpense > 0 ? ($cat['total'] / $totalExpense) * 100 : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-600">{{ $cat['name'] }}</span>
                        <span class="font-medium">Rp {{ number_format($cat['total'], 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all" style="width: {{ $pct }}%; background-color: {{ $cat['color'] }}"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-400 italic">No expenses in this period.</p>
                @endforelse
            </div>
        </div>

        <!-- Income Breakdown -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Income by Category</h3>
            <div class="space-y-3">
                @forelse($incomeByCategory as $cat)
                @php $pct = $totalIncome > 0 ? ($cat['total'] / $totalIncome) * 100 : 0; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-600">{{ $cat['name'] }}</span>
                        <span class="font-medium">Rp {{ number_format($cat['total'], 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="h-2 rounded-full bg-emerald-500 transition-all" style="width: {{ $pct }}%; background-color: {{ $cat['color'] }}"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-slate-400 italic">No income in this period.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Transaction List -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Transactions ({{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase hidden sm:table-cell">Account</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase hidden md:table-cell">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-3 text-sm text-slate-600">{{ $t->date->format('d M Y') }}</td>
                        <td class="px-6 py-3 text-sm">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $t->category->color }}"></span>
                                {{ $t->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-sm text-slate-500 hidden sm:table-cell">{{ $t->account->name }}</td>
                        <td class="px-6 py-3 text-sm text-slate-500 hidden md:table-cell max-w-[200px] truncate">{{ $t->description ?: '-' }}</td>
                        <td class="px-6 py-3 text-sm font-semibold text-right {{ $t->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $t->type === 'income' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">No transactions in this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
