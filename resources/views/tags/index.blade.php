@extends('layouts.app')
@section('title', 'Tags')

@section('content')
<div class="space-y-6">
    <!-- Add Tag Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6" x-data="{ open: false }">
        <div class="flex justify-between items-center">
            <h3 class="font-semibold text-slate-800">Your Tags</h3>
            <button @click="open = !open" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span class="hidden sm:inline">Add Tag</span>
            </button>
        </div>

        <form method="POST" action="{{ route('tags.store') }}" x-show="open" x-transition class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
            @csrf
            <input type="text" name="name" required placeholder="Tag name" class="rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            
            <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden pr-2">
                <span class="text-sm text-slate-500 px-3 border-r border-slate-200 bg-slate-50 h-full flex items-center">Color</span>
                <input type="color" name="color" value="#6366f1" class="ml-2 w-full h-8 cursor-pointer rounded border-none p-0 bg-transparent focus:ring-0">
            </div>
            
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-900 transition">Save Tag</button>
        </form>
    </div>

    <!-- Tag Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($tags as $tag)
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center gap-2">
                        @if($tag->color)
                        <div class="w-3.5 h-3.5 rounded-full" style="background-color: {{ $tag->color }};"></div>
                        @else
                        <div class="w-3.5 h-3.5 rounded-full bg-slate-200"></div>
                        @endif
                        <h4 class="font-semibold text-slate-800">#{{ $tag->name }}</h4>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">{{ $tag->transactions_count }} transactions</p>
                </div>
                
                <div class="flex flex-col items-end gap-2">
                    <a href="{{ route('tags.edit', $tag) }}" class="text-slate-300 hover:text-indigo-500 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </a>
                    <form action="{{ route('tags.destroy', $tag) }}" method="POST" x-data @submit.prevent="$dispatch('open-confirm-modal', { action: $el.action, message: 'Are you sure you want to delete this tag? The transactions themselves will not be deleted.' })">
                        @csrf @method('DELETE')
                        <button class="text-slate-300 hover:text-rose-500 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white p-12 text-center text-slate-400 rounded-2xl border border-slate-100 italic">
            No tags created yet.
        </div>
        @endforelse
    </div>
</div>
@endsection
