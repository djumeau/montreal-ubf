@php
    $locale = session('locale', app()->getLocale());
    $locale = substr($locale, 0, 2);
@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('bible-study/index.title') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8 pt-18'>{{ __('bible-study/index.title') }}</h1>

</x-layout>
