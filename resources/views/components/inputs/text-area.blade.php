@props(['id', 'name', 'label' => null, 'value' => '', 'placeholder' => ''])

<div class="mb-4">

    @if ($label)
        <label class="block text-sm font-medium text-slate-700 mb-1.5"
            for="{{ $id }}">{{ $label }}</label>
    @endif

    <textarea cols="30" rows="7" id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}"
        class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline @error($name) border-red-500 @else border-slate-300 @enderror text-sm">
        {{ old($name, $value) }}
    </textarea>

    @error($name)
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror

</div>
