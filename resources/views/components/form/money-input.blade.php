@props([
    'label',
    'name',
    'prefix' => 'Rp',
    'model',
    'required' => false,
    'placeholder' => null,
])

<div {{ $attributes }}>
    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ $label }}</label>
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">{{ $prefix }}</span>
        <input
            type="text"
            x-model="{{ $model }}"
            @input="{{ $model }} = DompetkuNumberFormat.formatNumber($event.target.value)"
            @if($required) required @endif
            class="w-full pl-9 rounded-xl border-slate-200 text-sm font-semibold focus:ring-indigo-500 focus:border-indigo-500"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
        >
        <input type="hidden" name="{{ $name }}" :value="DompetkuNumberFormat.getRaw({{ $model }})">
    </div>
</div>

