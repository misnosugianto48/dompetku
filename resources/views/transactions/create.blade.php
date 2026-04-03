@extends('layouts.app')
@section('title', 'Add Transaction')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8" x-data="{
        type: '{{ old('type', $selectedAsset ? ($selectedAsset->type === 'mutual_fund' ? 'expense' : 'expense') : request('type', 'expense')) }}',
        amount: '{{ old('amount', request('amount')) }}',
        quantity: '{{ old('quantity') }}',
        assetId: @js($selectedAsset?->id),
    }" x-init="amount = DompetkuNumberFormat.formatNumber(amount)">
        <form method="POST" action="{{ route('transactions.store') }}" class="space-y-6">
            @csrf
            
            @if($selectedAsset)
            <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl flex items-center justify-between mb-2">
                <div>
                    <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">Repeat Order for Asset</p>
                    <p class="font-bold text-indigo-900">{{ $selectedAsset->name }} <span class="text-xs font-normal text-indigo-600">({{ $selectedAsset->platform }})</span></p>
                </div>
                <input type="hidden" name="asset_id" value="{{ $selectedAsset->id }}">
                <button type="button" @click="assetId = null; $el.closest('.bg-indigo-50').remove()" class="text-indigo-400 hover:text-indigo-600 transition text-xs font-bold uppercase">Cancel</button>
            </div>
            @endif

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
                    </div>
                </div>
                <x-form.money-input label="Amount" name="amount" model="amount" required placeholder="0.00" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <x-form.select label="Category" name="category_id" required>
                    @foreach($categories->groupBy('type') as $catType => $cats)
                        <optgroup label="{{ ucfirst($catType) }}">
                            @foreach($cats as $cat)
                                <option value="{{ $cat->id }}" {{ ($selectedAsset?->type === 'mutual_fund' && $cat->name === 'Investasi') || old('category_id', request('category_id')) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </x-form.select>

                <x-form.select label="Account" name="account_id" required>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('account_id', request('account_id')) == $acc->id ? 'selected' : '' }}>{{ $acc->name }} (Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                    @endforeach
                </x-form.select>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Date</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div x-show="assetId">
                    <x-form.number-input
                        label="Asset Quantity"
                        name="quantity"
                        model="quantity"
                        :required="false"
                        placeholder="0.0000"
                        x-bind:required="!!assetId"
                    />
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Description</label>
                <textarea name="description" rows="2" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional description...">{{ old('description', request('description', $selectedAsset ? 'Repeat order for ' . $selectedAsset->name : '')) }}</textarea>
            </div>

            <x-ui.errors />

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-6 py-3 bg-slate-900 text-white text-sm font-bold rounded-xl hover:bg-slate-800 transition shadow-sm shadow-slate-200 uppercase tracking-widest">Save Transaction</button>
                <a href="{{ route('transactions.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition uppercase tracking-widest">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
