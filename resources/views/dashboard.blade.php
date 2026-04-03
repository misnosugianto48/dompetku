@extends('layouts.app')
@section('title', 'Dashboard')

@section('actions')
<a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
    <span class="hidden sm:inline">Add Transaction</span>
</a>
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="space-y-6">
    <!-- Net Worth Hero Card -->
    <div class="bg-indigo-600 rounded-3xl shadow-lg border border-indigo-500 overflow-hidden relative">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxjaXJjbGUgY3g9IjMiIGN5PSIzIiByPSIzIiBmaWxsPSIjZmZmZmZmIiBmaWxsLW9wYWNpdHk9IjAuMSIvPjwvZz48L3N2Zz4=')]"></div>
        <div class="p-6 sm:p-8 relative z-10 flex flex-col lg:flex-row justify-between items-center gap-8">
            <div class="text-white w-full lg:w-1/3">
                <p class="text-indigo-200 font-medium mb-1 tracking-wide">Total Net Worth</p>
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight">Rp {{ number_format($totalBalance + $totalAssetValue, 0, ',', '.') }}</h1>
            </div>
            <div class="w-full lg:w-2/3 h-40">
                <canvas id="netWorthChart"></canvas>
            </div>
        </div>
    </div>

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

    <!-- Quick Add Form -->
    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 sm:p-5 shadow-sm" x-data="{ type: 'expense', amount: '' }" x-init="amount = DompetkuNumberFormat.formatNumber(amount)">
        <h3 class="font-semibold text-indigo-900 mb-3 flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            Quick Add
        </h3>
        <form method="POST" action="{{ route('transactions.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            @csrf
            <div>
                <select name="type" x-model="type" class="w-full rounded-xl border-indigo-200 text-sm focus:ring-indigo-500 bg-white py-2">
                    <option value="expense">Expense</option>
                    <option value="income">Income</option>
                </select>
            </div>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">Rp</span>
                <input type="text" x-model="amount" @input="amount = DompetkuNumberFormat.formatNumber($event.target.value)" required class="w-full pl-9 rounded-xl border-indigo-200 text-sm font-semibold focus:ring-indigo-500 bg-white py-2" placeholder="0.00">
                <input type="hidden" name="amount" :value="DompetkuNumberFormat.getRaw(amount)">
            </div>
            <div>
                <select name="account_id" required class="w-full rounded-xl border-indigo-200 text-sm focus:ring-indigo-500 bg-white py-2">
                    @foreach($formAccounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="category_id" required class="w-full rounded-xl border-indigo-200 text-sm focus:ring-indigo-500 bg-white py-2">
                    @foreach($formCategories as $cat)
                        <option value="{{ $cat->id }}" x-show="type === '{{ $cat->type }}'">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 rounded-xl transition shadow-sm">Save</button>
            </div>
        </form>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Bar Chart -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-4">Cash Flow (Last 6 Months)</h3>
            <div class="relative h-64">
                <canvas id="incomeExpenseChart"></canvas>
            </div>
        </div>
        
        <!-- Donut Chart -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-4">Expense Breakdown (This Period)</h3>
            @if(empty($expenseValues))
            <div class="h-64 flex flex-col items-center justify-center text-slate-400">
                <svg class="w-12 h-12 mb-3 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p>No expenses recorded</p>
            </div>
            @else
            <div class="relative h-64">
                <canvas id="expenseBreakdownChart"></canvas>
            </div>
            @endif
        </div>
    </div>

    <!-- Budget Progress -->
    @if(isset($budgets) && $budgets->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 sm:p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-semibold text-slate-800">Monthly Budget Tracking</h3>
            <a href="{{ route('budgets.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Manage →</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($budgets as $budget)
            <div>
                <div class="flex justify-between items-end mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $budget->category->color }}"></div>
                        <span class="text-sm font-medium text-slate-700">{{ $budget->category->name }}</span>
                    </div>
                    <span class="text-xs font-semibold {{ $budget->percentage >= 90 ? 'text-rose-600' : ($budget->percentage >= 75 ? 'text-amber-500' : 'text-slate-500') }}">
                        {{ $budget->percentage }}%
                    </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 mb-1 overflow-hidden">
                    <div class="h-2 rounded-full {{ $budget->percentage >= 90 ? 'bg-rose-500' : ($budget->percentage >= 75 ? 'bg-amber-400' : 'bg-emerald-500') }}" style="width: {{ $budget->percentage }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-slate-500">
                    <span>Rp {{ number_format($budget->spent, 0, ',', '.') }} spent</span>
                    <span>Rp {{ number_format($budget->amount, 0, ',', '.') }} limit</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Net Worth Line Chart
    const nwElement = document.getElementById('netWorthChart');
    if (nwElement) {
        new Chart(nwElement.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($netWorthLabels),
                datasets: [{
                    label: 'Net Worth',
                    data: @json($netWorthData),
                    borderColor: '#818cf8',
                    backgroundColor: 'rgba(129, 140, 248, 0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#818cf8',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                color: 'rgba(255,255,255,0.7)',
                scales: {
                    x: {
                        grid: { color: 'rgba(255,255,255,0.1)', borderColor: 'transparent' },
                        ticks: { color: 'rgba(255,255,255,0.7)' }
                    },
                    y: { 
                        display: false // Hide y axis cleanly inside the hero banner
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }

    // Income vs Expense Bar Chart
    const barElement = document.getElementById('incomeExpenseChart');
    if (barElement) {
        new Chart(barElement.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($months),
                datasets: [
                    {
                        label: 'Income',
                        data: @json($incomeData),
                        backgroundColor: '#10b981',
                        borderRadius: 4
                    },
                    {
                        label: 'Expense',
                        data: @json($expenseData),
                        backgroundColor: '#f43f5e',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000) + 'k';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }

    // Expense Breakdown Donut Chart
    const donutElement = document.getElementById('expenseBreakdownChart');
    if (donutElement) {
        new Chart(donutElement.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: @json($expenseCategories),
                datasets: [{
                    data: @json($expenseValues),
                    backgroundColor: @json($expenseColors),
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { 
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
