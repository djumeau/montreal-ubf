<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo_ubf_favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <title>{{ $title ?? 'CBU Montréal UBF' }}</title>
</head>

<body class="bg-gray-100 text-gray-800">

    <x-header />

    @if (request()->is('/'))
        <!-- Mobile Hero -->
        <div class='block md:hidden'>
            <x-hero image="./images/montreal_skyline-mobile.jpg" subtitle="Adoration les dimanches" cbf_time="9h00"
                worship_time="11h00">Bienvenue à
                l'église<br />de la communion biblique
                universitaire<br />de Montréal!</x-hero>
            <x-events image='./images/2026_conf/2026-conf_franco_titre-mobile.jpg' title="Événements">2026 Conférence
                biblique francophone d'été</x-events>
        </div>

        <!-- Desktop Hero -->
        <div class='hidden md:block'>
            <x-hero image="./images/montreal_skyline-desktop.jpg" subtitle="Adoration les dimanches" cbf_time="9h00"
                worship_time="11h00">Bienvenue à
                l'église<br />de la communion biblique
                universitaire <br />de Montréal!</x-hero>
            <x-events image='./images/2026_conf/2026-conf_franco_titre-desktop.jpg' title="Événements">2026 Conférence
                biblique francophone d'été</x-events>
        </div>
    @endif

    <main class="container mx-auto p-4 mt-4">
        {{ $slot }}
    </main>

    <x-footer />

</body>

</html>
