@extends('layouts.app')
@section('title', 'Transactions')

@section('actions')
<div class="flex flex-wrap items-center gap-2">
    <!-- Import Form (Hidden) -->
    <form action="{{ route('transactions.import') }}" method="POST" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="file" name="csv_file" id="import-csv" accept=".csv" onchange="this.form.submit()">
    </form>
    
    <button onclick="document.getElementById('import-csv').click()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white text-slate-700 text-sm font-medium rounded-xl border border-slate-200 hover:bg-slate-50 transition shadow-sm">
        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
        <span class="hidden sm:inline">Import</span>
    </button>
    
    <a href="{{ route('transactions.export') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white text-slate-700 text-sm font-medium rounded-xl border border-slate-200 hover:bg-slate-50 transition shadow-sm">
        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
        <span class="hidden sm:inline">Export</span>
    </a>

    <!-- Divider -->
    <div class="h-6 w-px bg-slate-200 mx-1 hidden sm:block"></div>

    <a href="{{ route('transactions.transfer') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-900 transition shadow-sm">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
        <span class="hidden sm:inline">Transfer</span>
    </a>
    <a href="{{ route('transactions.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
        <span class="hidden sm:inline">Add</span>
    </a>
</div>
@endsection

@section('content')
<div class="space-y-4">
    <!-- Filters -->
    <form method="GET" action="{{ route('transactions.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <select name="type" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Types</option>
                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
            </select>
            <select name="category_id" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="account_id" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All Accounts</option>
                @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                @endforeach
            </select>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Start Date">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="End Date">
        </div>
        <div class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description, notes, category..." class="flex-1 rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            <button type="submit" class="px-6 py-2 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-900 transition flex items-center justify-center whitespace-nowrap">Filter</button>
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
                            @if($t->category)
                            <span class="inline-flex items-center gap-1.5 text-sm">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $t->category->color }}"></span>
                                {{ $t->category->name }}
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 text-sm text-slate-400">
                                <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                Transfer
                            </span>
                            @endif
                        </td>
                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-sm text-slate-500 hidden md:table-cell">
                            @if($t->type === 'transfer')
                                {{ $t->account->name }} &rarr; {{ $t->destinationAccount?->name ?? '?' }}
                            @else
                                {{ $t->account->name }}
                            @endif
                        </td>
                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-sm font-semibold text-right {{ $t->type === 'income' ? 'text-emerald-600' : ($t->type === 'transfer' ? 'text-indigo-600' : 'text-rose-600') }}">
                            {{ $t->type === 'income' ? '+' : ($t->type === 'expense' ? '-' : '') }}Rp {{ number_format($t->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2 text-slate-400">
                                <a href="{{ route('transactions.create', [
                                    'type' => $t->type,
                                    'amount' => (int) $t->amount,
                                    'category_id' => $t->category_id,
                                    'account_id' => $t->account_id,
                                    'description' => $t->description
                                ]) }}" class="hover:text-emerald-600 transition" title="Duplicate">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </a>
                                <a href="{{ route('transactions.edit', $t) }}" class="hover:text-indigo-600 transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                                <form action="{{ route('transactions.destroy', $t) }}" method="POST" class="inline" x-data @submit.prevent="$dispatch('open-confirm-modal', { action: $el.action, message: 'Are you sure you want to delete this transaction?' })">
                                    @csrf @method('DELETE')
                                    <button class="hover:text-rose-600 transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
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
