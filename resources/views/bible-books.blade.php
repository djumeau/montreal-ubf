<x-layout>

    <div class="bible-container">

        {{-- Display Old Testament Books --}}
        @if ($testaments->has('ancien'))
            <h2>Old Testament</h2>
            <div class="book-grid">
                @foreach ($testaments->get('ancien') as $id => $book)
                    <div class="book-card">
                        <h3>{{ $book['name'] }} ({{ $book['abbreviation'] }})</h3>
                        <p>{{ $book['chapters'] }} Chapters</p>
                        <a href="{{ url('/bible/book/' . $id) }}">Read</a>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Display New Testament Books --}}
        @if ($testaments->has('nt'))
            <h2>New Testament</h2>
            <div class="book-grid">
                @foreach ($testaments->get('nt') as $id => $book)
                    <div class="book-card">
                        <h3>{{ $book['name'] }} ({{ $book['abbreviation'] }})</h3>
                        <p>{{ $book['chapters'] }} Chapters</p>
                        <a href="{{ url('/bible/book/' . $id) }}">Read</a>
                    </div>
                @endforeach
            </div>
        @endif

</x-layout>
