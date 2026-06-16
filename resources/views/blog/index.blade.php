<x-layout>

    <x-slot name="title">Blog</x-slot>

    <h2>Subject: {{ $subject }}</h2>

    <ol>
        @foreach ($articles as $article)
            <li>{{ $article }}</li>
        @endforeach
    </ol>

</x-layout>
