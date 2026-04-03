@extends('layouts.app')
@section('title', 'Budgets')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- List Budgets -->
    <div class="lg:col-span-2 space-y-4">
        @forelse($budgets as $budget)
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex justify-between items-start hover:shadow-md transition">
            <div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $budget->category->color }}"></div>
                    <h4 class="font-semibold text-slate-800 text-lg">{{ $budget->category->name }}</h4>
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-medium">{{ ucfirst($budget->period) }}</span>
                </div>
                <p class="text-2xl font-bold mt-2 text-slate-900">Rp {{ number_format($budget->amount, 0, ',', '.') }}</p>
            </div>
            
            <div class="flex flex-col gap-2 relative" x-data="{ openEdit: false }">
                <button @click="openEdit = !openEdit" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-slate-50 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
                
                <form action="{{ route('budgets.destroy', $budget) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { action: $el.action, message: 'Delete this budget limit?' })">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </form>

                <!-- Edit Modal -->
                <div x-show="openEdit" @click.away="openEdit = false" class="absolute top-12 right-0 bg-white p-5 rounded-2xl shadow-xl z-20 border border-slate-100 w-72" style="display: none;">
                    <h4 class="font-semibold text-slate-800 mb-3">Edit {{ $budget->category->name }} Budget</h4>
                    <form action="{{ route('budgets.update', $budget) }}" method="POST" class="space-y-4">
                        @csrf @method('PUT')
                        <input type="hidden" name="category_id" value="{{ $budget->category_id }}">
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Amount Limit (Rp)</label>
                            <input type="number" name="amount" value="{{ (int)$budget->amount }}" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500" required>
                            @error('category_id')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white p-10 rounded-2xl text-center shadow-sm border border-slate-100">
            <svg class="w-16 h-16 mx-auto text-slate-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" /></svg>
            <h3 class="text-lg font-semibold text-slate-800">No Budgets Defined</h3>
            <p class="text-slate-500 mt-1">Keep an eye on spending by defining limits for categories here.</p>
        </div>
        @endforelse
    </div>

    <!-- Create Form -->
    <div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 sticky top-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Set New Budget</h3>
            <form action="{{ route('budgets.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                    <select name="category_id" class="w-full rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Select Category...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Monthly Limit (Rp)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="amount" min="0" class="w-full pl-10 rounded-xl border-slate-200 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0" required>
                    </div>
                </div>

                <button type="submit" class="w-full mt-2 px-4 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm">Save Budget Limit</button>
            </form>
        </div>
    </div>
</div>
@endsection
