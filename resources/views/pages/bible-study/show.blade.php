@php
    $locale = session('locale', app()->getLocale());
    $locale = substr($locale, 0, 2);
@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('bible-study/index.title') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8'>{{ __('bible-study/index.title') }}</h1>

    <p>

        @php
            $titleData = json_decode($biblestudy->title);
            $title = data_get($titleData, $locale, 'Default Feedback');
        @endphp

    <h2>{{ $title }}</h2>
    <p>{{ $biblestudy->book_id }} {{ $biblestudy->bible_passage }}</p>

</x-layout>
