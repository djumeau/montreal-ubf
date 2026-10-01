{{-- Current page ("active"): dimmed and not clickable. opacity-100! cancels the .75 opacity app.css gives a[aria-disabled],
     which took the gray text to about 3.8:1 on the footer; gray-400 alone is about 5.7:1 there (WCAG AA needs 4.5:1) --}}
@props([
    'url' => '/',
    'active' => false,
    'icon' => null,
    'isMobile' => false,
])

@if ($isMobile)

    <a  href="{{ $url }}"
        class="block p-3 hover:bg-blue-700 {{ $active ? 'text-gray-400 opacity-100!' : 'text-white' }}"
        {{ $active ? 'aria-disabled=true tabindex=-1' : '' }}>

        @if ($icon)
            <i class="fa fa-{{ $icon }} p-0 mr-1"></i>
        @endif

        <span class="hover:underline">{{ $slot }}</span>

    </a>
@else
    <a href="{{ $url }}"
        class="inline-flex justify-between items-center  {{ $active ? 'text-gray-400 opacity-100!' : 'text-white' }}"
        {{ $active ? 'aria-disabled=true tabindex=-1' : '' }}>

        @if ($icon)
            <i class="fa fa-{{ $icon }} p-0 mr-1"></i>
        @endif

        <span class="hover:underline">{{ $slot }}</span>

    </a>
@endif
