@props(['id', 'name', 'label' => null, 'value' => '', 'placeholder' => '', 'rows' => '7', 'cols' => '30'])

<div {{ $attributes->merge(['class' => 'w-full mb-4']) }}>

    @if ($label)
        <label class="block text-sm font-medium text-slate-100 mb-1.5"
            for="{{ $id }}">{{ $label }}</label>
    @endif

    <textarea
        cols="{{ $cols }}"
        rows="{{ $rows }}"
        id="{{ $id }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        class="w-full shadow appearance-none border rounded-sm py-2 px-3 leading-tight focus:outline-none focus:shadow-outline text-sm {{ $errors->has($name) ? 'border-red-500' : 'border-slate-300' }}"
    >{{ old($name, $value) }}</textarea>

    @error($name)
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror

</div>
