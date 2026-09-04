@php
    $locale = session('locale', app()->getLocale());
    $locale = substr($locale, 0, 2);
@endphp

<x-layout class="bg-slate-900" textColor="text-white" x-data="currentTab: 'en'
}">

    <x-slot name="title">{{ __('header.name') }} - {{ __('bible-study/index.create') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8 pt-18'>{{ __('bible-study/index.create') }}</h1>

    <div class="max-w-4xl mx-auto">

    </div>

</x-layout>
