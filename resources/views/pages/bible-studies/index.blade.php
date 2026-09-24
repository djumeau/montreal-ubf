@php
    $isFrench = app()->getLocale() === 'fr_CA';

    // Search panel filters; any of them set shows the "Clear filters" link
    $hasFilters = $search !== '' || $currentSeries || $currentBook;

    // Series and books as "Name (study count)" in the current locale; no count when there are none
    $seriesName = fn ($series) => $isFrench ? $series->name_fr : $series->name_en;
    $bookName = fn ($book) => $isFrench ? $book->name_fr : $book->name_en;
    $withCount = fn ($name) => fn ($item) => $name($item) . ($item->bible_studies_count ? " ({$item->bible_studies_count})" : '');

    // Options without studies are greyed out and can't be picked (they would show an empty list)
    $emptySeriesIds = $seriesList->where('bible_studies_count', 0)->pluck('id')->all();
    $emptyBookIds = $books->where('bible_studies_count', 0)->pluck('id')->all();

    $seriesFilterOptions = ['' => __('dashboard/index.all_series')]
        + $seriesList->mapWithKeys(fn ($series) => [$series->id => $withCount($seriesName)($series)])->all();

    // Books grouped by testament, in canonical order
    $bookFilterOptions = ['' => __('dashboard/index.all_books')];
    foreach (['ot' => 'old_testament', 'nt' => 'new_testament'] as $testament => $groupKey) {
        $bookFilterOptions[__('bible-study/index.' . $groupKey)] = $books->where('testament', $testament)
            ->mapWithKeys(fn ($book) => [$book->id => $withCount($bookName)($book)])
            ->all();
    }
@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('bible-study/index.resources_title') }}</x-slot>

    <!-- Hero Banner: full width from the top of the page, behind the fixed header (pt-16 keeps the text below it).
         Filtered series' desktop image, else the default series image -->
    <x-slot name="hero">
        <section class="relative bg-cover bg-center h-72 md:h-96 pt-16 flex items-center justify-center"
            style="background-image: url('{{ $heroImage }}')">

            <div class="absolute inset-0 bg-slate-900/60"></div>

            <div class="relative z-10 text-center px-4 -translate-y-4">
                <h1 class="text-3xl md:text-5xl font-bold drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                    {{ __('bible-study/index.resources_title') }}
                </h1>
                <p class="mt-2 text-lg md:text-xl text-slate-100 drop-shadow-[0_1px_3px_rgba(0,0,0,0.8)]">
                    {{ __('bible-study/index.subtitle') }}
                </p>
                <div class="mx-auto mt-4 h-0.5 w-14 bg-white/80"></div>
            </div>
        </section>
    </x-slot>

    <!-- Search Panel: overlaps the bottom of the banner (pulled up past <main>'s top margin and padding) -->
    <div class="relative z-10 -mt-20 mx-2 md:mx-6 bg-slate-800 border border-slate-700 rounded-sm shadow-xl p-4">

        <!-- Same behaviour as Manage Studies: GET form, so results are bookmarkable and survive pagination.
             Only one filter applies at a time:
             - Choosing a series clears the search text and sets the book back to "All books".
             - Choosing a book clears the search text and sets the series back to "All series".
             - Searching (button or Enter) sets series and book back to "All".
             @submit only fires for the button / Enter, since $el.submit() from the selects skips the submit event -->
        <form action="{{ request()->url() }}" method="GET" x-data
            @change="
                if ($event.target.name === 'series') { $el.elements.q.value = ''; $el.elements.book.value = ''; }
                if ($event.target.name === 'book') { $el.elements.q.value = ''; $el.elements.series.value = ''; }
                if ($event.target.tagName === 'SELECT') $el.submit();
            "
            @submit="$el.elements.series.value = ''; $el.elements.book.value = ''"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-x-3 items-end">

            <!-- Search text and button; label styled like the select labels, button lines up with the input -->
            <div class="md:col-span-2 lg:col-span-6 flex flex-col sm:flex-row sm:items-end gap-3 mb-4">
                <div class="flex-1">
                    <label for="search_q" class="block text-sm font-medium text-slate-100 mb-1.5">
                        {{ __('dashboard/index.search_studies_placeholder') }}
                    </label>

                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 pointer-events-none"></i>
                        <input type="search" id="search_q" name="q" value="{{ $search }}"
                            class="w-full shadow appearance-none border border-slate-300 rounded-sm py-2 pl-9 pr-2 leading-tight bg-slate-900 focus:outline-none focus:shadow-outline text-sm">
                    </div>
                </div>

                <!-- leading-tight + transparent border: same height as the input and the selects -->
                <button type="submit"
                    class="px-5 py-2 leading-tight border border-transparent bg-sky-900 hover:bg-sky-950 text-white text-sm font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                    {{ __('dashboard/index.search') }}
                </button>
            </div>

            <x-inputs.select class="lg:col-span-3" id="filter_series" name="series" :value="$currentSeries?->id"
                :options="$seriesFilterOptions" :disabled="$emptySeriesIds" :label="__('dashboard/index.study_series')" />

            <x-inputs.select class="lg:col-span-3" id="filter_book" name="book" :value="$currentBook?->id"
                :options="$bookFilterOptions" :disabled="$emptyBookIds" :label="__('dashboard/index.bible_book')" />
        </form>

        <!-- Result count, with "Clear filters" when any filter is set -->
        <div class="flex items-center justify-between gap-3 text-sm">
            <p class="text-slate-300">
                {{ trans_choice('bible-study/index.studies_found', $studies->total(), ['count' => $studies->total()]) }}
            </p>

            @if ($hasFilters)
                <a href="{{ request()->url() }}" class="text-sky-400 hover:text-sky-300 hover:underline">
                    {{ __('dashboard/index.clear_filters') }}
                </a>
            @endif
        </div>
    </div>

    <!-- Study Cards: 1 / 2 / 4 per row, 12 per page -->
    <div class="mt-6 px-2 md:px-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse ($studies as $study)
            <x-study-card :study="$study" />
        @empty
            <p class="col-span-full py-10 text-center text-slate-400">{{ __('dashboard/index.no_studies') }}</p>
        @endforelse
    </div>

    @if ($studies->hasPages())
        <div class="mt-6 px-2 md:px-6">
            {{ $studies->links('pagination.dashboard') }}
        </div>
    @endif

</x-layout>
