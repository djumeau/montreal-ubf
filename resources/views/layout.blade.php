@props([
    'bgSolid' => 'bg-gray-800',
    'bgGradient' => null,
    'textColor' => 'text-black',
    'questionSheets' => null, // Home page only: featured study's question sheets keyed by extension (HomeController)
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

    @if(request()->is('/'))

        <!-- Mobile Hero -->
        <div class='block md:hidden'>

            <x-hero
                image="./images/montreal_skyline-mobile.jpg"
                subtitle="{{__('home/hero.subtitle')}}"
                cat_1="{{__('home/hero.cat_1')}}" cat_1_time="9h00"
                cat_2="{{__('home/hero.cat_2')}}" cat_2_time="11h00"
                social_media="{{__('home/hero.social_media')}}"
                image_2="{{ __('home/hero.image_2') }}">
                {{__('home/hero.welcome')}}
            </x-hero>

            <!-- QR Code (Bible Study site) placed under the hero on mobile -->
            <div class="flex justify-center py-4">
                <a href="https://startbiblestudy.org/montreal-ubf" target="_blank" class="cursor-pointer">
                    <img src="{{ asset(__('home/hero.image_2')) }}" alt="{{ __('home/hero.image_2_alt_text') }}" class="w-40 h-40">
                </a>
            </div>

            <x-study
                :imagesDir="__('home/study.imagesDir')"
                :image="__('home/study.image.mobile')"
                :heading="__('home/study.heading')"
                :book="__('home/study.book')"
                :dateStamp="__('home/study.dateStamp')"
                :biblePassage="__('home/study.biblePassage')"
                :bibleLink="__('home/study.bibleLink')"
                :questionSheets="$questionSheets">
                {{__('home/study.title')}}
            </x-study>

        </div>

        <!-- Desktop Hero -->
        <div class='hidden md:block'>

            <x-hero
                image="./images/montreal_skyline-desktop.jpg"
                subtitle="{{__('home/hero.subtitle')}}"
                cat_1="{{__('home/hero.cat_1')}}" cat_1_time="9h00"
                cat_2="{{__('home/hero.cat_2')}}" cat_2_time="11h00"
                social_media="{{__('home/hero.social_media')}}"
                image_2="{{ __('home/hero.image_2') }}">
                {{__('home/hero.welcome')}}
            </x-hero>

            <x-study
                :imagesDir="__('home/study.imagesDir')"
                :image="__('home/study.image.desktop')"
                :heading="__('home/study.heading')"
                :book="__('home/study.book')"
                dateStamp="{{__('home/study.dateStamp')}}"
                :biblePassage="__('home/study.biblePassage')"
                :bibleLink="__('home/study.bibleLink')"
                :questionSheets="$questionSheets">
                {{__('home/study.title')}}
            </x-study>

        </div>

    @endif

    <main class="container mx-auto p-4 mt-4">
        {{ $slot }}
    </main>

    <x-footer />

    <x-compliance-requirement />

</body>

</html>
