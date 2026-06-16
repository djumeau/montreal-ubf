<x-layout>

    <x-slot name="title">{{ $current_book }} {{ $current_chapter }}</x-slot>

    <h1>{{ $current_book }}</h1>

    <h2>Chapter {{ $current_chapter }}</h2>
    <p>Verses: {{ $verses }}</p>

</x-layout>
