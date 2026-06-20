@props([
    'image' => './images/montreal_skyline-mobile.jpg',
    'cbf_time' => '9h00',
    'worship_time' => '11h00',
])

<!-- Hero Section -->
<section {{ $attributes->merge(['class' => 'relative bg-cover bg-center bg-no-repeat h-80 flex items-center']) }}
    style="background-image: url('{{ asset($image) }}')">
    <div class="overlay bg-black/75"></div>
    <div class="container mx-auto text-center z-10">
        <div class="flex flex-col">
            <!-- top row -->
            <div class="mb-2 mx-4">
                <h2 class="text-2xl md:text-4xl text-white font-bold">{{ $slot }}</h2>
                <p class="text-white italic underline pt-6 pb-0">Adoration les dimanches</p>
            </div>

            <!-- Bottom Row -->
            <div class="grid grid-cols-2 gap-4 p-0">
                <!-- First column: Right Justified -->
                <div class="flex items-center justify-end text-right">
                    <i class="fas fa-clock text-white"></i><span class="text-white">Jeunes: {{ $cbf_time }}</span>
                </div>

                <!-- Second column: Left Justified -->
                <div class="flex items-center justify-start text-left">
                    <i class="fas fa-clock text-white"></i><span class="text-white">Générale: {{ $worship_time }}</span>
                </div>

            </div>

        </div>
    </div>
</section>
