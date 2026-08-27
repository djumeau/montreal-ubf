@props([
    'toggleLeft' => true,
    'img' => null,
    'alt' => 'Sample text',
    'imageSize' => '100',
])

@php
    // Map human-readable percentage numbers cleanly to Tailwind max-width restrictions
    $sizeClasses = [
        '25' => 'max-w-[25%]',
        '33' => 'max-w-[33.333333%]',
        '50' => 'max-w-[50%]',
        '66' => 'max-w-[66.666667%]',
        '70' => 'max-w-[70%]',
        '75' => 'max-w-[75%]',
        '80' => 'max-w-[80%]',
        '90' => 'max-w-[90%]',
        '100' => 'max-w-full',
    ];

    // Fallback gracefully to full width if an invalid size is passed
    $selectedSizeClass = $sizeClasses[$imageSize] ?? 'max-w-full';
@endphp

{{-- md:max-w-[80%] limits desktop width to 80%. mx-auto centers it. gap-4 tightens the spacing. --}}

<div class="grid grid-cols-1 md:grid-cols-2 w-full {{ $selectedSizeClass }} mx-auto p-4">

    {{-- Image Container --}}
    <div class="{{ $toggleLeft ? 'md:order-first' : 'md:order-last' }} flex items-center justify-center mb-2">
        @if ($img)
            <img src="{{ asset($img) }}"
                class="h-auto rounded-md shadow-md object-cover pointer-events-none select-none {{ $selectedSizeClass }} mb-2 md:mb-0"
                role="img" @if (empty($alt)) aria-hidden="true" @endif>
        @endif
    </div>

    {{-- Text/Slot Container --}}
    <div class="prose max-w-none flex flex-col justify-center">
        {{ $slot }}
    </div>

</div>
