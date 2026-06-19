@props([
    'url' => '#',
    'type' => 'primary',
    'size' => 'md',
    'icon' => null,
    'disabled' => false,
    'bgColor' => 'blue',
    'hoverColor' => 'blue',
])

@php
    $bgColor = $bgColor ? 'bg-' . $bgColor . '-500' : 'bg-blue-500';
    $hoverColor = $hoverColor ? 'hover:bg-' . $hoverColor . '-700' : 'hover:bg-blue-700';
@endphp

<a href="{{ $url ?? '#' }}"
    class="{{ $type }} inline-flex justify-center items-center font-semibold rounded {{ $bgColor }} {{ $hoverColor }} {{ $size === 'sm' ? 'px-3 py-1 text-sm' : 'px-4 py-2' }} {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}"
    {{ $disabled ? 'aria-disabled=true tabindex=-1' : '' }}>

    @if ($icon)
        <i class="fa fa-{{ $icon }} p-0 mr-1"></i>
    @endif

    <span>{{ $slot }}</span>
</a>
