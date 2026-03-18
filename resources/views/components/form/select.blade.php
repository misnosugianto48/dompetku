@props([
    'label',
    'name',
])

<div>
    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ $label }}</label>
    <select
        name="{{ $name }}"
        {{ $attributes->class('w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500') }}
    >
        {{ $slot }}
    </select>
</div>

