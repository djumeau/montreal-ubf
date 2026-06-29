@props([
    'bgSolid' => 'bg-gray-800', 
    'bgGradient' => null,       
    'textColor' => 'text-black' 
])

@php
    // Tailwind scans this literal map and guarantees both classes compile into app.css
    $colorMap = [
        'light' => 'text-black',
        'dark' => 'text-white'
    ];

    // Gather all backgrounds passed down via props or direct 'class=""' attributes
    $customClasses = $attributes->get('class', '');
    $combinedBg = implode(' ', array_filter([$bgSolid, $bgGradient, $customClasses]));

    // Match dark numbers (700-950) or the word black
    $isDark = preg_match('/-(700|800|900|950)\b|black/', $combinedBg);

    // Apply the mutually exclusive choice
    $finalTextColor = $textColor ?? ($isDark ? $colorMap['dark'] : $colorMap['light']);

    // Set fallback layout background if none was passed anywhere
    $hasBgClass = preg_match('/\bbg-/', $combinedBg);
    $fallbackBg = !$hasBgClass ? 'bg-gray-100' : '';
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
    <title>{{ $title ?? 'CBU Montréal UBF' }}</title>
</head>

{{-- Merge classes while cleanly isolating variables to prevent style duplication conflicts --}}
<body 
    style="--layout-text: {{ $finalTextColor === 'text-white' ? '#ffffff' : '#000000' }};"
    {{ $attributes->merge(['class' => implode(' ', array_filter([$fallbackBg, $bgSolid, $bgGradient, $finalTextColor, 'min-h-screen']))]) }}
>

    <x-header />

    @if (request()->is('/'))
        <!-- Mobile Hero -->
        <div class='block md:hidden'>
            <x-hero image="./images/montreal_skyline-mobile.jpg" subtitle="Adoration les dimanches" cbf_time="9h00"
                worship_time="11h00">Bienvenue à
                l'église<br />de la communion biblique
                universitaire<br />de Montréal!</x-hero>
            <x-events href='https://montrealubf.org/franco2026' image='./images/2026_conf/2026-conf_franco_titre-mobile.jpg' title="Événements">2026 Conférence
                biblique francophone d&apos;été</x-events>
        </div>

        <!-- Desktop Hero -->
        <div class='hidden md:block'>
            <x-hero image="./images/montreal_skyline-desktop.jpg" subtitle="Adoration les dimanches" cbf_time="9h00"
                worship_time="11h00">Bienvenue à
                l&apos;église<br />de la communion biblique
                universitaire <br />de Montréal!</x-hero>
            <x-events href='https://montrealubf.org/franco2026' image='./images/2026_conf/2026-conf_franco_titre-desktop.jpg' title="Événements">2026 Conférence
                biblique francophone d&apos;été</x-events>
        </div>
    @endif

    <main class="container mx-auto p-4 mt-4">
        {{ $slot }}
    </main>

    <x-footer />

</body>

</html>
