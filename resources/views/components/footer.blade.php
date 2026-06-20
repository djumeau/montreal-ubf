<!-- Footer -->
<footer class="bg-blue-900 text-white pt-6 pb-2">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Row-1 - Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Column 1 -->
            <div class="pl-4">
                <h3 class="font-bold">Addresse</h3>
                <p>2627 rue Ryde</p>
                <p>Montréal, QC, H3K 1R7</p>
            </div>

            <!-- Column 2 -->
            <div class="flex flex-col items-left text-left">
                <h3 class="font-bold">Liens</h3>
                <div><x-nav-link url="/about" :active="request()->routeIs('about')" icon="angle-right">À propos</x-nav-link>
                </div>
                <div><x-nav-link url="/events" :active="request()->routeIs('events')" icon="angle-right">Événements</x-nav-link></div>
            </div>
        </div>

        <!-- Bottom Copyright Row -->
        <div class="text-center pt-4">
            <p class="text-center p-0">&copy; {{ now()->year }} CBU Montréal UBF</p>
        </div>

    </div>

</footer>
