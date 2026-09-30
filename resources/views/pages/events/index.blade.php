@php
    // Lists shown (null = not selected), only when they have results: upcoming soonest first (up arrow), past latest first (down arrow)
    $sections = collect([
        ['title' => __('events/index.upcoming_events'), 'events' => $upcoming, 'arrow' => 'fa-arrow-up'],
        ['title' => __('events/index.past'), 'events' => $past, 'arrow' => 'fa-arrow-down'],
    ])->filter(fn ($section) => $section['events']?->isNotEmpty());

    $total = ($upcoming?->count() ?? 0) + ($past?->count() ?? 0);

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
        <!-- Events list: title with a blue accent bar, then the events table -->
        <section class="mt-8 mx-2 md:mx-6">
            <h2 class="border-l-4 border-blue-600 pl-3 text-2xl md:text-3xl font-bold">
                {{ $section['title'] }}
            </h2>

            <!-- Table headings from md up (phones will get cards); the arrow on Date and time shows the sort order.
                 Last column is left blank for the "More info" buttons -->
            <div class="hidden md:block mt-4 overflow-x-auto border border-slate-700 rounded-sm">
                <!-- Fixed layout: the same column widths in every list (Upcoming, Past), whatever the content.
                     Title and Location share the space left; below 64rem the table scrolls sideways -->
                <table class="w-full min-w-5xl table-fixed text-left text-sm">
                    <colgroup>
                        <col class="w-28">  {{-- Image --}}
                        <col>               {{-- Title --}}
                        <col class="w-44">  {{-- Category --}}
                        <col class="w-60">  {{-- Date and time --}}
                        <col>               {{-- Location --}}
                        <col class="w-36">  {{-- Attachments --}}
                        <col class="w-40">  {{-- More info --}}
                    </colgroup>
                    <thead>
                        <tr class="bg-slate-800 text-slate-100">
                            <th class="py-2 px-3 font-medium">{{ __('events/index.column_image') }}</th>
                            <th class="py-2 px-3 font-medium border-l border-slate-700">{{ __('events/index.column_title') }}</th>
                            <th class="py-2 px-3 font-medium border-l border-slate-700">{{ __('events/index.column_category') }}</th>
                            <th class="py-2 px-3 font-medium border-l border-slate-700 whitespace-nowrap">
                                {{ __('events/index.column_date') }}
                                <i class="fa-solid {{ $section['arrow'] }} ml-1 text-xs"></i>
                            </th>
                            <th class="py-2 px-3 font-medium border-l border-slate-700">{{ __('events/index.column_location') }}</th>
                            <th class="py-2 px-3 font-medium border-l border-slate-700">{{ __('events/index.column_attachments') }}</th>
                            <th class="py-2 px-3 border-l border-slate-700"><span class="sr-only">{{ __('events/index.column_actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($section['events'] as $event)
                            @php [$dateLine, $timeLine] = $dateLines($event); @endphp

                            <tr class="border-t border-slate-700 align-middle odd:bg-black/20">
                                <td class="p-1">
                                    <img src="{{ $event->imageUrl('square') }}" alt="{{ $event->current_title }}"
                                        class="w-20 h-12 rounded-sm object-cover">
                                </td>

                                <td class="py-2 px-3 text-slate-100">{{ $event->current_title }}</td>

                                <td class="py-2 px-3">
                                    <span class="inline-block whitespace-nowrap px-3 py-0.5 text-xs font-medium border rounded-full {{ $event->category->badgeClasses() }}">
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
                                <td class="py-2 px-3 whitespace-nowrap">
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
        </section>
    @endforeach

</x-layout>
