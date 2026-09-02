<x-layout class="bg-slate-900" textColor="text-white">

    <div class="bible-container mt-20">

        <div class="bible-books">
            <h1 class="text-3xl font-bold mb-4">Bible Books</h1>

            @foreach ($bible_books as $book)
                <div class="bible-book">
                    <h2 class="text-xl font-semibold">{{ $book->name_en }} ({{ $book->abbreviation_en }})</h2>
                    <p>Testament: {{ $book->testament }}</p>
                    <p>Chapters: {{ $book->chapters }}</p>
                </div>
            @endforeach
        </div>

</x-layout>
