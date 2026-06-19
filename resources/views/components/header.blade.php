<!-- Header -->
<header class="bg-blue-900 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">

        <div class="inline-flex">
            <a href="{{ route('home') }}" class="inline-flex items-center p-0">
                <img src="{{ asset('images/icons/logo_ubf_white.svg') }}" style="width: 60px; height: 60px;"
                    alt="UBF Logo" />
                <h1 class="ml-10 font-bold">CBU Montréal UBF</h1>
            </a>
        </div>

        <nav class="hidden md:flex items-center space-x-4">

            <x-nav-link url="/about" :active="request()->routeIs('about')"> About</x-nav-link>

            <x-nav-link url="/events" :active="request()->routeIs('events')"> Events</x-nav-link>

            <x-nav-link url="/dashboard" :active="request()->routeIs('admin')" icon="gauge">Admin</x-nav-link>

            <x-nav-link url="/dashboard" :active="request()->routeIs('admin')" icon="globe">FR</x-nav-link>

            <x-button-link url="/login" :active="request()->routeIs('login')" icon="user" bgColor="bg-sky-500" hoverColor="bg-sky-700"
                textColor="text-white">Login</x-button-link>

        </nav>

        <button id="hamburger" class="text-white md:hidden flex items-center">
            <i class="fa fa-bars text-2xl"></i>
        </button>

    </div>

    <!-- Mobile Menu -->
    <nav id="mobile-menu" class="hidden md:hidden bg-blue-900 text-white mt-5 pb-4 space-y-2">
        <x-nav-link url="/about" :active="request()->routeIs('about')" :isMobile='true'>About</x-nav-link>
        <x-nav-link url="/events" :active="request()->routeIs('events')" :isMobile='true'>Events</x-nav-link>
        <x-nav-link url="/admin" :active="request()->routeIs('admin')" :isMobile='true'>Admin</x-nav-link>

        <x-nav-link url="/dashboard" :active="request()->routeIs('admin')" icon="globe" :isMobile='true'>FR</x-nav-link>

        <x-button-link url="/login" :active="request()->routeIs('login')" icon="user" bgColor="bg-sky-500" hoverColor="bg-sky-700"
            textColor="text-white" :block="true">Login</x-button-link>
    </nav>
</header>
