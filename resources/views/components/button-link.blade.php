@props([
    'url' => '#',
    'active' => false,
    'icon' => null,
    'bgColor' => 'bg-blue-500',
    'hoverColor' => 'bg-blue-700',
    'textColor' => 'text-white',
    'block' => false,
])

@php
    $hoverColor = 'hover:' . $hoverColor;
@endphp

<!-- What is block [{{ $block }}] -->

<a href="{{ $url }}"
    class="{{ $block ? 'block' : 'flex-inline' }} items-center px-4 py-2 text-center {{ $textColor }} rounded {{ $bgColor }} {{ $hoverColor }}"
    {{ $active ? 'aria-disabled=true tabindex=-1' : '' }}>
    @if ($icon)
        <i class="fa fa-{{ $icon }} p-0 mr-1"></i>
    @endif
    <span>{{ $slot }}</span>
</a>
