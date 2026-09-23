@props([
    'id',
    'name',
    'label' => null,
    'accept' => null,
    'bag' => 'default', // Named error bag (e.g. validateWithBag('createSeries', ...))
    'current' => null, // Optional AlpineJS expression for the current file name (file inputs can't be pre-filled)
])

@php
    $fieldErrors = $errors->getBag($bag);
@endphp

<div {{ $attributes->merge(['class' => 'mb-4']) }} x-data="{ fileName: '' }">

    @if ($label)
        <label class="block text-sm font-medium text-slate-100 mb-1.5"
            for="{{ $id }}">{{ $label }}</label>
    @endif

    <!-- The browser's own "Choose file / No file chosen" text ignores the page locale,
         so the native input is visually hidden and this translated label stands in for it -->
    <label
        class="flex items-center gap-3 w-full border rounded py-2 px-3 text-sm cursor-pointer hover:bg-slate-700/50 focus-within:ring-2 focus-within:ring-sky-500 {{ $fieldErrors->has($name) ? 'border-red-500' : 'border-slate-300' }}">

        <input id="{{ $id }}" name="{{ $name }}" type="file" @if ($accept) accept="{{ $accept }}" @endif
            class="sr-only" @change="fileName = $event.target.files[0]?.name ?? ''" />

        <span class="inline-flex items-center gap-2 px-2 py-1 bg-sky-900 rounded-sm text-white whitespace-nowrap">
            <i class="fas fa-upload"></i>{{ __('Choose file') }}
        </span>

        <!-- Newly chosen file, else the current file, else "No file chosen" -->
        <span class="truncate text-slate-300"
            x-text="fileName || {{ $current ? "($current) || " : '' }}@js(__('No file chosen'))">{{ __('No file chosen') }}</span>
    </label>

    @if ($fieldErrors->has($name))
        <p class="text-xs text-red-500 mt-1">{{ $fieldErrors->first($name) }}</p>
    @endif

</div>
