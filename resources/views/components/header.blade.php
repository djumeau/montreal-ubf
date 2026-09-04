@php
    use Illuminate\Support\Str;

    $locale = app()->getLocale();
    $isFrench = ($locale === 'fr_CA');
    $newLocale = ($locale === 'en_CA' ? 'fr_CA' : 'en_CA');

    $logoFilePath = 'images/icons/logo_ubf_white.svg';

    $aboutActive = request()->routeIs('about') || request()->routeIs('apropos');
    $eventsActive = request()->routeIs('events') || request()->routeIs('evenements');
    $givingActive = request()->routeIs('giving') || request()->routeIs('donnez');

    if ($locale === 'fr_CA') {
        $logoFilePath = 'images/icons/logo_cbu_white.svg';
    }

@endphp

<header
    id="main-header"
    class="bg-slate-900/95 fixed top-0 left-0 w-full z-50 text-white p-4">

    <div class="container mx-auto flex justify-between items-center">

        <div class="inline-flex">

            @if (request()->routeIs('home'))
                <div class="inline-flex items-center p-0">
                    <img src="{{ asset( $logoFilePath ) }}" style="width: 80px; height: 80px;"
                    alt="{{__('header.logo_alt')}}" />
                    <h1 class="ml-2 text-xl md:text-2xl font-bold">{{__('header.name')}}</h1>
                </div>
            @else
                <a href="{{ route('home') }}" class="inline-flex items-center p-0">
                    <img src="{{ asset( $logoFilePath ) }}" style="width: 80px; height: 80px;"
                    alt="{{__('header.logo_alt')}}" />
                    <h1 class="ml-2 text-xl md:text-2xl font-bold">{{__('header.name')}}</h1>
                </a>
            @endif

        </div>

        <!-- Desktop Menu -->
        <nav class="hidden md:flex items-center space-x-4">

            <x-nav-link url="{{ __('nav.about_us.url') }}" :active="$aboutActive" >{{__('nav.about_us.title')}}</x-nav-link>

            <x-nav-link url="{{ __('nav.events.url') }}" :active="$eventsActive" >{{__('nav.events.title')}}</x-nav-link>

            <x-nav-link url="{{ __('nav.giving.url') }}" :active="$givingActive" >{{__('nav.giving.title')}}</x-nav-link>

            <x-nav-link url="{{route('locale', $newLocale)}}" icon="globe">
                {{Str::upper(Str::before($newLocale, '_'))}}
            </x-nav-link>

            @auth

                {{-- <x-nav-link url="/dashboard" icon="gauge">Admin</x-nav-link> --}}

                // This is wrong, it should be a form with a POST method to logout, not a link. But for now, we will keep it as is.
                <x-nav-link url="{{ __('nav.logout.url') }}" icon="arrow-right-from-bracket">{{__('nav.logout.title')}}</x-nav-link>

            @else

                <x-nav-link url="{{ __('nav.login.url') }}" icon="user">{{__('nav.login.title')}}</x-nav-link>

            @endauth

        </nav>

        <button id="hamburger" class="text-white md:hidden flex items-center">
            <i class="fa fa-bars text-2xl"></i>
        </button>

    </div>

    <!-- Mobile Menu -->
    <nav id="mobile-menu" class="hidden md:hidden bg-slate-900/95 text-white mt-5 pb-4 space-y-2">

        <x-nav-link url="{{ __('nav.about_us.url') }}" :active="$aboutActive" :isMobile='true'>{{__('nav.about_us.title')}}</x-nav-link>

        <x-nav-link url="{{ __('nav.events.url') }}" :active="$eventsActive" :isMobile='true'>{{__('nav.events.title')}}</x-nav-link>

        <x-nav-link url="{{ __('nav.giving.url') }}" :active="$givingActive" :isMobile='true'>{{__('nav.giving.title')}}</x-nav-link>

        @auth

            {{-- <x-nav-link url="/dashboard" icon="gauge">Admin</x-nav-link> --}}

            // This is wrong, it should be a form with a POST method to logout, not a link. But for now, we will keep it as is.
            <x-nav-link url="{{ __('nav.logout.url') }}" icon="arrow-right-from-bracket" :isMobile='true'>{{__('nav.logout.title')}}</x-nav-link>

        @else

            <x-nav-link url="{{ __('nav.login.url') }}" icon="user" :isMobile='true'>{{__('nav.login.title')}}</x-nav-link>

        @endauth

        <x-nav-link url="{{route('locale', $newLocale)}}" icon="globe" :isMobile='true'>
            {{Str::upper(Str::before($newLocale, '_'))}}
        </x-nav-link>

    </nav>

</header>
