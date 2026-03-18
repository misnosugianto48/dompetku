@props([
    'errors' => $errors,
])

@if($errors->any())
    <div {{ $attributes->class('bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm transition-all animate-pulse') }}>
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

