@php
    use Illuminate\Support\Str;

    $locale = app()->getLocale();
    $isFrench = ($locale === 'fr_CA');
    $newLocale = ($locale === 'en_CA' ? 'fr_CA' : 'en_CA');

    $logoFilePath = 'images/icons/logo_ubf_white.svg';

    $homeActive = request()->routeIs('home');
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

        <div>

            <!-- Row 1: Logo and Site Name -->
            <div id="row 1" class="flex items-center">
                @if (request()->routeIs('home'))
                    <div class="mt-2 mb-0 inline-flex items-center">
                        <img src="{{ asset( $logoFilePath ) }}" style="width: 80px; height: 60px;"
                        alt="{{__('header.logo_alt')}}" />
                        <h1 class="text-xl md:text-2xl font-bold">{{__('header.name')}}</h1>
                    </div>
                @else
                    <a href="{{ route('home') }}" class="inline-flex items-center">
                        <img src="{{ asset( $logoFilePath ) }}" style="width: 80px; height: 60px;"
                        alt="{{__('header.logo_alt')}}" />
                        <h1 class="text-xl md:text-2xl font-bold">{{__('header.name')}}</h1>
                    </a>
                @endif
            </div>

            <!-- Row 2: Welcome Message -->
            <div id="row 2">
                @auth
                    @if($homeActive)
                         <i class="ml-3 fa fa-user p-0 mr-1"></i><span class="italic">{{ __('home/index.hello') }}, <a href="{{ __('nav.dashboard.url') }}" id="msg" class="text-blue-300 hover:text-blue-500 underline">{{ Auth::User()->name }}</a>{{ $isFrench ? ' !' : '!' }}</span>
                    @endif
                @endauth
            </div>

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

                <x-logout-button />

            @else

                <x-login-button />

            @endauth

        </nav>

        <button id="hamburger" class="text-white md:hidden flex items-center">
            <i class="fa fa-bars text-2xl"></i>
        </button>

    </div>

    <!-- Mobile Menu -->
    <nav id="mobile-menu" class="hidden md:hidden text-white space-y-1">

        <x-nav-link url="{{ __('nav.about_us.url') }}" :active="$aboutActive" :isMobile='true'>{{__('nav.about_us.title')}}</x-nav-link>

        <x-nav-link url="{{ __('nav.events.url') }}" :active="$eventsActive" :isMobile='true'>{{__('nav.events.title')}}</x-nav-link>

        <x-nav-link url="{{ __('nav.giving.url') }}" :active="$givingActive" :isMobile='true'>{{__('nav.giving.title')}}</x-nav-link>

        <x-nav-link url="{{route('locale', $newLocale)}}" icon="globe" :isMobile='true'>
            {{Str::upper(Str::before($newLocale, '_'))}}
        </x-nav-link>

        @auth

            {{-- <x-nav-link url="/dashboard" icon="gauge">Admin</x-nav-link> --}}

            <x-logout-button isMobile='true' />

        @else

            <x-login-button isMobile='true' />

        @endauth

    </nav>

</header>
