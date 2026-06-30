@props([
    'image' => './images/montreal_skyline-mobile.jpg',
    'title' => '',
    'dates' => '16 juillet au 19 juillet 2026',
    'location' => 'CEGEP John Abbott - Ste-Anne-de-Bellevue, QC',
    'href' => null,
]) <!-- Events Section -->

<section {{ $attributes->merge(['class' => 'relative bg-cover bg-center bg-no-repeat h-80']) }}
    style="background-image: url('{{ asset($image) }}')">

    <div class='absolute top-2 left-3 md:top-3 text-white drop-shadow-lg'>

        <h2 @class(['hidden' => !isset($title),
                   'text-sm italic md:text-lg'
                    ])>{{ $title }}</h2>
       
       <p class='font-semibold text-base md:text-xl'>{{ $slot }}</p>
        <div>
            <i class="fa-solid fa-calendar" aria-hidden="true"></i>
            <span class="text-sm md:text-base"> {{ $dates }}</span>
        </div>
        <div>
            <i class="fa-solid fa-map-marker" aria-hidden="true"></i>
            <span class="text-sm md:text-base"> {{ $location }}</span>
        </div>
    </div>

    @if(!@empty($href))

    <a href='{{$href}}'
        class='absolute bottom-3 right-3 md:bottom-6 md:right-6
            px-3 py-2 md:px-6 md:py-3
            bg-purple-700 hover:bg-purple-800
            text-sm md:text-base
            text-white rounded-md'>
        {{ __('home/events.more_info') }}
    </a>

    @endif

</section>
