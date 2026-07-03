@extends('layouts.app')
@section('title', 'Edit Savings Goal')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
        <h3 class="font-semibold text-slate-800 mb-6 text-lg">Edit Savings Goal</h3>

        <form method="POST" action="{{ route('savings-goals.update', $savingsGoal) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Goal Name</label>
                    <input type="text" name="name" value="{{ old('name', $savingsGoal->name) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Target Amount (Rp)</label>
                        <input type="number" name="target_amount" value="{{ old('target_amount', $savingsGoal->target_amount) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Current Amount (Rp)</label>
                        <input type="number" name="current_amount" value="{{ old('current_amount', $savingsGoal->current_amount) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deadline</label>
                    <input type="date" name="deadline" value="{{ old('deadline', $savingsGoal->deadline ? $savingsGoal->deadline->format('Y-m-d') : '') }}" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status</label>
                    <select name="status" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="active" {{ old('status', $savingsGoal->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status', $savingsGoal->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>

            <x-ui.errors />

            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition shadow-sm uppercase tracking-widest flex-1">Save Changes</button>
                <a href="{{ route('savings-goals.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition uppercase tracking-widest text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
