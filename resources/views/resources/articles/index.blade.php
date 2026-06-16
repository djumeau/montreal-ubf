<x-layout>

    <x-slot name="title">Articles</x-slot>

    <h1>Articles</h1>

    <ul>
        @foreach ($articles as $article)
            <li><a href="{{ route('home') }}">{{ $article }}</a></li>
        @endforeach
    </ul>

</x-layout>
