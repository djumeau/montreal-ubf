<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite('resources/css/app.css')
    <title>{{ $title ?? '2026 CBU | UBF conférence d\'été - Montréal' }}</title>
</head>

<body class="bg-gray-100">

    <x-header />

    <h1 class="text-3xl font-bold text-center mt-6">{{ $title ?? '2026 CBU | UBF conférence d\'été - Montréal' }}</h1>

    <main class="container mx-auto p-4 mt-4">
        {{ $slot }}
    </main>

</body>

</html>
