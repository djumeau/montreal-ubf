@props([
    'id',
    'name',
    'label' => null,
    'value' => '',
    'options' => [], // value => label, or group label => [value => label] for an <optgroup>
    'disabled' => [], // Option values shown greyed out and not selectable
    'bag' => 'default', // Named error bag (e.g. validateWithBag('createSeries', ...))
    'model' => null, // Optional AlpineJS x-model binding for the select
])

@php
    $fieldErrors = $errors->getBag($bag);
@endphp

<div {{ $attributes->merge(['class' => 'w-full mb-4']) }}>

    @if ($label)
        <label class="block text-sm font-medium text-slate-100 mb-1.5"
            for="{{ $id }}">{{ $label }}</label>
    @endif

    <div class="relative shadow border rounded {{ $fieldErrors->has($name) ? 'border-red-500' : 'border-slate-300' }}">

        <select id="{{ $id }}" name="{{ $name }}" @if ($model) x-model="{{ $model }}" @endif
            class="w-full appearance-none border-0 rounded py-2 pl-3 pr-8 leading-tight focus:outline-none text-sm bg-slate-900">

            @foreach ($options as $optionValue => $optionLabel)
                @if (is_array($optionLabel))
                    <optgroup label="{{ $optionValue }}">
                        @foreach ($optionLabel as $groupValue => $groupLabel)
                            <option value="{{ $groupValue }}" {{ old($name, $value) == $groupValue ? 'selected' : '' }}
                                @if (in_array($groupValue, $disabled)) disabled class="text-slate-500" @endif>
                                {{ $groupLabel }}
                            </option>
                        @endforeach
                    </optgroup>
                @else
                    <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}
                        @if (in_array($optionValue, $disabled)) disabled class="text-slate-500" @endif>
                        {{ $optionLabel }}
                    </option>
                @endif
            @endforeach

        </select>

        <i class="fa-solid fa-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-300 pointer-events-none"></i>

    </div>

    @if ($fieldErrors->has($name))
        <p class="text-xs text-red-500 mt-1">{{ $fieldErrors->first($name) }}</p>
    @endif

</div>
