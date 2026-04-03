@extends('layouts.app')
@section('title', 'Edit Account')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
        <h3 class="font-semibold text-slate-800 mb-6 text-lg">Edit Account</h3>

        <form method="POST" action="{{ route('accounts.update', $account) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Account Name</label>
                    <input type="text" name="name" value="{{ old('name', $account->name) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Account Type</label>
                    <select name="type" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="bank" {{ old('type', $account->type) === 'bank' ? 'selected' : '' }}>Bank</option>
                        <option value="wallet" {{ old('type', $account->type) === 'wallet' ? 'selected' : '' }}>E-Wallet</option>
                        <option value="cash" {{ old('type', $account->type) === 'cash' ? 'selected' : '' }}>Cash</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Current Balance</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-slate-400 font-medium">Rp</span>
                        </div>
                        <input type="number" step="any" name="balance" value="{{ old('balance', $account->balance) }}" required class="w-full pl-12 pr-4 py-3 rounded-xl border-slate-200 text-slate-900 font-semibold focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <x-ui.errors />

            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition shadow-sm uppercase tracking-widest flex-1">Save Changes</button>
                <a href="{{ route('accounts.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition uppercase tracking-widest text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
