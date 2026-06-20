<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo_ubf_favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <title>{{ $title ?? 'CBU - Montréal' }}</title>
</head>

<body class="bg-gray-100 text-gray-800">

    <x-header />

    @if (request()->is('/'))
        <x-hero>Welcome to Montreal UBF!</x-hero>
    @endif

    <h1 class="text-3xl font-bold text-center mt-6">
        {{ $title ?? 'Église de la communion biblique universitaire – Montréal' }}</h1>

    <main class="container mx-auto p-4 mt-4">
        {{ $slot }}
    </main>

    <x-footer />

</body>

</html>
