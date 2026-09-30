@php
    $start = $event->start_date;
    $end = $event->has_end_date ? $event->end_date : null;
    $dateFormat = __('events/index.date_format');
    $longFormat = __('events/index.date_long_format');
    $timeFormat = __('events/index.time_format');

    // Date block in the current language:
    // - over several days: "26 juin 2026 – 28 juin 2026" / "Du vendredi 26 juin 2026 (15 h 00)" / "au dimanche 28 juin 2026 (12 h 00)"
    // - one day: "14 sept. 2026" / "lundi 19 h 00 – 21 h 00"
    if ($end && !$end->isSameDay($start)) {
        $dateLines = [
            $start->isoFormat($dateFormat) . ' – ' . $end->isoFormat($dateFormat),
            __('events/index.date_from', [
                'date' => $start->isoFormat($longFormat),
                'time' => $start->isoFormat($timeFormat),
            ]),
            __('events/index.date_to', [
                'date' => $end->isoFormat($longFormat),
                'time' => $end->isoFormat($timeFormat),
            ]),
        ];
    } else {
        $dateLines = [
            $start->isoFormat($dateFormat),
            $start->isoFormat('dddd ' . $timeFormat) . ($end ? ' – ' . $end->isoFormat($timeFormat) : ''),
        ];
    }

    $isOnline = $event->category === \App\Enums\EventCategory::GBS_ONLINE;

    $linkButtonClass =
        'inline-flex items-center gap-2 whitespace-nowrap bg-sky-900/50 hover:bg-sky-950/50 text-white font-bold mt-2 p-2 rounded outline-1 outline-white focus:shadow-outline';

    // Description and post-event summary are safe HTML (see App\Support\SafeHtml);

    // Tailwind resets list, heading and link styles, so they're set here
$richTextClass = 'mt-3 text-slate-200 leading-relaxed space-y-3
                                                [&_ul]:list-disc [&_ol]:list-decimal [&_ul]:pl-6 [&_ol]:pl-6 [&_li]:mt-1
                                                [&_h3]:text-lg [&_h3]:font-semibold [&_h4]:font-semibold
                                                [&_blockquote]:border-l-2 [&_blockquote]:border-slate-500 [&_blockquote]:pl-3 [&_blockquote]:italic
                                                [&_a]:text-sky-400 [&_a]:underline hover:[&_a]:text-sky-300';

@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ $event->current_title }}</x-slot>

    <x-slot name="hero">
        <x-page-banner :title="__('events/index.title')" :subtitle="__('events/index.subtitle')" desktop='storage/images/events/events_01-desktop.jpg'
            mobile='storage/images/events/events_01-mobile.jpg' />
    </x-slot>

    <div class="px-2 md:px-6">

        <x-events::back-button class="{{ $linkButtonClass }} py-2"
            icon="fa-solid fa-arrow-left">{{ __('events/index.back_to_events') }}</x-events::back-button>

        <!-- Event Header: desktop image beside the category, title, date and location (stacked on phones) -->
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
            <picture class="lg:col-span-5">
                <source media="(min-width: 768px)" srcset="{{ $event->imageUrl('desktop') }}">
                <img src="{{ $event->imageUrl('mobile') }}" alt="{{ $event->current_title }}"
                    class="w-full aspect-video object-cover rounded-sm border border-slate-700">
            </picture>

            <div class="lg:col-span-7 flex flex-col gap-4 lg:pb-6 border-b border-slate-700 pb-6">
                <span
                    class="self-start px-3 py-0.5 text-xs font-medium border rounded-full {{ $event->category->badgeClasses() }}">
                    {{ $event->category->label() }}
                </span>

                <h2 class="text-3xl md:text-4xl font-bold">{{ $event->current_title }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-200">
                    <!-- Date and time; recurring events add a line with a repeat icon -->
                    <div class="flex gap-1">
                        <i class="fa-regular fa-calendar text-lg text-slate-300 mt-0.5"></i>
                        <div class="leading-relaxed">
                            <div class="text-slate-100">{{ $dateLines[0] }}</div>
                            @foreach (array_slice($dateLines, 1) as $line)
                                <div>{{ $line }}</div>
                            @endforeach
                            @if ($event->recurring)
                                <div class="text-slate-400"><i
                                        class="fa-solid fa-repeat mr-1 text-xs"></i>{{ __('events/index.recurring') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Location name and address; video icon for online Bible studies -->
                    @if ($event->location)
                        <div class="flex gap-1">
                            <i
                                class="fa-solid {{ $isOnline ? 'fa-video' : 'fa-location-dot' }} text-lg text-slate-300 mt-0.5"></i>
                            <div class="leading-relaxed">
                                <div class="text-slate-100">{{ $event->location_name }}</div>
                                @if ($event->location_address)
                                    <div>{{ $event->location_address }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Event website (same for both languages), in a new tab; web addresses only -->
                @if (preg_match('~^https?://~i', (string) $event->website_url))
                    <a href="{{ $event->website_url }}" target="_blank" rel="noopener noreferrer"
                        class="{{ $linkButtonClass }} p-4 self-start">
                        {{ __('events/index.visit_website') }}
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                    </a>
                @endif
            </div>
        </div>

        <!-- Body: description, media and summary on the left; attachments and location on the right (below on phones) -->
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-8">

            <div class="lg:col-span-7 flex flex-col gap-8 min-w-0">

                @if ($event->current_description)
                    <section>
                        <h3 class="border-l-4 border-blue-600 pl-3 text-2xl font-bold">
                            {{ __('events/index.description') }}</h3>
                        <div class="{{ $richTextClass }}">{!! $event->description_html !!}</div>
                    </section>
                @endif

                <!-- Media: scrolling strip with arrows (the scroll position keeps "active" in step with them); images open full size, videos play in place -->
                @if ($media->isNotEmpty())
                    <section x-data="{
                        active: 0,
                        count: {{ $media->count() }},
                        go(index) {
                            this.active = Math.max(0, Math.min(this.count - 1, index));
                            this.$refs.track.scrollTo({ left: this.$refs.track.children[this.active].offsetLeft, behavior: 'smooth' });
                        },
                        sync() {
                            const track = this.$refs.track;
                            const max = track.scrollWidth - track.clientWidth;
                            this.active = max > 0 ? Math.round(track.scrollLeft / max * (this.count - 1)) : 0;
                        },
                    }">
                        <h3 class="border-l-4 border-blue-600 pl-3 text-2xl font-bold">{{ __('events/index.media') }}
                        </h3>

                        <div class="relative mt-3">
                            <div x-ref="track" @scroll.debounce.100ms="sync()"
                                class="relative flex gap-3 overflow-x-auto snap-x snap-mandatory scroll-smooth scrollbar-none">
                                @foreach ($media as $item)
                                    <div
                                        class="snap-start shrink-0 w-48 md:w-56 aspect-4/3 rounded-sm overflow-hidden border border-slate-700 bg-black">
                                        @if ($item->isVideo())
                                            <video src="{{ $item->url }}" controls preload="metadata"
                                                class="w-full h-full object-cover"></video>
                                        @else
                                            <a href="{{ $item->url }}" target="_blank" rel="noopener">
                                                <img src="{{ $item->url }}" alt="{{ $item->display_name }}"
                                                    loading="lazy" class="w-full h-full object-cover hover:opacity-90">
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @if ($media->count() > 1)
                                <button type="button" @click="go(active - 1)"
                                    aria-label="{{ __('events/index.previous') }}"
                                    class="absolute left-1 top-1/2 -translate-y-1/2 size-9 rounded-full border-2 border-white bg-slate-900/70 hover:bg-slate-900 cursor-pointer">
                                    <i class="fa-solid fa-arrow-left"></i>
                                </button>
                                <button type="button" @click="go(active + 1)"
                                    aria-label="{{ __('events/index.next') }}"
                                    class="absolute right-1 top-1/2 -translate-y-1/2 size-9 rounded-full border-2 border-white bg-slate-900/70 hover:bg-slate-900 cursor-pointer">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            @endif
                        </div>
                    </section>
                @endif

                @if ($event->current_post_event_summary)
                    <section>
                        <h3 class="border-l-4 border-blue-600 pl-3 text-2xl font-bold">
                            {{ __('events/index.post_event_summary') }}</h3>
                        <div class="{{ $richTextClass }}">{!! $event->post_event_summary_html !!}</div>
                    </section>
                @endif
            </div>

            <div class="lg:col-span-5 flex flex-col gap-6 min-w-0">

                <!-- Attachments: documents in the current language; the heading shows even with none -->
                <section class="bg-slate-800/60 border border-slate-700 rounded-sm p-4">
                    <h3 class="border-l-4 border-blue-600 pl-3 text-xl font-bold">
                        {{ __('events/index.attachments_heading', ['count' => $documents->count()]) }}
                    </h3>

                    @if ($documents->isNotEmpty())
                        <ul class="mt-3 max-h-72 overflow-y-auto flex flex-col gap-2 pr-1">
                            @foreach ($documents as $document)
                                <li>
                                    <a href="{{ $document->url }}" target="_blank" rel="noopener"
                                        class="flex items-center gap-3 p-3 bg-slate-900 border border-slate-700 rounded-sm hover:border-slate-500">
                                        <i class="fa-solid {{ $document->icon }} text-3xl w-8 text-center"></i>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm text-slate-100 truncate">{{ $document->display_name }}
                                            </div>
                                            <div class="text-xs text-slate-400">
                                                {{ strtoupper($document->extension) }}@if ($document->size_label)
                                                    · {{ $document->size_label }}
                                                @endif
                                            </div>
                                        </div>
                                        <i class="fa-solid fa-download text-slate-300"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                <!-- Location map: only for addresses Google Maps can find (not online events) -->
                @if ($event->hasMap())
                    <section class="bg-slate-800/60 border border-slate-700 rounded-sm p-4">
                        <h3 class="border-l-4 border-blue-600 pl-3 text-xl font-bold">
                            {{ __('events/index.location_heading') }}</h3>

                        <iframe src="{{ $event->maps_embed_url }}" title="{{ $event->location_name }}"
                            class="mt-3 w-full h-56 rounded-sm border-0" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>

                        <a href="{{ $event->maps_url }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 mt-3 text-sm text-sky-400 hover:text-sky-300 hover:underline">
                            {{ __('events/index.open_in_maps') }}
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        </a>
                    </section>
                @endif
            </div>
        </div>

        <x-events::back-button class="{{ $linkButtonClass }}"
            icon="fa-solid fa-arrow-left">{{ __('events/index.back_to_events') }}</x-events::back-button>

    </div>

</x-layout>
