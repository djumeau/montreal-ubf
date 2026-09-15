@props([
    'id',
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'width' => '80',
    'fit' => false,
])

<div>

    @if( $label )
    <label class="block text-sm font-medium text-slate-700 mb-1.5" for="{{ $id }}">{{ $label }}</label>
    @endif

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"

        class="w-{{ $width }} shadow appearance-none border rounded-sm p-2 focus:outline-none focus:shadow-outline border-slate-30 @error($name) border-red-500 @enderror text-sm" />

    @error($name)
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror

</div>
