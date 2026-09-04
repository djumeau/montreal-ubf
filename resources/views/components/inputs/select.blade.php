@props(['id', 'name', 'label' => null, 'value' => '', 'options' => []])

<div class="mb-4">

    @if ($label)
        <label class="block text-sm font-medium text-slate-700 mb-1.5"
            for="{{ $id }}">{{ $label }}</label>
    @endif

    <select id="{{ $id }}" name="{{ $name }}"
        class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline @error($name) border-red-500 @else border-slate-300 @enderror text-sm">

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach

    </select>

    @error($name)
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror

</div>
