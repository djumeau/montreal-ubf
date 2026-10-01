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

                <!-- Media: scrolling strip with arrows (the scroll position keeps "active" in step with them).
                     Clicking an item opens the viewer: a full-screen carousel (‹ › buttons, arrow keys, swipe; Esc or ✕ closes) -->
                @if ($media->isNotEmpty())
                    <section x-data="{
                        active: 0,
                        count: {{ $media->count() }},
                        items: @js($media->map(fn ($item) => ['url' => $item->url, 'video' => $item->isVideo(), 'name' => $item->display_name])->values()),
                        viewerOpen: false,
                        current: 0,
                        touchX: null,
                        go(index) {
                            this.active = Math.max(0, Math.min(this.count - 1, index));
                            this.$refs.track.scrollTo({ left: this.$refs.track.children[this.active].offsetLeft, behavior: 'smooth' });
                        },
                        sync() {
                            const track = this.$refs.track;
                            const max = track.scrollWidth - track.clientWidth;
                            this.active = max > 0 ? Math.round(track.scrollLeft / max * (this.count - 1)) : 0;
                        },
                        openViewer(index) {
                            this.current = index;
                            this.viewerOpen = true;
                            document.body.classList.add('overflow-hidden');
                            this.$nextTick(() => this.$refs.close.focus());
                        },
                        closeViewer() {
                            this.viewerOpen = false;
                            document.body.classList.remove('overflow-hidden');
                            this.go(this.current); // Strip follows the last item viewed
                        },
                        step(offset) {
                            this.current = (this.current + offset + this.count) % this.count; // Wraps around
                        },
                        swipe(endX) {
                            if (this.touchX !== null && Math.abs(endX - this.touchX) > 50) this.step(endX < this.touchX ? 1 : -1);
                            this.touchX = null;
                        },
                    }">
                        <h3 class="border-l-4 border-blue-600 pl-3 text-2xl font-bold">{{ __('events/index.media') }}
                        </h3>

                        <div class="relative mt-3">
                            <div x-ref="track" @scroll.debounce.100ms="sync()"
                                class="relative flex gap-3 overflow-x-auto snap-x snap-mandatory scroll-smooth scrollbar-none">
                                @foreach ($media as $item)
                                    <button type="button" @click="openViewer({{ $loop->index }})"
                                        aria-label="{{ __('events/index.view_media', ['name' => $item->display_name]) }}"
                                        class="group relative snap-start shrink-0 w-48 md:w-56 aspect-4/3 rounded-sm overflow-hidden border border-slate-700 bg-black cursor-zoom-in">
                                        @if ($item->isVideo())
                                            <video src="{{ $item->url }}" preload="metadata" muted playsinline
                                                class="w-full h-full object-cover pointer-events-none"></video>
                                            <i class="fa-solid fa-circle-play absolute inset-0 m-auto size-fit text-4xl text-white/90 drop-shadow"></i>
                                        @else
                                            <img src="{{ $item->url }}" alt="{{ $item->display_name }}"
                                                loading="lazy" class="w-full h-full object-cover group-hover:opacity-90">
                                        @endif
                                    </button>
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

                        <!-- Media Viewer: moved to the end of <body> so it covers the fixed header -->
                        <template x-teleport="body">
                            <div x-show="viewerOpen" x-cloak x-transition.opacity
                                role="dialog" aria-modal="true" aria-label="{{ __('events/index.media') }}"
                                @keydown.escape.window="viewerOpen && closeViewer()"
                                @keydown.arrow-left.window="viewerOpen && step(-1)"
                                @keydown.arrow-right.window="viewerOpen && step(1)"
                                @touchstart="touchX = $event.changedTouches[0].clientX"
                                @touchend="swipe($event.changedTouches[0].clientX)"
                                class="fixed inset-0 z-100 flex items-center justify-center bg-black/90 p-4 md:p-12">

                                <!-- Clicking the dark background closes; clicks on the item itself don't -->
                                <div class="absolute inset-0" @click="closeViewer()"></div>

                                <template x-if="viewerOpen">
                                    <div class="relative max-w-full max-h-full">
                                        <template x-if="items[current].video">
                                            <video :src="items[current].url" controls autoplay playsinline
                                                class="max-w-full max-h-[85vh] rounded-sm"></video>
                                        </template>
                                        <template x-if="!items[current].video">
                                            <img :src="items[current].url" :alt="items[current].name"
                                                class="max-w-full max-h-[85vh] object-contain rounded-sm">
                                        </template>
                                    </div>
                                </template>

                                <button type="button" x-ref="close" @click="closeViewer()" aria-label="{{ __('events/index.close') }}"
                                    class="absolute top-4 right-4 size-11 rounded-full border-2 border-white bg-slate-900/70 hover:bg-slate-900 text-xl cursor-pointer">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>

                                @if ($media->count() > 1)
                                    <button type="button" @click="step(-1)" aria-label="{{ __('events/index.previous') }}"
                                        class="absolute left-2 md:left-6 top-1/2 -translate-y-1/2 size-11 rounded-full border-2 border-white bg-slate-900/70 hover:bg-slate-900 cursor-pointer">
                                        <i class="fa-solid fa-arrow-left"></i>
                                    </button>
                                    <button type="button" @click="step(1)" aria-label="{{ __('events/index.next') }}"
                                        class="absolute right-2 md:right-6 top-1/2 -translate-y-1/2 size-11 rounded-full border-2 border-white bg-slate-900/70 hover:bg-slate-900 cursor-pointer">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                @endif
                            </div>
                        </template>
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

                <!-- Attachments: documents in the current language; the whole card is hidden when there are none -->
                @if ($documents->isNotEmpty())
                    <section class="bg-slate-800/60 border border-slate-700 rounded-sm p-4">
                        <h3 class="border-l-4 border-blue-600 pl-3 text-xl font-bold">
                            {{ __('events/index.attachments_heading', ['count' => $documents->count()]) }}
                        </h3>

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
                    </section>
                @endif

                <!-- Location map: only for addresses Google Maps can find (not online events) -->
                @if ($event->hasMap())
                    <section class="bg-slate-800/60 border border-slate-700 rounded-sm p-4">
                        <h3 class="border-l-4 border-blue-600 pl-3 text-xl font-bold">
                            {{ __('events/index.location_heading') }}</h3>

                        <!-- The Google Map only loads once cookies are accepted on the consent banner (see compliance-requirement);
                             refused or not answered yet: a "Feature disabled" placeholder, and nothing is requested from Google -->
                        <div x-data="{ consent: null }"
                            x-init="try { consent = localStorage.getItem('privacy_consent_given') } catch (e) {}"
                            @privacy-consent-updated.window="consent = $event.detail">

                            <template x-if="consent === 'accept'">
                                <iframe src="{{ $event->maps_embed_url }}" title="{{ $event->location_name }}"
                                    class="mt-3 w-full h-56 rounded-sm border-0" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </template>

                            <template x-if="consent !== 'accept'">
                                <div role="img" aria-label="{{ __('events/index.map_disabled') }}"
                                    class="mt-3 w-full h-56 flex flex-col items-center justify-center gap-2 px-4 text-center rounded-sm border border-dashed border-slate-500 bg-slate-900 text-slate-300">
                                    <i class="fa-solid fa-map-location-dot text-4xl text-slate-500" aria-hidden="true"></i>
                                    <span class="text-lg font-bold text-slate-100">{{ __('events/index.map_disabled') }}</span>
                                    <span class="text-xs">{{ __('events/index.map_disabled_hint') }}</span>
                                </div>
                            </template>
                        </div>

                        <a href="{{ $event->maps_url }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 mt-3 text-sm text-sky-400 hover:text-sky-300 hover:underline">
                            {{ __('events/index.open_in_maps') }}
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        </a>
                    </section>
                @endif
            </div>
        </div>

        <x-events::back-button class="{{ $linkButtonClass }} mt-8"
            icon="fa-solid fa-arrow-left">{{ __('events/index.back_to_events') }}</x-events::back-button>

    </div>

</x-layout>
