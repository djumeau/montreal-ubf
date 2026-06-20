@props([
    'url' => '#',
    'active' => false,
    'icon' => null,
    'bgColor' => 'bg-red-500',
    'hoverColor' => 'bg-red-700',
    'textColor' => 'text-white',
    'block' => false,
])

@php
    $hoverColor = 'hover:' . $hoverColor;
@endphp

<!-- What is hoverColor [{{ $hoverColor }}] -->

<a href="{{ $url }}"
    class="{{ $block ? 'block' : 'flex-inline' }} items-center px-2 py-2 text-center {{ $textColor }} {{ $bgColor }} {{ $hoverColor }} rounded"
    {{ $active ? 'aria-disabled=true tabindex=-1' : '' }}>
    @if ($icon)
        <i class="fa fa-{{ $icon }}"></i>
    @endif
    <span class="mr-1">{{ $slot }}</span>
</a>
