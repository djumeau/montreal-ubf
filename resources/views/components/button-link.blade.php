@props([
    'url' => '#',
    'active' => false,
    'icon' => null,
    'bgColor' => 'blue',
    'hoverColor' => 'blue',
    'textColor' => 'white',
])

@php
    $bgColor = $bgColor ? 'bg-' . $bgColor . '-500' : 'bg-blue-500';
    $hoverColor = $hoverColor ? 'hover:bg-' . $hoverColor . '-700' : 'hover:bg-blue-700';
    $textColor = $textColor ? 'text-' . $textColor : 'text-white';
@endphp

<a href="{{ url($url) }}"
    class='inline-flex items-center px-4 py-2 text-center {{ $textColor }} rounded {{ $bgColor }} {{ $hoverColor }}'
    {{ $active ? 'aria-disabled=true tabindex=-1' : '' }}>
    @if ($icon)
        <i class="fa fa-{{ $icon }} p-0 mr-1"></i>
    @endif
    <span>{{ $slot }}</span>
</a>
