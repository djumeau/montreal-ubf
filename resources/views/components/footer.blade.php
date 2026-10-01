@php

    $aboutActive = request()->routeIs('about') || request()->routeIs('apropos');
    $eventsActive = request()->routeIs('events') || request()->routeIs('evenements');
    $bibleStudiesActive = request()->routeIs('bible-studies') || request()->routeIs('etudes-bibliques');
    $scheduleActive = request()->routeIs('bible-study-schedule') || request()->routeIs('horaire-etudes-bibliques');
    $givingActive = request()->routeIs('giving') || request()->routeIs('donner');
    $privacyActive = request()->routeIs('confidentiality') || request()->routeIs('confidentialite');
    $contactActive = request()->routeIs('contact');

    // Random Bible verse in the current locale, e.g. ['text' => '...', 'reference' => 'Psalm 119:105']
    $verse = \Illuminate\Support\Arr::random(__('footer.verses'));

@endphp

<!-- Footer -->
<footer class="border-t outline-1 border-slate-300 bg-slate-800 text-white pt-6 pb-2">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Row-1 - Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-0 md:divide-x md:divide-slate-600 mb-4">

            <!-- Column 1 -->
            <div class="pl-4">

                <!--Address Container -->
                <div class="address-container" style="display: flex; align-items: center; gap: 12px;">

                    <!-- Left Side: Clickable Thumbnail Image -->
                    <a href="https://www.google.com/maps/place/University+Bible+Fellowship+Missionary+Church/@45.4755238,-73.5676033,17z/data=!3m1!4b1!4m6!3m5!1s0x4cc9107f9e01be35:0x3487a6c7fa11c19!8m2!3d45.4755201!4d-73.5650284!16s%2Fg%2F1tz75ldw?entry=ttu&g_ep=EgoyMDI2MDgyNC4wIKXMDSoASAFQAw%3D%3D" target="_blank" rel="noopener noreferrer" style="flex-shrink: 0;">
                        <img src="{{ asset('images/mtl-ubf-map.jpg') }}" alt="Location Map" class="map-thumbnail" style="width: 120px; height: auto; display: block; border-radius: 8px;">
                    </a>

                    <!-- Right Side: Address Text -->
                    <div class="address-text">
                        <h3 class="font-bold">{{__('footer.address')}}</h3>
                        <p>2627 rue Ryde</p>
                        <p>Montréal, QC, H3K 1R7</p>
                    </div>

                </div>

                <h3 class="pt-4 pb-2">
                    <i class="fas fa-envelope text-white pr-2" aria-hidden="true"></i>
                    <span class="text-white"><a href="mailto:montrealubf@gmail.com" target="_blank" class="hover:underline">montrealubf@gmail.com</a>
                    </span>
                </h3>

                <a href="https://facebook.com/montrealubf" aria-label="Facebook" target="_blank">
                    <i class="fa-brands fa-facebook text-white text-2xl"></i>
                </a>

                <a href="https://instagram.com/montrealubf" aria-label="Instagram" target="_blank">
                    <i class="fa-brands fa-instagram text-white text-2xl"></i>
                </a>

                <!-- <a href="https://x.com/montrealubf" aria-label="X (formerly Twitter)" target="_blank">
                    <i class="fa-brands fa-x-twitter text-white text-2xl"></i>
                </a> -->

            </div>

            <!-- Column 2 -->
            <div class="flex flex-col items-left text-left md:pl-8 border-t border-slate-600 pt-6 md:border-t-0 md:pt-0">

                <h3 class="font-bold pl-4">{{__('footer.links')}}</h3>

                <div class="pl-4"><x-nav-link url="{{ __('nav.about_us.url') }}" :active="$aboutActive" icon="angle-right">{{__('nav.about_us.title')}}</x-nav-link>
                </div>

                <div class="pl-4"><x-nav-link url="{{ __('nav.events.url') }}" :active="$eventsActive"
                        icon="angle-right">{{__('nav.events.title')}}</x-nav-link>
                </div>

                <div class="pl-4"><x-nav-link url="{{ __('nav.confidentiality.url') }}" :active="$privacyActive"
                        icon="angle-right">{{__('nav.confidentiality.title')}}</x-nav-link>
                </div>

                <div class="pl-4"><x-nav-link url="{{ __('nav.bible_studies.url') }}" :active="$bibleStudiesActive"
                        icon="angle-right">{{__('nav.bible_studies.title')}}</x-nav-link>
                </div>

                <div class="pl-4"><x-nav-link url="{{ __('nav.study_schedule.url') }}" :active="$scheduleActive"
                        icon="angle-right">{{__('nav.study_schedule.title')}}</x-nav-link>
                </div>

                <div class="pl-4"><x-nav-link url="{{ __('nav.giving.url') }}" :active="$givingActive"
                        icon="angle-right">{{__('nav.giving.title')}}</x-nav-link>
                </div>

                <div class="pl-4"><x-nav-link url="{{ __('nav.contact.url') }}" :active="$contactActive"
                        icon="angle-right">{{__('nav.contact.title')}}</x-nav-link>
                </div>

            </div>

            <!-- Column 3: Random Bible Verse -->
            <figure class="flex flex-col justify-center text-center px-4 md:px-8 border-t border-slate-600 pt-6 md:border-t-0 md:pt-0">

                <blockquote class="font-serif italic text-lg leading-relaxed text-white/90">
                    {{ __('footer.verse_quote', ['verse' => $verse['text']]) }}
                </blockquote>

                <figcaption class="mt-3 text-sm tracking-wide text-slate-300">
                    {{ $verse['reference'] }}
                </figcaption>

            </figure>

        </div>

        <!-- Bottom Copyright Row -->
        <div class="text-center mb-16">
            <p class="text-center p-0">&copy; {{ now()->year }} {{__('footer.name')}}</p>
        </div>

    </div>

</footer>
