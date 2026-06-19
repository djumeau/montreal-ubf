@props(['url' => '/', 'active' => false, 'icon' => null])

<a href="{{ url($url) }}"
    class="inline-flex justify-between items-center text-white py-2 {{ $active ? 'font-bold' : '' }}"
    {{ $active ? 'aria-disabled=true tabindex=-1' : '' }}>

    @if ($icon)
        <i class="fa fa-{{ $icon }} p-0 mr-1"></i>
    @endif

    <span class="hover:underline">{{ $slot }}</span>

</a>
