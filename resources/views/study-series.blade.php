<x-layout>

    <h1 class="text-3xl font-bold mb-4 mt-20 text-white">{{ __('Study Series') }}</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        @foreach ($study_series as $series)
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-xl font-semibold mb-2">{{ $series->name_en }}</h2>
                <p class="text-gray-600 mb-2">{{ $series->dates }}</p>
            </div>
        @endforeach
    </div>

</x-layout>
