@props(['url' => '/', 'active' => false, 'icon' => null, 'isMobile' => false])

@if ($isMobile)

    <a href="{{ $url }}" class="block px-4 py-2 hover:bg-blue-700 {{ $active ? 'font-bold' : '' }}">

        @if ($icon)
            <i class="fa fa-{{ $icon }} p-0 mr-1"></i>
        @endif

        <span class="hover:underline">{{ $slot }}</span>

    </a>
@else
    <a href="{{ $url }}"
        class="inline-flex justify-between items-center text-white {{ $active ? 'font-bold' : '' }}"
        {{ $active ? 'aria-disabled=true tabindex=-1' : '' }}>

        @if ($icon)
            <i class="fa fa-{{ $icon }} p-0 mr-1"></i>
        @endif

        <span class="hover:underline">{{ $slot }}</span>

    </a>
@endif
