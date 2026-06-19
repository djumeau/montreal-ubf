@props(['url' => '/', 'active' => false])

<a href="{{ url($url) }}" class="text-white hover:underline py-2 {{ $active ? 'font-bold' : '' }}"
    {{ $active ? 'aria-disabled=true tabindex=-1' : '' }}>
    {{ $slot }}
</a>
