@extends('layouts.app')
@section('title', 'Transfer Between Accounts')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8" x-data="{ amount: '{{ old('amount') }}' }" x-init="amount = DompetkuNumberFormat.formatNumber(amount)">
        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="type" value="transfer">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-form.select label="From Account (Source)" name="account_id" required>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                    @endforeach
                </x-form.select>

                <x-form.select label="To Account (Destination)" name="destination_account_id" required>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('destination_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                    @endforeach
                </x-form.select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-form.money-input label="Amount" name="amount" model="amount" required placeholder="0.00" />
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Date</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Notes / Description</label>
                <textarea name="description" rows="2" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional notes about this transfer...">{{ old('description') }}</textarea>
            </div>

            <x-ui.errors />

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition shadow-sm shadow-indigo-200 uppercase tracking-widest">Execute Transfer</button>
                <a href="{{ route('transactions.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition uppercase tracking-widest">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
