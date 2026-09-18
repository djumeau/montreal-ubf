@props([
    'id',
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
])

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
        class="w-full shadow appearance-none border rounded-sm p-2 focus:outline-none focus:shadow-outline text-sm {{ $errors->has($name) ? 'border-red-500' : 'border-slate-300' }}"
    />

    @error($name)
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror

</div>
