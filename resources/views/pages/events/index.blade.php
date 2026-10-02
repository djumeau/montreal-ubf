@php
    // Lists shown (null = not selected), only when they have results: upcoming soonest first (up arrow), past latest first (down arrow)
    // 'id' is the anchor the pagination links jump back to (see EventController::paginate)
    $sections = collect([
        ['id' => 'upcoming', 'title' => __('events/index.upcoming_events'), 'events' => $upcoming, 'arrow' => 'fa-arrow-up'],
        ['id' => 'past', 'title' => __('events/index.past'), 'events' => $past, 'arrow' => 'fa-arrow-down'],
    ])->filter(fn ($section) => $section['events']?->isNotEmpty());

    $total = ($upcoming?->total() ?? 0) + ($past?->total() ?? 0);

    // Date column, two lines in the current locale (e.g. "14 sept. 2026" / "lun. 19 h 00 – 21 h 00"):
    // over several days, start date – / end date; otherwise the date, then the weekday and time(s)
    $dateFormat = __('events/index.date_format');
    $timeFormat = __('events/index.time_format');
    $dateLines = function ($event) use ($dateFormat, $timeFormat) {
        $start = $event->start_date;
        $end = $event->has_end_date ? $event->end_date : null;

        if ($end && !$end->isSameDay($start)) {
            return [$start->isoFormat($dateFormat) . ' –', $end->isoFormat($dateFormat)];
        }

        return [
            $start->isoFormat($dateFormat),
            $start->isoFormat('ddd ' . $timeFormat) . ($end ? ' – ' . $end->isoFormat($timeFormat) : ''),
        ];
    };
@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('events/index.title') }}</x-slot>

    <x-slot name="hero">
        <x-page-banner :title="__('events/index.title')" :subtitle="__('events/index.subtitle')"
            desktop='storage/images/events/events_01-desktop.jpg'
            mobile='storage/images/events/events_01-mobile.jpg' />
    </x-slot>

    <!-- Filter Panel: overlaps the bottom of the banner (pulled up past <main>'s top margin and padding) -->
    <div class="relative z-10 -mt-20 mx-2 md:mx-6 bg-slate-800 border border-slate-700 rounded-sm shadow-xl p-4">

        <!-- GET form, so results are bookmarkable. Changing "Show" submits right away and keeps the search text;
             the button / Enter searches within the selected list -->
        <form action="{{ request()->url() }}" method="GET" x-data
            @change="if ($event.target.tagName === 'SELECT') $el.submit()"
            class="grid grid-cols-1 lg:grid-cols-12 gap-x-3 items-end">

            <x-inputs.select class="lg:col-span-4" id="filter_show" name="show" :value="$show"
                :options="['all' => __('events/index.all'), 'upcoming' => __('events/index.upcoming_events'), 'past' => __('events/index.past')]"
                :label="__('events/index.show')" />

            <!-- Search text and button; label styled like the select label, button lines up with the input -->
            <div class="lg:col-span-8 flex flex-col sm:flex-row sm:items-end gap-3 mb-4">
                <div class="flex-1">
                    <label for="search_q" class="block text-sm font-medium text-slate-100 mb-1.5">
                        {{ __('events/index.search_placeholder') }}
                    </label>

                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 pointer-events-none"></i>
                        <input type="search" id="search_q" name="q" value="{{ $search }}"
                            class="w-full shadow appearance-none border border-slate-300 rounded-sm py-2 pl-9 pr-2 leading-tight bg-slate-900 focus:outline-none focus:shadow-outline text-sm">
                    </div>
                </div>

                <!-- leading-tight + transparent border: same height as the input and the select -->
                <button type="submit"
                    class="px-5 py-2 leading-tight border border-transparent bg-sky-900 hover:bg-sky-950 text-white text-sm font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                    {{ __('dashboard/index.search') }}
                </button>
            </div>
        </form>

        <!-- Result count, with "Clear filters" (back to all events) when a search is set or one list is picked -->
        <div class="flex items-center justify-between gap-3 text-sm">
            <p class="text-slate-300">
                {{ trans_choice('events/index.events_found', $total, ['count' => $total]) }}
            </p>

            @if ($search !== '' || $show !== 'all')
                <a href="{{ request()->url() }}" class="text-sky-400 hover:text-sky-300 hover:underline">
                    {{ __('dashboard/index.clear_filters') }}
                </a>
            @endif
        </div>
    </div>

    @foreach ($sections as $section)
        <!-- Events list: title with a blue accent bar, then the events as cards (below md) or a table (md up), 5 per page -->
        <section id="{{ $section['id'] }}" class="mt-8 mx-2 md:mx-6 scroll-mt-24">
            <h2 class="border-l-4 border-blue-600 pl-3 text-2xl md:text-3xl font-bold">
                {{ $section['title'] }}
            </h2>

            <!-- Event Cards, below md (1 / 2 per row): image with the category badge, then the same details as the table rows -->
            <div class="md:hidden mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach ($section['events'] as $event)
                    @php [$dateLine, $timeLine] = $dateLines($event); @endphp

                    <article class="flex flex-col bg-slate-800 border border-slate-700 rounded-sm overflow-hidden text-sm">
                        <div class="relative">
                            <img src="{{ $event->imageUrl('mobile') }}" alt="{{ $event->current_title }}" loading="lazy"
                                class="w-full h-40 object-cover">
                            <span class="absolute top-2 left-2 inline-block whitespace-nowrap px-3 py-0.5 text-xs font-medium border rounded-full shadow {{ $event->category->solidBadgeClasses() }}">
                                {{ $event->category->label() }}
                            </span>
                        </div>

                        <div class="flex flex-col flex-1 gap-3 p-4">
                                                        <!-- Title, then the linked Bible study when there is one (e.g. a Sunday service): its title and passage -->
                            <div>
                                <h3 class="text-lg font-semibold text-slate-100 leading-snug">{{ $event->current_title }}</h3>
                                @if ($event->bibleStudy)
                                    <p class="mt-1 text-slate-300">
                                        <i class="fa-solid fa-book-bible mr-1" aria-hidden="true"></i>
                                        {{ $event->bibleStudy->current_title }}
                                        @if ($event->bibleStudy->display_passage)
                                            <span class="whitespace-nowrap">· {{ $event->bibleStudy->display_passage }}</span>
                                        @endif
                                    </p>
                                @endif
                            </div>

                            <!-- Recurring events show a repeat icon next to their first date -->
                            <div class="flex items-start gap-3">
                                <i class="fa-regular fa-calendar w-4 mt-0.5 text-center text-base text-slate-300"></i>
                                <div class="text-slate-100 leading-snug">
                                    <div>
                                        {{ $dateLine }}
                                        @if ($event->recurring)
                                            <i class="fa-solid fa-repeat ml-1 text-xs text-slate-400" title="{{ __('events/index.recurring') }}"></i>
                                            <span class="sr-only">{{ __('events/index.recurring') }}</span>
                                        @endif
                                    </div>
                                    <div>{{ $timeLine }}</div>
                                </div>
                            </div>

                            <!-- Video icon for online Bible studies, map pin otherwise; links to Google Maps when it has an address -->
                            <div class="flex items-start gap-3">
                                <i class="fa-solid {{ $event->category === \App\Enums\EventCategory::GBS_ONLINE ? 'fa-video' : 'fa-location-dot' }} w-4 mt-0.5 text-center text-base text-slate-300"></i>
                                @if ($event->maps_url)
                                    <a href="{{ $event->maps_url }}" target="_blank" rel="noopener noreferrer"
                                        class="text-sky-400 hover:text-sky-300 hover:underline">{{ $event->location_name }}</a>
                                @else
                                    <span class="text-slate-100">{{ $event->location_name }}</span>
                                @endif
                            </div>

                            <!-- Attachments count and More info, kept at the bottom of the card -->
                            <div class="flex items-center justify-between gap-3 mt-auto pt-3 border-t border-slate-700">
                                <div title="{{ __('events/index.column_attachments') }}">
                                    <i class="fa-solid fa-paperclip mr-2 text-slate-300"></i>
                                    <span class="sr-only">{{ __('events/index.column_attachments') }}</span>
                                    <span class="text-slate-100">{{ $event->documents_count }}</span>
                                </div>

                                <a href="{{ route(__('nav.events.name') . '.show', $event) }}"
                                    class="inline-flex items-center gap-2 whitespace-nowrap px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-sm">
                                    {{ __('events/index.more_info') }}
                                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Table headings from md up (phones get the cards above); the arrow on Date and time shows the sort order.
                 Last column is left blank for the "More info" buttons -->
            <div class="hidden md:block mt-4 border border-slate-700 rounded-sm">
                <!-- Fixed layout: the same column widths in every list (Upcoming, Past), whatever the content.
                     Title and Location share the space left. So the table always fits without scrolling sideways,
                     Category only shows from lg up, Image and Attachments from xl up -->
                <table class="w-full table-fixed text-left text-sm">
                    <colgroup>
                        <col class="hidden xl:table-column w-28">  {{-- Image --}}
                        <col>                                      {{-- Title --}}
                        <col class="hidden lg:table-column w-44">  {{-- Category --}}
                        <col class="w-56">                         {{-- Date and time --}}
                        <col>                                      {{-- Location --}}
                        <col class="hidden xl:table-column w-36">  {{-- Attachments --}}
                        <col class="w-40">                         {{-- More info --}}
                    </colgroup>
                    <thead>
                        <tr class="bg-slate-800 text-slate-100">
                            <th class="hidden xl:table-cell py-2 px-3 font-medium">{{ __('events/index.column_image') }}</th>
                            <th class="py-2 px-3 font-medium xl:border-l border-slate-700">{{ __('events/index.column_title') }}</th>
                            <th class="hidden lg:table-cell py-2 px-3 font-medium border-l border-slate-700">{{ __('events/index.column_category') }}</th>
                            <th class="py-2 px-3 font-medium border-l border-slate-700 whitespace-nowrap">
                                {{ __('events/index.column_date') }}
                                <i class="fa-solid {{ $section['arrow'] }} ml-1 text-xs"></i>
                            </th>
                            <th class="py-2 px-3 font-medium border-l border-slate-700">{{ __('events/index.column_location') }}</th>
                            <th class="hidden xl:table-cell py-2 px-3 font-medium border-l border-slate-700">{{ __('events/index.column_attachments') }}</th>
                            <th class="py-2 px-3 border-l border-slate-700"><span class="sr-only">{{ __('events/index.column_actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($section['events'] as $event)
                            @php [$dateLine, $timeLine] = $dateLines($event); @endphp

                            <tr class="border-t border-slate-700 align-middle odd:bg-black/20">
                                <td class="hidden xl:table-cell p-1">
                                    <img src="{{ $event->imageUrl('square') }}" loading="lazy" alt="{{ $event->current_title }}"
                                        class="w-20 h-12 rounded-sm object-cover">
                                </td>

                                                                <!-- Title, then the linked Bible study when there is one (e.g. a Sunday service): its title and passage -->
                                <td class="py-2 px-3 text-slate-100">
                                    {{ $event->current_title }}
                                    @if ($event->bibleStudy)
                                        <div class="mt-1 text-xs text-slate-300">
                                            <i class="fa-solid fa-book-bible mr-1" aria-hidden="true"></i>
                                            {{ $event->bibleStudy->current_title }}
                                            @if ($event->bibleStudy->display_passage)
                                                <span class="whitespace-nowrap">· {{ $event->bibleStudy->display_passage }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <td class="hidden lg:table-cell py-2 px-3">
                                    <span class="inline-block whitespace-nowrap px-3 py-0.5 text-xs font-medium border rounded-full {{ $event->category->solidBadgeClasses() }}">
                                        {{ $event->category->label() }}
                                    </span>
                                </td>

                                <!-- Recurring events show a repeat icon next to their first date -->
                                <td class="py-2 px-3">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-regular fa-calendar text-base text-slate-300"></i>
                                        <div class="text-slate-100 leading-snug whitespace-nowrap">
                                            <div>
                                                {{ $dateLine }}
                                                @if ($event->recurring)
                                                    <i class="fa-solid fa-repeat ml-1 text-xs text-slate-400" title="{{ __('events/index.recurring') }}"></i>
                                                    <span class="sr-only">{{ __('events/index.recurring') }}</span>
                                                @endif
                                            </div>
                                            <div>{{ $timeLine }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Video icon for online Bible studies, map pin otherwise.
                                     Location name only; links to Google Maps in a new tab when it has an address -->
                                <td class="py-2 px-3">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid {{ $event->category === \App\Enums\EventCategory::GBS_ONLINE ? 'fa-video' : 'fa-location-dot' }} text-base text-slate-300"></i>
                                        @if ($event->maps_url)
                                            <a href="{{ $event->maps_url }}" target="_blank" rel="noopener noreferrer"
                                                class="text-sky-400 hover:text-sky-300 hover:underline">{{ $event->location_name }}</a>
                                        @else
                                            <span class="text-slate-100">{{ $event->location_name }}</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Attachments: documents in the current language, media not counted (see EventController) -->
                                <td class="hidden xl:table-cell py-2 px-3 whitespace-nowrap">
                                    <i class="fa-solid fa-paperclip mr-2 text-slate-300"></i>
                                    <span class="text-slate-100">{{ $event->documents_count }}</span>
                                </td>

                                <!-- More info: event details page, /events/{id} or /evenements/{id} -->
                                <td class="py-2 px-3">
                                    <a href="{{ route(__('nav.events.name') . '.show', $event) }}"
                                        class="inline-flex items-center gap-2 whitespace-nowrap px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-sm">
                                        {{ __('events/index.more_info') }}
                                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($section['events']->hasPages())
                <div class="mt-4">
                    {{ $section['events']->links('pagination.dashboard') }}
                </div>
            @endif
        </section>
    @endforeach

</x-layout>
