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
</div>
@endsection
