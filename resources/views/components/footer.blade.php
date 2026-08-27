<!-- Footer -->
<footer class="bg-blue-900 text-white pt-6 pb-2">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Row-1 - Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">

            <!-- Column 1 -->
            <div class="pl-4">

                <!--Address Container -->
                <div class="address-container" style="display: flex; align-items: center; gap: 15px;">

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

                <h3 class="pt-4">
                    <i class="fas fa-envelope text-white pr-2" aria-hidden="true"></i>
                    <span class="text-white"><a href="mailto:montrealubf@gmail.com" target="_blank">montrealubf@gmail.com</a>
                    </span>
                </h3>

            </div>

            <!-- Column 2 -->
            <div class="flex flex-col items-left text-left">
                <h3 class="font-bold pl-4">{{__('footer.links')}}</h3>
                <div class="pl-4"><x-nav-link url="/about" :active="request()->routeIs('about')" icon="angle-right">{{__('nav.about_us')}}</x-nav-link>
                </div>
                <div class="pl-4"><x-nav-link url="/events" :active="request()->routeIs('events')"
                        icon="angle-right">{{__('nav.events')}}</x-nav-link></div>
            </div>

        </div>

        <!-- Bottom Copyright Row -->
        <div class="text-center pt-4">
            <p class="text-center p-0">&copy; {{ now()->year }} {{__('footer.name')}}</p>
        </div>

    </div>

</footer>
