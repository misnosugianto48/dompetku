@extends('layouts.app')
@section('title', 'Savings Goals')

@section('content')
<div class="space-y-6">
    <!-- Add Savings Goal Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6" x-data="{ open: false }">
        <div class="flex justify-between items-center">
            <h3 class="font-semibold text-slate-800">Your Savings Goals</h3>
            <button @click="open = !open" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span class="hidden sm:inline">Add Savings Goal</span>
            </button>
        </div>

        <form method="POST" action="{{ route('savings-goals.store') }}" x-show="open" x-transition class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Goal Name</label>
                <input type="text" name="name" required placeholder="e.g. New Macbook" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Target Amount (Rp)</label>
                <input type="number" name="target_amount" required placeholder="0.00" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Initial Amount (Rp)</label>
                <input type="number" name="current_amount" value="0" placeholder="0.00" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deadline (Optional)</label>
                <input type="date" name="deadline" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <button type="submit" class="w-full px-4 py-2.5 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-900 transition">Save Goal</button>
        </form>
    </div>

    <!-- Savings Goal Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($savingsGoals as $goal)
        @php
            $percentage = $goal->target_amount > 0 ? min(100, round(($goal->current_amount / $goal->target_amount) * 100)) : 0;
        @endphp
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-md transition flex flex-col justify-between h-56 relative overflow-hidden">
            @if($goal->status === 'completed')
            <div class="absolute top-0 right-0 bg-emerald-500 text-white text-[9px] font-bold px-3 py-1 uppercase tracking-widest rounded-bl-xl">Completed</div>
            @endif
            
            <div>
                <div class="flex justify-between items-start mb-2">
                    <h4 class="font-bold text-slate-800 text-lg leading-snug">{{ $goal->name }}</h4>
                    <span class="text-xs font-semibold text-slate-500 bg-slate-50 border border-slate-100 px-2 py-0.5 rounded-lg">
                        {{ $goal->status }}
                    </span>
                </div>
                
                @if($goal->deadline)
                <p class="text-xs text-slate-400 flex items-center gap-1 mb-4">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Target by {{ $goal->deadline->format('d M Y') }}
                </p>
                @else
                <p class="text-xs text-slate-400 flex items-center gap-1 mb-4">No deadline set</p>
                @endif
            </div>

            <div class="space-y-2">
                <div class="flex justify-between items-end text-xs">
                    <span class="font-bold text-slate-700">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</span>
                    <span class="text-slate-400">of Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</span>
                </div>
                
                <!-- Progress Bar -->
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 {{ $goal->status === 'completed' ? 'bg-emerald-500' : 'bg-indigo-600' }}" style="width: {{ $percentage }}%"></div>
                </div>
                
                <div class="flex justify-between items-center text-xs pt-1">
                    <span class="font-semibold text-indigo-600">{{ $percentage }}% achieved</span>
                    
                    <div class="flex items-center gap-3">
                        <a href="{{ route('savings-goals.edit', $goal) }}" class="text-slate-300 hover:text-indigo-600 transition" title="Edit">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </a>
                        <form action="{{ route('savings-goals.destroy', $goal) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { action: $el.action, message: 'Are you sure you want to delete this savings goal?' })">
                            @csrf @method('DELETE')
                            <button class="text-slate-300 hover:text-rose-600 transition" title="Delete">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white p-12 text-center text-slate-400 rounded-3xl border border-slate-100 italic">
            No savings goals set yet. Set your first financial milestone above!
        </div>
        @endforelse
    </div>
</div>
@endsection
