@extends('layouts.app')
@section('title', 'Edit Category')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
        <h3 class="font-semibold text-slate-800 mb-6 text-lg">Edit Category</h3>

        <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Category Name</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Type</label>
                    <select name="type" required class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="expense" {{ old('type', $category->type) === 'expense' ? 'selected' : '' }}>Expense</option>
                        <option value="income" {{ old('type', $category->type) === 'income' ? 'selected' : '' }}>Income</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="color" value="{{ old('color', $category->color ?? '#cbd5e1') }}" class="w-10 h-10 cursor-pointer rounded-xl border-none p-0 bg-transparent">
                        <span class="text-sm text-slate-500">Select a color to easily identify this category</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Icon Class (Optional)</label>
                    <input type="text" name="icon" value="{{ old('icon', $category->icon) }}" placeholder="e.g. mbdi mbdi-home" class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <x-ui.errors />

            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition shadow-sm uppercase tracking-widest flex-1">Save Changes</button>
                <a href="{{ route('categories.index') }}" class="px-6 py-3 bg-slate-100 text-slate-600 text-sm font-bold rounded-xl hover:bg-slate-200 transition uppercase tracking-widest text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
