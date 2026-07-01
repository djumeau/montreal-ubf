@php
    $locale = app()->getLocale();
@endphp

<!-- Header -->
<header class="sticky top-0 z-50 bg-blue-900/90 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">

        <div class="inline-flex">
            <a href="{{ route('home') }}" class="inline-flex items-center p-0">
                <img src="{{ asset('images/icons/logo_ubf_white.svg') }}" style="width: 80px; height: 80px;"
                    alt="UBF Logo" />
                <h1 class="ml-2 text-xl md:text-2xl font-bold">{{__('header.name')}}</h1>
            </a>
        </div>

        <nav class="hidden md:flex items-center space-x-4">

            <x-nav-link url="/about" :active="request()->routeIs('about')">{{__('nav.about_us')}}</x-nav-link>

            <x-nav-link url="/events" :active="request()->routeIs('events')">{{__('nav.events')}}</x-nav-link>

            <!-- {{ app()->getLocale() }} -->

            <x-nav-link url="{{route('lang.switch', app()->getLocale() === 'en' ? 'fr' : 'en')}}" icon="globe">
                <!-- EN Link/Text -->
                <span>EN</span>
                
                &nbsp;|&nbsp;

                <!-- FR Link/Text -->
                <span>FR</span>
            </x-nav-link>

        </nav>

        <button id="hamburger" class="text-white md:hidden flex items-center">
            <i class="fa fa-bars text-2xl"></i>
        </button>

    </div>

    <!-- Mobile Menu -->
    <nav id="mobile-menu" class="hidden md:hidden bg-blue-900 text-white mt-5 pb-4 space-y-2">

        <x-nav-link url="/about" :active="request()->routeIs('about')" :isMobile='true'>{{__('nav.about_us')}}</x-nav-link>

        <x-nav-link url="/events" :active="request()->routeIs('events')" :isMobile='true'>{{__('nav.events')}}</x-nav-link>

         <x-nav-link url=""{{route('language.switch')}}" icon="globe" :isMobile='true'>
            <!-- EN Link/Text -->
            <span)>EN</span>
            
            &nbsp;|&nbsp;

            <!-- FR Link/Text -->
            <span>FR</span>
        </x-nav-link>

    </nav>

</header>
