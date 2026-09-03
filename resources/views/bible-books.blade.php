@php

    $isFrench = app()->getLocale() === 'fr_CA';

@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{__('bible-study/index.bible_books')}}</x-slot>

    <div class="bible-container flex flex-col-2 mt-20">

        <div class="ot-books w-1/2 gap-1 p-4">
            <h1 class="text-3xl font-bold mb-4">{{__('bible-study/index.bible_books')}} - {{__('bible-study/index.old_testament')}} - {{ $ot->count() }} {{ __('bible-study/index.books') }}</h1>

            @foreach ($ot as $book)
                <div class="bible-book">
                    <p class="text-xl font-semibold">
                        @if ($isFrench)
                            {{ $book->name_fr }} ({{ $book->abbreviation_fr }}) - {{ $book->chapters }} chapîtres
                        @else
                            {{ $book->name_en }} ({{ $book->abbreviation_en }}) - {{ $book->chapters }} chapters
                        @endif
                    </p>
                </div>
            @endforeach
        </div>

        <div class="ot-books w-1/2 gap-1 p-4">
            <h1 class="text-3xl font-bold mb-4">{{__('bible-study/index.bible_books')}} - {{__('bible-study/index.new_testament')}} - {{ $nt->count() }} {{ __('bible-study/index.books') }}</h1>

            @foreach ($nt as $book)
                <div class="bible-book">
                    <p class="text-xl font-semibold">
                        @if ($isFrench)
                            {{ $book->name_fr }} ({{ $book->abbreviation_fr }}) - {{ $book->chapters }} chapîtres
                        @else
                            {{ $book->name_en }} ({{ $book->abbreviation_en }}) - {{ $book->chapters }} chapters
                        @endif
                    </p>
                </div>
            @endforeach
        </div>

</x-layout>
