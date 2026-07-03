@extends('layouts.app')
@section('title', 'Edit Tag')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
        <h3 class="font-semibold text-slate-800 mb-6 text-lg">Edit Tag</h3>

        <form method="POST" action="{{ route('tags.update', $tag) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tag Name</label>
                    <input type="text" name="name" value="{{ old('name', $tag->name) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" value="{{ old('color', $tag->color ?? '#6366f1') }}" class="w-10 h-10 cursor-pointer rounded-xl border-none p-0 bg-transparent">
                        <span class="text-sm text-slate-500">Select a color to easily identify this tag</span>
                    </div>
                </div>
            </div>

            <x-ui.errors />

            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition shadow-sm uppercase tracking-widest flex-1">Save Changes</button>
                <a href="{{ route('tags.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition uppercase tracking-widest text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
