@props(['id', 'name', 'label' => null, 'type' => 'text', 'value' => null, 'placeholder' => null, 'required' => false]));

<div>

    @if($label)
    <label class="block text-sm font-medium text-slate-700 mb-1.5" for="{{ $id }}">{{ $label }}</label>
    @endif

    <input id={{ $id }} type="{{ $type }}" name="{{ $name }}" value="{{ old('{{ $name }}') }}"
        class="w-full px-3 py-2 bg-white border rounded-lg @error({{ $name }}) border-red-500 @else border-slate-300 @enderror focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">

    @error('{{ $name }}')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror

</div>
