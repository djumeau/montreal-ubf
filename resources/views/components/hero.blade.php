@props([
    'image' => './images/montreal_skyline-mobile.jpg',
    'subtitle' => 'Adoration les dimanches',
    'cat_1' => 'Jeunes: ',
    'cat_1_time' => '9h00',
    'cat_2' => 'Générale: ',
    'cat_2_time' => '11h00',
    'social_media' => 'Réseaux sociaux',
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
                <p class="text-white italic pt-6 pb-0">{{ $subtitle }}</p>
            </div>

            <!-- Middle Row -->
            <div class="grid grid-cols-2 gap-2 pt-0">
                <!-- First column: Right Justified -->
                <div class="flex items-center justify-end text-right">
                    <i class="fas fa-clock text-white pr-2"></i> <span class="text-white">{{ $cat_1 . ' ' . $cat_1_time }}</span>
                </div>

                <!-- Second column: Left Justified -->
                <div class="flex items-center justify-start text-left">
                    <i class="fas fa-clock text-white pr-2"></i> <span class="text-white">{{ $cat_2 . ' ' . $cat_2_time }}</span>
                </div>

            </div>

            <!-- Bottom Row: Social Media Icons -->

            <div class="col-span-2 text-center text-white pt-6 pb-1">
                <p class="text-white italic">{{ $social_media }}</p>
            </div>

            <div class="mx-2">
                <a href="https://instagram.com/montrealubf" aria-label="Instagram" target="_blank">
                    <i class="fa-brands fa-instagram text-white"></i>
                </a>
                <a href="https://facebook.com/montrealubf" aria-label="Facebook" target="_blank">
                    <i class="fa-brands fa-facebook text-white"></i>
                </a>
                <a href="https://x.com/montrealubf" aria-label="X (formerly Twitter)" target="_blank">
                    <i class="fa-brands fa-x-twitter text-white"></i>
                </a>
            </div>

        </div>
    </div>
</section>
