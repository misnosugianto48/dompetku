@extends('layouts.app')
@section('title', 'Edit Transaction')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8" x-data="{
        type: '{{ old('type', $transaction->type) }}',
        amount: '{{ old('amount', $transaction->amount) }}',
    }" x-init="amount = DompetkuNumberFormat.formatNumber(amount)">
        <form method="POST" action="{{ route('transactions.update', $transaction) }}" enctype="multipart/form-data" class="space-y-6">
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

            @if($savingsGoals->isNotEmpty())
            <div>
                <x-form.select label="Link to Savings Goal" name="savings_goal_id">
                    <option value="">None (General Transaction)</option>
                    @foreach($savingsGoals as $goal)
                    <option value="{{ $goal->id }}" {{ old('savings_goal_id', $transaction->savings_goal_id) == $goal->id ? 'selected' : '' }}>
                        {{ $goal->name }} (Rp {{ number_format($goal->current_amount, 0, ',', '.') }} / Rp {{ number_format($goal->target_amount, 0, ',', '.') }})
                    </option>
                    @endforeach
                </x-form.select>
            </div>
            @endif

            @if($tags->isNotEmpty())
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tags</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition text-sm">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $transaction->tags->pluck('id')->toArray())) ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $tag->color ?? '#cbd5e1' }}"></span>
                            #{{ $tag->name }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Description</label>
                <textarea name="description" rows="2" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $transaction->description) }}</textarea>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Notes</label>
                <textarea name="notes" rows="2" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes', $transaction->notes) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Receipt / Attachment</label>
                @if($transaction->receipt_path)
                <div class="mb-3 p-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if(Str::endsWith($transaction->receipt_path, '.pdf'))
                            <svg class="w-10 h-10 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        @else
                            <img src="{{ $transaction->receipt_url }}" class="w-10 h-10 object-cover rounded-lg border border-slate-200" alt="Receipt">
                        @endif
                        <div>
                            <p class="text-xs font-bold text-slate-700">Current Receipt</p>
                            <a href="{{ $transaction->receipt_url }}" target="_blank" class="text-[11px] text-indigo-600 hover:underline">View Attachment &rarr;</a>
                        </div>
                    </div>
                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-rose-100 bg-rose-50 text-rose-700 text-xs font-semibold cursor-pointer hover:bg-rose-100 transition">
                        <input type="checkbox" name="delete_receipt" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                        <span>Delete current receipt</span>
                    </label>
                </div>
                @endif

                <div class="relative border-2 border-dashed border-slate-200 rounded-xl p-4 text-center hover:bg-slate-50 transition cursor-pointer" x-data="{ fileName: '' }">
                    <input type="file" name="receipt" accept="image/*,application/pdf" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                    <div class="flex flex-col items-center justify-center space-y-1">
                        <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        <span class="text-xs font-semibold text-slate-600" x-text="fileName || 'Upload new receipt (PDF or Image, max 5MB)'">Upload new receipt (PDF or Image, max 5MB)</span>
                    </div>
                </div>
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
