@props([
    'image' => './images/montreal_skyline-mobile.jpg',
    'title' => '',
    'href' => null,
])

<!-- Bible Study Section -->
<section {{ $attributes->merge(['class' => 'relative bg-cover bg-center bg-no-repeat h-80']) }}
    style="background-image: url('{{ asset($image) }}')">

    <div class='absolute top-2 left-3 md:top-3 text-white drop-shadow-lg'>

        <h2 @class(['hidden' => !isset($title), 'text-sm italic md:text-lg'])>{{ $title }}</h2>

        <p class='font-semibold text-base md:text-xl'>{{ $slot }}</p>

    </div>

</section>
