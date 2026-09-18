@props(['id', 'name', 'label' => null, 'value' => '', 'options' => []])

<div {{ $attributes->merge(['class' => 'w-full mb-4']) }}>

    @if ($label)
        <label class="block text-sm font-medium text-slate-100 mb-1.5"
            for="{{ $id }}">{{ $label }}</label>
    @endif

    <div class="relative shadow border rounded border-slate-300 {{ $errors->has($name) ? 'border-red-500' : '' }}">

        <select id="{{ $id }}" name="{{ $name }}"
            class="w-full appearance-none border-0 rounded py-2 pl-3 pr-8 leading-tight focus:outline-none text-sm bg-slate-900">

            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach

        </select>

        <i class="fa-solid fa-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-300 pointer-events-none"></i>

    </div>

    @error($name)
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror

</div>
