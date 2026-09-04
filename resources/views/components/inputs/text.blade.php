@props(['id', 'name', 'label' => null, 'type' => 'text', 'value' => '', 'placeholder' => ''])

<div class="mb-4">

    @if( $label )
    <label class="block text-sm font-medium text-slate-700 mb-1.5" for="{{ $id }}">{{ $label }}</label>
    @endif

    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}"
        class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline @error($name) border-red-500 @else border-slate-300 @enderror text-sm">

    @error($name)
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror

</div>
