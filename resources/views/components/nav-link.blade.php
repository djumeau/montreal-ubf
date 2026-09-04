@props([
    'url' => '/',
    'active' => false,
    'icon' => null,
    'isMobile' => false,
])

@if ($isMobile)

    <a  href="{{ $url }}"
        class="block px-4 hover:bg-blue-700 {{ $active ? 'text-gray-400' : 'text-white' }}">

        @if ($icon)
            <i class="fa fa-{{ $icon }} p-0 mr-1"></i>
        @endif

        <span class="hover:underline">{{ $slot }}</span>

    </a>
@else
    <a href="{{ $url }}"
        class="inline-flex justify-between items-center  {{ $active ? 'text-gray-400' : 'text-white' }}"
        {{ $active ? 'aria-disabled=true tabindex=-1' : '' }}>

        @if ($icon)
            <i class="fa fa-{{ $icon }} p-0 mr-1"></i>
        @endif

        <span class="hover:underline">{{ $slot }}</span>

    </a>
@endif
