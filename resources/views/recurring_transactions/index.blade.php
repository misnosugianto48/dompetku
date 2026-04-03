@extends('layouts.app')
@section('title', 'Recurring Transactions')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- List -->
    <div class="lg:col-span-2 space-y-4">
        @forelse($recurrings as $rec)
        <div class="bg-white p-5 rounded-2xl shadow-sm border {{ $rec->is_active ? 'border-indigo-100' : 'border-slate-300 opacity-70' }} hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $rec->type === 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        @if($rec->type === 'income')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                        @endif
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800">{{ $rec->description }} <span class="text-xs font-semibold px-2 py-0.5 ml-1 rounded-full {{ $rec->is_active ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-100 text-slate-500' }}">{{ $rec->is_active ? 'Active' : 'Paused' }}</span></h4>
                        <p class="text-sm text-slate-500 mt-1">
                            {{ $rec->account->name }} • {{ $rec->category?->name ?? 'No Category' }} • <span class="text-indigo-600 font-bold capitalize">{{ $rec->frequency }}</span>
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-lg {{ $rec->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $rec->type === 'income' ? '+' : '-' }}Rp {{ number_format($rec->amount, 0, ',', '.') }}
                    </p>
                    <p class="text-xs font-semibold text-slate-400 mt-1">Next: <span class="text-slate-700">{{ $rec->next_due_date->format('d M Y') }}</span></p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-4">
                <form action="{{ route('recurring.toggle', $rec) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-sm font-bold {{ $rec->is_active ? 'text-amber-500 hover:text-amber-600' : 'text-emerald-500 hover:text-emerald-600' }} transition">{{ $rec->is_active ? '⏸ Pause' : '▶ Resume' }}</button>
                </form>

                <div x-data="{ openEdit: false }" class="relative ml-auto">
                    <button @click="openEdit = !openEdit" class="text-sm font-bold text-indigo-600 hover:text-indigo-700 transition mr-3">Edit</button>
                    <!-- Edit Modal -->
                    <div x-show="openEdit" @click.away="openEdit = false" class="absolute bottom-full right-0 mb-3 w-80 bg-white p-5 rounded-2xl shadow-2xl border border-slate-200 z-10" style="display: none;">
                        <h5 class="font-bold mb-3 text-slate-800">Edit Automation</h5>
                        <form action="{{ route('recurring.update', $rec) }}" method="POST" class="space-y-3">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="amount" value="{{ (int) $rec->amount }}" class="w-full rounded-xl text-sm border-slate-200" required placeholder="Amount">
                                <select name="frequency" class="w-full rounded-xl text-sm border-slate-200" required>
                                    <option value="daily" {{ $rec->frequency === 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ $rec->frequency === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="monthly" {{ $rec->frequency === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="yearly" {{ $rec->frequency === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                            </div>
                            <input type="date" name="next_due_date" value="{{ $rec->next_due_date->format('Y-m-d') }}" class="w-full rounded-xl text-sm border-slate-200" required>
                            <input type="text" name="description" value="{{ $rec->description }}" class="w-full rounded-xl text-sm border-slate-200" required>
                            <input type="hidden" name="type" value="{{ $rec->type }}">
                            <input type="hidden" name="account_id" value="{{ $rec->account_id }}">
                            <input type="hidden" name="category_id" value="{{ $rec->category_id }}">
                            <input type="hidden" name="is_active" value="{{ $rec->is_active ? 1 : 0 }}">
                            <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 transition text-white rounded-xl text-sm font-bold mt-2">Update Plan</button>
                        </form>
                    </div>
                </div>

                <form action="{{ route('recurring.destroy', $rec) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { action: $el.action, message: 'Delete this recurring plan?' })">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm font-bold text-rose-500 hover:text-rose-600 transition">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white p-10 rounded-2xl text-center shadow-sm border border-slate-100 flex flex-col items-center justify-center">
            <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            <h3 class="text-lg font-bold text-slate-800">No Automations Setup</h3>
            <p class="text-slate-500 mt-1 max-w-sm">Schedule regular bills or incomes to automatically track in the background.</p>
        </div>
        @endforelse
    </div>

    <!-- Create Form -->
    <div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 sticky top-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">New Automation</h3>
            <form action="{{ route('recurring.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="flex p-1 bg-slate-100 rounded-xl">
                    <label class="flex-1">
                         <input type="radio" name="type" value="expense" class="sr-only peer" checked>
                         <span class="block text-center py-2 text-sm font-bold rounded-lg cursor-pointer transition peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-500">Expense</span>
                     </label>
                     <label class="flex-1">
                         <input type="radio" name="type" value="income" class="sr-only peer">
                         <span class="block text-center py-2 text-sm font-bold rounded-lg cursor-pointer transition peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm text-slate-500">Income</span>
                     </label>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <input type="number" name="amount" min="0" placeholder="Amount (Rp)" class="col-span-2 w-full rounded-xl border-slate-200 focus:ring-indigo-500" required>
                    
                    <select name="category_id" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500">
                        <option value="">Category...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <select name="account_id" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500" required>
                        <option value="">Account...</option>
                        @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="text" name="description" placeholder="Description" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500" required>

                <div class="grid grid-cols-2 gap-3">
                    <select name="frequency" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500" required>
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                        <option value="daily">Daily</option>
                        <option value="yearly">Yearly</option>
                    </select>

                    <input type="date" name="next_due_date" value="{{ now()->format('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500" required>
                </div>

                <label class="flex items-center gap-2 cursor-pointer mt-2 text-sm font-medium text-slate-600">
                    <input type="checkbox" name="is_active" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked>
                    Activate immediately
                </label>

                <button type="submit" class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 transition shadow-sm mt-3 uppercase tracking-wider text-sm">Start Plan</button>
            </form>
        </div>
    </div>
</div>
@endsection
