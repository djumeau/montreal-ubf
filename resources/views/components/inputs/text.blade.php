@props([
    'id',
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'bag' => 'default', // Named error bag (e.g. validateWithBag('createSeries', ...))
    'model' => null, // Optional AlpineJS x-model binding for the input
])

@php
    $fieldErrors = $errors->getBag($bag);
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>

    @if( $label )
    <label class="block text-sm font-medium text-slate-100 mb-1.5" for="{{ $id }}">{{ $label }}</label>
    @endif

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if ($model) x-model="{{ $model }}" @endif
        class="w-full shadow appearance-none border rounded-sm p-2 focus:outline-none focus:shadow-outline text-sm {{ $fieldErrors->has($name) ? 'border-red-500' : 'border-slate-300' }}"
    />

    @if ($fieldErrors->has($name))
        <p class="text-xs text-red-500 mt-1">{{ $fieldErrors->first($name) }}</p>
    @endif

</div>
