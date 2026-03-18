@props([
    'label',
    'name',
    'model',
    'required' => false,
    'placeholder' => null,
])

<div {{ $attributes }}>
    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">{{ $label }}</label>
    <div class="relative">
        <input
            type="text"
            x-model="{{ $model }}"
            @input="{{ $model }} = DompetkuNumberFormat.formatNumber($event.target.value)"
            @if($required) required @endif
            class="w-full rounded-xl border-slate-200 text-sm focus:ring-indigo-500 focus:border-indigo-500"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
        >
        <input type="hidden" name="{{ $name }}" :value="DompetkuNumberFormat.getRaw({{ $model }})">
    </div>
</div>

