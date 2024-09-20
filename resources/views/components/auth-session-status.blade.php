@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'mb-4 flex w-full bg-green-50 rounded px-6 py-3 border-b-2 border-green-400 text-green-600 animate-fadeIn']) }}>
        {{ $status }}
    </div>
@endif
