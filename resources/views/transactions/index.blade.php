@extends('layouts.app')
@section('title', 'Transactions')

@section('actions')
<a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
    <span class="hidden sm:inline">Add</span>
</a>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Filters -->
    <form method="GET" action="{{ route('transactions.index') }}" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
            <select name="type" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Types</option>
                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
            </select>
            <select name="category_id" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="account_id" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Accounts</option>
                @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                @endforeach
            </select>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Start Date">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="End Date">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-900 transition">Filter</button>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Date</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase">Description</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase hidden sm:table-cell">Category</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase hidden md:table-cell">Account</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Amount</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-sm text-slate-600">{{ $t->date->format('d M Y') }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-slate-800 max-w-[200px] truncate">{{ $t->description ?: '-' }}</td>
                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap hidden sm:table-cell">
                            <span class="inline-flex items-center gap-1.5 text-sm">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $t->category->color }}"></span>
                                {{ $t->category->name }}
                            </span>
                        </td>
                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-sm text-slate-500 hidden md:table-cell">{{ $t->account->name }}</td>
                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-sm font-semibold text-right {{ $t->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $t->type === 'income' ? '+' : '-' }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-right">
                            <form action="{{ route('transactions.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Delete this transaction?')">
                                @csrf @method('DELETE')
                                <button class="text-slate-400 hover:text-rose-600 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">No transactions found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="px-6 py-3 border-t border-slate-100">{{ $transactions->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
