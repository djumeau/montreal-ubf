<!-- Footer -->
<footer class="bg-blue-900 text-white pt-6 pb-2">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Row-1 - Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Column 1 -->
            <div class="pl-4">
                <h3 class="font-bold">{{__('footer.address')}}</h3>
                <p>2627 rue Ryde</p>
                <p>Montréal, QC, H3K 1R7</p>

                <h3 class="pt-4">
                    <i class="fas fa-envelope text-white pr-2" aria-hidden="true"></i> <span class="text-white"><a href="mailto:montrealubf@gmail.com">montrealubf@gmail.com</a></span>
                </h3>
            </div>

            <!-- Column 2 -->
            <div class="flex flex-col items-left text-left">
                <h3 class="font-bold pl-4">{{__('footer.links')}}</h3>
                <div class="pl-4"><x-nav-link url="/about" :active="request()->routeIs('about')" icon="angle-right">{{__('footer.about_us')}}</x-nav-link>
                </div>
                <div class="pl-4"><x-nav-link url="/events" :active="request()->routeIs('events')"
                        icon="angle-right">{{__('footer.events')}}</x-nav-link></div>
            </div>
        </div>

        <!-- Bottom Copyright Row -->
        <div class="text-center pt-4">
            <p class="text-center p-0">&copy; {{ now()->year }} {{__('footer.name')}}</p>
        </div>

    </div>

</footer>
