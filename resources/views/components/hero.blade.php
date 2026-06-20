@props([
    'image' => './images/montreal_skyline-mobile.jpg',
])

<!-- Hero Section -->

<section {{ $attributes->merge(['class' => 'relative bg-cover bg-center bg-no-repeat h-80 flex items-center']) }}
    style="background-image: url('{{ asset($image) }}')">
    <div class="overlay"></div>
    <div class="container mx-auto text-center z-10">
        <h2 class="text-4xl md:text-5xl text-white font-bold mb-8">
            {{ $slot }}
        </h2>
    </div>
</section>
