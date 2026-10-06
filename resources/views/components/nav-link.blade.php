{{-- Current page ("active"): dimmed and not clickable. opacity-100! cancels the .75 opacity app.css gives a[aria-disabled],
     which took the gray text to about 3.8:1 on the footer; gray-400 alone is about 5.7:1 there (WCAG AA needs 4.5:1) --}}
@props([
    'url' => '/',
    'active' => false,
    'icon' => null,
    'isMobile' => false,
])

@php
    // icon="cross" (items of the Info / Resources menus): a thin drawn cross, as Font Awesome's only comes in bold.
    // It is as tall as one line of text and the link's content starts at the top, so it stays next to the first line
    // when the title wraps
    $isCross = $icon === 'cross';
@endphp

@if ($isMobile)

    <a  href="{{ $url }}"
        class="{{ $isCross ? 'flex items-start' : 'block' }} p-3 hover:bg-blue-700 {{ $active ? 'text-gray-400 opacity-100!' : 'text-white' }}"
        {{ $active ? 'aria-disabled=true tabindex=-1' : '' }}>

        @if ($isCross)
            <svg viewBox="0 0 10 25" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"
                style="width: 0.6em; height: 1.5em; margin-right: 0.45em; flex-shrink: 0;"><path d="M5 6v13M1.5 10h7" /></svg>
        @elseif ($icon)
            <i class="fa fa-{{ $icon }} p-0 mr-1"></i>
        @endif

        <span class="hover:underline">{{ $slot }}</span>

    </a>
@else
    <a href="{{ $url }}"
        class="inline-flex justify-between {{ $isCross ? 'items-start' : 'items-center' }}  {{ $active ? 'text-gray-400 opacity-100!' : 'text-white' }}"
        {{ $active ? 'aria-disabled=true tabindex=-1' : '' }}>

        @if ($isCross)
            <svg viewBox="0 0 10 25" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"
                style="width: 0.6em; height: 1.5em; margin-right: 0.45em; flex-shrink: 0;"><path d="M5 6v13M1.5 10h7" /></svg>
        @elseif ($icon)
            <i class="fa fa-{{ $icon }} p-0 mr-1"></i>
        @endif

        <span class="hover:underline">{{ $slot }}</span>

    </a>
@endif
