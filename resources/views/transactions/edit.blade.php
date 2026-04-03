@extends('layouts.app')
@section('title', 'Edit Transaction')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8" x-data="{
        type: '{{ old('type', $transaction->type) }}',
        amount: '{{ old('amount', $transaction->amount) }}',
    }" x-init="amount = DompetkuNumberFormat.formatNumber(amount)">
        <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Transaction Type</label>
                    <div class="flex p-1 bg-slate-100 rounded-xl">
                        <label class="flex-1">
                            <input type="radio" name="type" value="expense" x-model="type" class="sr-only peer">
                            <span class="block text-center py-2 text-sm font-semibold rounded-lg cursor-pointer transition peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-500 hover:text-slate-700">Expense</span>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="type" value="income" x-model="type" class="sr-only peer">
                            <span class="block text-center py-2 text-sm font-semibold rounded-lg cursor-pointer transition peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm text-slate-500 hover:text-slate-700">Income</span>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="type" value="transfer" x-model="type" class="sr-only peer">
                            <span class="block text-center py-2 text-sm font-semibold rounded-lg cursor-pointer transition peer-checked:bg-white peer-checked:text-indigo-600 peer-checked:shadow-sm text-slate-500 hover:text-slate-700">Transfer</span>
                        </label>
                    </div>
                </div>
                <x-form.money-input label="Amount" name="amount" model="amount" required placeholder="0.00" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div x-show="type !== 'transfer'">
                    <x-form.select label="Category" name="category_id" required x-bind:required="type !== 'transfer'">
                        @foreach($categories->groupBy('type') as $catType => $cats)
                            <optgroup label="{{ ucfirst($catType) }}">
                                @foreach($cats as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </x-form.select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-form.select label="Account (Source)" name="account_id" required>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('account_id', $transaction->account_id) == $acc->id ? 'selected' : '' }}>{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                    @endforeach
                </x-form.select>

                <div x-show="type === 'transfer'">
                    <x-form.select label="Destination Account" name="destination_account_id" x-bind:required="type === 'transfer'">
                        <option value="">Select Account...</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('destination_account_id', $transaction->destination_account_id) == $acc->id ? 'selected' : '' }}>{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                        @endforeach
                    </x-form.select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Date</label>
                    <input type="date" name="date" value="{{ old('date', $transaction->date->format('Y-m-d')) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Description</label>
                <textarea name="description" rows="2" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $transaction->description) }}</textarea>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Notes</label>
                <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $transaction->notes) }}</textarea>
            </div>

            <x-ui.errors />

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition shadow-sm uppercase tracking-widest">Update Transaction</button>
                <a href="{{ route('transactions.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition uppercase tracking-widest">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
