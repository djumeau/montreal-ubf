@php
    use Illuminate\Support\Str;

    $locale = app()->getLocale();
    $isFrench = ($locale === 'fr_CA');
    $newLocale = ($locale === 'en_CA' ? 'fr_CA' : 'en_CA');

    $logoFilePath = 'images/icons/logo_ubf_white.svg';

    $homeActive = request()->routeIs('home');
    $aboutActive = request()->routeIs('about') || request()->routeIs('apropos');
    $eventsActive = request()->routeIs('events') || request()->routeIs('evenements');
    $givingActive = request()->routeIs('giving') || request()->routeIs('donner');
    $privacyActive = request()->routeIs('confidentiality') || request()->routeIs('confidentialite');

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
                         <i class="ml-3 fa fa-user p-0 mr-1"></i><span class="italic">{{ __('home/index.hello') }}, <a href="{{ route(__('nav.dashboard.name')) }}" id="msg" class="text-blue-300 hover:text-blue-500 underline">{{ Auth::User()->name }}</a>{{ $isFrench ? ' !' : '!' }}</span>
                    @endif
                @endauth
            </div>

        </div>

        <!-- Desktop Menu -->
        <nav class="hidden md:flex items-center space-x-4">

            <div class="relative inline-flex items-center" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">

                <button type="button" @click="open = !open"
                    class="inline-flex items-center text-white">
                    <i class="fa-solid fa-caret-right mr-1 transition-transform duration-200" :class="{ 'rotate-90': open }"></i>
                    <span class="hover:underline">{{ __('nav.info.title') }}</span>
                </button>

                <div x-show="open" x-transition x-cloak
                    class="absolute left-0 top-full mt-2 min-w-40 bg-slate-800 border border-white rounded shadow-lg py-2 z-50">


                    <div class="px-4 py-1">
                        <x-nav-link url="{{ __('nav.about_us.url') }}" :active="$aboutActive">{{__('nav.about_us.title')}}</x-nav-link>
                    </div>

                    <div class="px-4 py-1">
                        <x-nav-link url="{{ __('nav.confidentiality.url') }}" :active="$privacyActive">{{__('nav.confidentiality.title')}}</x-nav-link>
                    </div>

                </div>

            </div>

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
    <nav id="mobile-menu" class="hidden md:hidden text-white space-y-0.5">

        <div class="p-3 text-gray-300 font-semibold tracking-wide">{{ __('nav.info.title') }}</div>

        <div class="pl-6">
            <x-nav-link url="{{ __('nav.about_us.url') }}" :active="$aboutActive" :isMobile='true'>{{__('nav.about_us.title')}}</x-nav-link>
        </div>

        <div class="pl-6">
            <x-nav-link url="{{ __('nav.confidentiality.url') }}" :active="$privacyActive" :isMobile='true'>{{__('nav.confidentiality.title')}}</x-nav-link>
        </div>

        <x-nav-link url="{{ __('nav.events.url') }}" :active="$eventsActive" :isMobile='true'>{{__('nav.events.title')}}</x-nav-link>

        <x-nav-link url="{{ __('nav.giving.url') }}" :active="$givingActive" :isMobile='true'>{{__('nav.giving.title')}}</x-nav-link>

        <x-nav-link url="{{route('locale', $newLocale)}}" icon="globe" :isMobile='true' class="mb-2">
            {{Str::upper(Str::before($newLocale, '_'))}}
        </x-nav-link>

        @auth

            <x-logout-button isMobile='true' />

        @else

            <x-login-button isMobile='true' />

        @endauth

    </nav>

</header>
