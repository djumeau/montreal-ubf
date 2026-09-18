@props([
    'image' => 'images/montreal_skyline-mobile.jpg',
    'subtitle' => 'Adoration les dimanches',
    'cat_1' => 'Jeunes: ',
    'cat_1_time' => '9h00',
    'cat_2' => 'Générale: ',
    'cat_2_time' => '11h00',
    'social_media' => 'Réseaux sociaux',
    'image_2' => "images/ig_qr_code_fr.png",
])

<!-- Hero Section -->
<section {{ $attributes->merge(['class' => 'relative bg-cover bg-center bg-no-repeat h-[425px] flex items-center pt-12']) }}
    style="background-image: url('{{ asset($image) }}')">

    <div class="overlay bg-black/65"></div>

    <div class="container mx-auto text-center z-10">

        <div class="flex flex-col">

            <!-- top row - Heading -->
            <div class="mb-2 mx-4 pt-22 md:pt-25">
                <h1 class="text-2xl md:text-4xl text-white font-bold">{{ $slot }}</h1>
            </div>

            <!-- Middle Row + QR code -->
            <div class="flex flex-col md:flex-row items-center justify-center gap-6">

                <div id="WorshipTimeSocialMedia" class="text-white italic pt-6 pb-0">

                    <!-- Worship Times -->
                    <p class="mb-2">{{ $subtitle }}</p>

                    <div class="flex justify-center gap-6 pt-0">

                        <!-- First column: Right Justified -->
                        <div class="flex items-center justify-end text-right">
                            <i class="fas fa-clock text-white pr-2"></i> <span class="text-white">{{ $cat_1 . ' ' . $cat_1_time }}</span>
                        </div>

                        <!-- Second column: Left Justified -->
                        <div class="flex items-center justify-start text-left">
                            <i class="fas fa-clock text-white pr-2"></i> <span class="text-white">{{ $cat_2 . ' ' . $cat_2_time }}</span>
                        </div>

                    </div>

                    <!-- Social Media -->
                    <div class="col-span-2 text-center text-white pt-2 pb-2">
                        <p class="text-white italic">{{ $social_media }}</p>
                    </div>

                    <div id="socialMediaIcons" class="flex justify-center gap-4 ">

                        <a href="https://facebook.com/montrealubf" aria-label="Facebook" target="_blank">
                            <i class="fa-brands fa-facebook text-white text-3xl"></i>
                        </a>

                        <a href="https://instagram.com/montrealubf" aria-label="Instagram" target="_blank">
                            <i class="fa-brands fa-instagram text-white text-3xl"></i>
                        </a>

                        <!-- <a href="https://x.com/montrealubf" aria-label="X (formerly Twitter)" target="_blank">
                            <i class="fa-brands fa-x-twitter text-white text-3xl"></i>
                        </a> -->

                    </div>

                </div>

                <a href="https://startbiblestudy.org/montreal-ubf" class="cursor-pointer"><img src="{{ asset($image_2) }}"
                    class="hidden md:block w-36 h-36"></a>

            </div>

        </div>

    </div>

</section>
