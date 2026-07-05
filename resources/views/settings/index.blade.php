@extends('layouts.app')
@section('title', 'Application Settings')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] border border-slate-100">
        <h2 class="text-xl font-bold text-slate-800 mb-6">Financial Reporting Engine</h2>
        <p class="text-sm text-slate-500 mb-8 border-b border-slate-100 pb-6">Configure how often Dompetku parses your transactional history dynamically pushing financial breakdowns towards your exact setup.</p>
        
        <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Enable Reports Toggle -->
            <div class="flex items-center justify-between p-5 rounded-2xl bg-indigo-50/50 border border-indigo-100/50">
                <div>
                    <h4 class="font-bold text-indigo-900">Automated Mail Distributions</h4>
                    <p class="text-sm font-medium text-slate-500 mt-1">Receive high-level PDF performance exports natively.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="reports_enabled" value="1" class="sr-only peer" {{ $reportsEnabled == '1' ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>

            <!-- Send Date -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 mt-4">Automated Dispatch Target Schedule</label>
                <select name="reports_send_date" class="w-full rounded-2xl border-slate-200 text-sm font-medium focus:ring-indigo-500 py-3 bg-white">
                    <option value="end_of_month" {{ $reportsSendDate === 'end_of_month' ? 'selected' : '' }}>Last Day of the Month (Evaluation)</option>
                    <option value="start_of_month" {{ $reportsSendDate === 'start_of_month' ? 'selected' : '' }}>First Day of the Month (Summarization)</option>
                </select>
                <p class="text-xs font-semibold text-slate-400 mt-3">Emails will securely distribute towards <span class="font-bold text-indigo-600">{{ auth()->user()->email }}</span>.</p>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-3.5 bg-indigo-600 text-white font-bold text-sm tracking-wide uppercase rounded-xl shadow-lg shadow-indigo-200 hover:shadow-indigo-300 hover:bg-indigo-700 transition">Save Configurations</button>
            </div>
        </form>
    </div>

    <!-- Exchange Rates Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] border border-slate-100 mt-8">
        <h2 class="text-xl font-bold text-slate-800 mb-2">Exchange Rates Configuration</h2>
        <p class="text-sm text-slate-500 mb-6 border-b border-slate-100 pb-4">Manage exchange rates relative to IDR (our base currency). Values dictate currency conversion conversions across multi-currency accounts.</p>

        <!-- Current Rates List -->
        <div class="space-y-3 mb-6">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Conversions</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 dark:bg-slate-800/20 dark:border-slate-800/50">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 text-xs font-bold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded">IDR</span>
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400">Base Currency</span>
                    </div>
                    <span class="text-sm font-bold text-slate-800 dark:text-white">1.0000 IDR</span>
                </div>
                @foreach($exchangeRates as $rate)
                <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-100 dark:bg-slate-800/20 dark:border-slate-800/50">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 text-xs font-bold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded">{{ $rate->currency }}</span>
                        <span class="text-sm font-semibold text-slate-600 dark:text-slate-400">1 {{ $rate->currency }} =</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-slate-800 dark:text-white">{{ number_format($rate->rate, 4, '.', ',') }} IDR</span>
                        <form action="{{ route('settings.exchange-rates.delete', $rate) }}" method="POST" onsubmit="return confirm('Delete exchange rate for {{ $rate->currency }}?');" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-rose-500 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Add/Edit Form -->
        <form action="{{ route('settings.exchange-rates.update') }}" method="POST" class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row gap-3">
            @csrf
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input type="text" name="currency" required placeholder="Currency Code (e.g. USD)" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 py-3 bg-white" minlength="3" maxlength="3">
                <input type="number" step="any" name="rate" required placeholder="Rate in IDR (e.g. 15000)" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 py-3 bg-white" min="0.0001">
            </div>
            <button type="submit" class="px-5 py-3 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm tracking-wide uppercase rounded-xl transition whitespace-nowrap">
                Add / Update Rate
            </button>
        </form>
    </div>

    <!-- Danger Zone Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.05)] border border-rose-100/50 mt-8">
        <h2 class="text-xl font-bold text-rose-800 mb-6 flex items-center gap-2">
            <svg class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            Danger Zone
        </h2>
        <p class="text-sm text-slate-500 mb-8 border-b border-slate-100 pb-6">Perform destructive actions on your account database. These operations cannot be undone, please proceed with caution.</p>
        
        <div class="p-5 rounded-2xl bg-rose-50/50 border border-rose-100/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1">
                <h4 class="font-bold text-rose-900">Reset Application Database</h4>
                <p class="text-sm font-medium text-slate-500 mt-1">Clears all transaction logs, asset holdings, budgets, custom accounts, categories, and tags. Restores default accounts and categories.</p>
            </div>
            <form action="{{ route('settings.clear-data') }}" method="POST" onsubmit="return confirm('WARNING: This will delete ALL transactions, assets, budgets, custom accounts, categories, and tags. Default categories/accounts will be re-seeded. This cannot be undone. Are you absolutely sure?');">
                @csrf
                <button type="submit" class="w-full sm:w-auto px-5 py-3 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-sm uppercase tracking-wide rounded-xl shadow-lg shadow-rose-200 transition whitespace-nowrap">
                    Clear All Data
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
