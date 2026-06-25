@props([
    'image' => './images/montreal_skyline-mobile.jpg',
    'title' => 'Événements',
    'startDate' => '16 juillet',
    'endDate' => '19 juillet',
]) <!-- Events Section -->
<section id='{{ $title }}' {{ $attributes->merge(['class' => 'relative bg-cover bg-center bg-no-repeat h-80']) }}
    style="background-image: url('{{ asset($image) }}')">


    <div class='absolute top-2 left-3 md:top-3 md:left-4
       text-white
        drop-shadow-lg'>
        <h2 class='text-sm md:text-lg'>{{ $title }}</h2>
        <p class='font-semibold text-base md:text-xl'>{{ $slot }}</p>
        <div>
            <i class="fa-solid fa-calendar" aria-hidden="true"></i>
            <span class="date-text">{{ $startDate }}</span> au
            <span class="date-text">{{ $endDate }}</span>
        </div>
    </div>

    <a href='#more-info'
        class='absolute bottom-3 right-3 md:bottom-6 md:right-6
            px-3 py-2 md:px-6 md:py-3
            bg-purple-700 hover:bg-purple-800
            text-sm md:text-base
            text-white rounded-md'>
        Plus d'information
    </a>

</section>
