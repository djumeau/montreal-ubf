@props([
    'bgSolid' => 'bg-slate-900',
    'bgGradient' => null,
    'textColor' => 'text-white',
])

@php
    // Tailwind scans this literal map and guarantees both classes compile into app.css

    // Gather all backgrounds passed down via props or direct 'class=""' attributes
    $customClasses = $attributes->get('class', '');
    $combinedBg = implode(' ', array_filter([$bgSolid, $bgGradient, $customClasses]));

    // Apply the mutually exclusive choice
    $finalTextColor = $colorMap['dark'];

    // Set fallback layout background if none was passed anywhere
    $hasBgClass = preg_match('/\bbg-/', $combinedBg);
    $fallbackBg = !$hasBgClass ? 'bg-slate-900' : '';
@endphp

<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" type="image/svg+xml" href="{{ asset('logo_ubf_favicon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

    <title>{{ $title ?? 'CBU Montréal UBF' }} – {{ __('dashboard/index.dashboard') }}</title>

</head>

<body style="--layout-text: {{ $finalTextColor === 'text-white' ? '#ffffff' : '#000000' }};"
    {{ $attributes->merge(['class' => implode(' ', array_filter([$fallbackBg, $bgSolid, $bgGradient, $finalTextColor, 'min-h-screen']))]) }}>

    <x-header />

    <div x-data="{ sidebarOpen: true }" class="flex min-h-screen text-white">

        <!-- Shared Left Column: Collapsible Sidebar -->
        <aside
            :class="{
                'w-full block': sidebarOpen,
                'md:block md:w-16': !sidebarOpen,
                'md:w-64': sidebarOpen && window.innerWidth >= 768
            }"
            class="transition-all duration-300 ease-in-out border rounded-sm border-slate-100 flex flex-col justify-between">

            <div>

                <!-- Header & Toggle Chevron Button -->
                <div class="p-2 flex items-center justify-between border-b-2">
                    <span x-show="sidebarOpen" class="font-bold text-lg text-slate-100">
                        {{ __('dashboard/index.dashboard') }}
                    </span>
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="grid place-items-center size-10 pl-2 text-slate-100 hover:text-slate-300 transition-colors focus:outline-none cursor-pointer">
                        <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                    </button>
                </div>

                <!-- Profile Info Block -->
                <div class="p-4 flex flex-col items-center text-center overflow-hidden">
                    <!-- {{ auth()->user()->avatar_url }} -->
                    <img src="{{ auth()->user()->avatar_url }}?v={{ time() }}" alt="Avatar"
                        :class="sidebarOpen ? 'size-20' : 'size-10 md:size-10'"
                        class="rounded-full object-cover border-2 border-white shadow-xs transition-all duration-300">


                    <div x-show="sidebarOpen" class="mt-3 transition-opacity duration-300">
                        <h3 class="font-semibold text-base leading-tight truncate max-w-50">{{ auth()->user()->name }}
                        </h3>
                        <p class="text-xs text-slate-100 truncate max-w-50 mb-2">{{ auth()->user()->email }}</p>
                        <span
                            class="inline-block border-2 px-2 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-black text-slate-100">
                            {{ auth()->user()->role->value }}
                        </span>
                    </div>
                </div>
                <!-- End of Profile Block -->

                <!-- Context Dynamic Links -->
                <nav id="features" class="space-y-1">

                    <!-- My Profile Endpoint Link -->
                    <a href="{{ route('dashboard') }}"
                        class="w-full flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 font-medium' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                        <i class="fas fa-user-cog w-6 text-center"></i>
                        <span x-show="sidebarOpen"
                            class="ml-3 text-sm">{{ __('dashboard/index.update_profile') }}</span>
                    </a>

                    <!-- Conditional Admin / Elder Access Link -->
                    @if (auth()->user()->canManageRoles())
                        <!--
                        <a href="{{ route('admin.roles.index') }}"
                            class="w-full flex items-center p-3 rounded-lg transition-colors {{ request()->routeIs('admin.roles.*') ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 font-medium' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <i class="fas fa-users-cog w-6 text-center"></i>
                            <span x-show="sidebarOpen" class="ml-3 text-sm">{{ __('Manage Roles') }}</span>
                        </a>
                        -->
                    @endif
                </nav>
            </div>
        </aside>

        <!-- Right Column Shared Main Layout Canvas Frame -->
        <main :class="sidebarOpen ? 'hidden md:block' : 'block'" class="flex-1 p-6 lg:p-8 overflow-y-auto">

            <!-- Display Notification - If applicable -->
            @if (session('status'))
                <div class="flex items-center justify-left ml-4 mb-4">
                    <div class="w-100 p-2 border rounded-sm border-emerald-600 text-emerald-400 text-sm">
                        {{ __(session('status')) }}
                    </div>
                </div>
            @endif

            <!-- Page Body Slot Placement -->
            {{ $slot }}

        </main>

    </div>

    <x-footer />

</body>

</html>
