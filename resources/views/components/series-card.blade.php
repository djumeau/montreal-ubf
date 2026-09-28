@props([
    'series', // StudySeries with bible_studies_count loaded (see BibleStudyController::index)
])

@php
    // Opens this series' studies on the same page (English or French route)
    $url = request()->url() . '?series=' . $series->id;
@endphp

<!-- Study Series Card (links to the series' Bible studies) -->
<a href="{{ $url }}" {{ $attributes->merge(['class' => 'group flex flex-col h-full bg-slate-800 border border-slate-700 rounded-sm shadow-lg overflow-hidden hover:border-slate-400 transition-colors']) }}>

    <img src="{{ $series->imageUrl('desktop') }}" alt="{{ $series->current_name }}" loading="lazy"
        class="w-full aspect-video object-cover border-b border-slate-700">

    <div class="flex flex-col flex-1 p-4">

        <h3 class="text-lg font-bold text-slate-100 leading-snug group-hover:underline">{{ $series->current_name }}</h3>

        @if ($series->dates)
            <p class="flex items-center gap-2 mt-1 text-sm text-slate-400">
                <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                {{ $series->localized_dates }}
            </p>
        @endif

        <!-- Pushed to the bottom so cards line up -->
        <p class="mt-auto pt-4 flex items-center justify-between text-sm text-slate-300">
            <span>{{ trans_choice('bible-study/index.series_studies', $series->bible_studies_count, ['count' => $series->bible_studies_count]) }}</span>
            <i class="fa-solid fa-arrow-right text-sky-400" aria-hidden="true"></i>
        </p>

    </div>

</a>
