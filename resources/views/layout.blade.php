@props([
    'bgSolid' => 'bg-gray-800',
    'bgGradient' => null,
    'textColor' => 'text-black',
])

@php

    // Gather all backgrounds passed down via props or direct 'class=""' attributes
    $customClasses = $attributes->get('class', '');
    $combinedBg = implode(' ', array_filter([$bgSolid, $bgGradient, $customClasses]));

    // Set fallback layout background if none was passed anywhere
    $hasBgClass = preg_match('/\bbg-/', $combinedBg);
    $fallbackBg = !$hasBgClass ? 'bg-gray-100' : '';

    // Featured study (BibleStudy id) shown under the hero on mobile and desktop: the Bible study of the next
    // Sunday worship service that has one (events of the Manage Schedule dashboard). Home page only, where it is shown;
    // without such a service, nothing is featured (see the study component)
    $nextService = request()->is('/')
        ? \App\Models\Event::where('category', \App\Enums\EventCategory::SUNDAY_SERVICE)
            ->whereNotNull('bible_study_id')
            ->upcoming()
            ->orderBy('start_date')
            ->first()
        : null;

    $featuredStudyId = $nextService?->bible_study_id;

    // The featured study is held on the day of that service
    $studyDate = $nextService?->start_date;

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
    style="--layout-text: {{ $textColor === 'text-white' ? '#ffffff' : '#000000' }};"
    {{ $attributes->merge(['class' => implode(' ', array_filter([$fallbackBg, $bgSolid, $bgGradient, $textColor, 'min-h-screen']))]) }}
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
            <div class="flex justify-center pt-6 mb-4">
                <a href="https://startbiblestudy.org/montreal-ubf" target="_blank" class="cursor-pointer">
                    <img src="{{ asset(__('home/hero.image_2')) }}" alt="{{ __('home/hero.image_2_alt_text') }}" class="w-40 h-40">
                </a>
            </div>

            <!-- Upcoming events ticked "Featured on Home Page" on Manage Schedule (nothing shows without any) -->
            <x-featured-on-home-page />

            <x-study
                heading="{{ __('home/study.upcoming_sunday') }}"
                :studyId="$featuredStudyId"
                :date="$studyDate">
            </x-study>

        </div>

        <!-- Desktop Hero -->
        <!-- A gap between the hero, each featured event and the Bible study (none added when there is no featured event) -->
        <div class='hidden md:flex md:flex-col md:gap-1'>

            <x-hero
                image="./images/montreal_skyline-desktop.jpg"
                subtitle="{{__('home/hero.subtitle')}}"
                cat_1="{{__('home/hero.cat_1')}}" cat_1_time="9h00"
                cat_2="{{__('home/hero.cat_2')}}" cat_2_time="11h00"
                social_media="{{__('home/hero.social_media')}}"
                image_2="{{ __('home/hero.image_2') }}">
                {{__('home/hero.welcome')}}
            </x-hero>

            <!-- Upcoming events ticked "Featured on Home Page" on Manage Schedule (nothing shows without any) -->
            <x-featured-on-home-page />

            <x-study
                :studyId="$featuredStudyId"
                :date="$studyDate"
                heading="{{ __('home/study.upcoming_sunday') }}">
            </x-study>

        </div>

    @endif

    {{-- Optional full-width banner from the top of the page, behind the fixed header (e.g. Bible Studies) --}}
    {{ $hero ?? '' }}

    <main class="container mx-auto p-4 mt-4">
        {{ $slot }}
    </main>

    <x-footer />

    <x-compliance-requirement />

</body>

</html>
