<x-layout class="bg-slate-900" textColor="text-white">

    <div class="bible-container">

        {{-- Display Old Testament Books --}}
        @if ($testaments->has('ot'))
            <h2 class="text-2xl">{{ __('bible-study/index.old_testament') }}</h2>
            <div class="book-grid">
                @foreach ($testaments->get('ot') as $id => $book)

                    <div class="book-card">

                        <h3>
                            <a href="{{ url('/bible/book/' . $id) }}">
                                {{ __('bible-study/index.read') }} >
                                {{ $book['name'] }} ({{ $book['abbreviation'] }}) - {{ $book['chapters'] }} {{ Str::lower(__('bible-study/index.chapters')) }}
                            </a>
                        </h3>

                    </div>

                @endforeach
            </div>
        @endif

        <br/><br/>

        {{-- Display New Testament Books --}}
        @if ($testaments->has('nt'))
            <h2 class="text-2xl">{{ __('bible-study/index.new_testament') }}</h2>
            <div class="book-grid">
                @foreach ($testaments->get('nt') as $id => $book)

                    <div class="book-card">

                        <h3>
                            <a href="{{ url('/bible/book/' . $id) }}">
                                {{ __('bible-study/index.read') }}>
                                {{ $book['name'] }} ({{ $book['abbreviation'] }}) - {{ $book['chapters'] }} {{ Str::lower(__('bible-study/index.chapters')) }}
                            </a>
                        </h3>

                    </div>

                @endforeach
            </div>
        @endif

</x-layout>
