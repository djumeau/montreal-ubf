@php
    $locale = session('locale', app()->getLocale());
    $locale = substr($locale, 0, 2);
@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{__('bible-study/index.title')}}</x-slot>

     <h1 class='text-right text-4xl font-bold pb-8'>{{__('bible-study/index.title')}}</h1>

    <ul>
        @forelse($biblestudies as $biblestudy)
            @php
                $jsonData = json_decode($biblestudy->title);
                $value = data_get($jsonData, $locale, 'Default Feedback');
            @endphp

            <li>
                {{ $value }}
            </li>
        @empty
            <li>No Bible studies available.</li>
        @endforelse
    </ul>

</x-layout>
