@php
    use App\Models\Event;

    // Upcoming events ticked "Featured on Home Page" on Manage Schedule, soonest first, among those open to the viewer's role
$events = Event::where('featured_on_home_page', true)
    ->visibleTo(auth()->user())
    ->upcoming()
    ->with('bibleStudy.series')
    ->orderBy('start_date')
    ->get();

$dayFormat = __('bible-study-schedule/index.list_day_format');
@endphp

@foreach ($events as $event)
    @php
        // The day and times; an event ending on another day (e.g. a conference) also shows its last day
        $endsAnotherDay = $event->has_end_date && $event->end_date && !$event->end_date->isSameDay($event->start_date);
        $when =
            $event->schedule_when . ($endsAnotherDay ? ' – ' . ucfirst($event->end_date->isoFormat($dayFormat)) : '');
    @endphp

    <!-- Featured Event Section (mobile image below md:, desktop image from md: up) -->
    <section
        {{ $attributes->merge(['class' => 'relative bg-cover bg-center bg-no-repeat min-h-75 md:min-h-95 bg-(image:--event-bg-mobile) md:bg-(image:--event-bg-desktop)']) }}
        style="--event-bg-mobile: url('{{ $event->imageUrl('mobile') }}'); --event-bg-desktop: url('{{ $event->imageUrl('desktop') }}');">

        <div class="overlay bg-slate-900/40"></div>

        <div class="flex flex-col mx-auto text-center z-10">

            <!-- Top Left Badge -->
            <div
                class="absolute top-6 left-0 bg-black/60 backdrop-blur-xs text-amber-50 px-8 py-2.5 rounded-r-full font-sans text-sm md:text-base tracking-wide shadow-md z-5">
                {{ __('home/featured.heading') }}
            </div>

            <!-- Main Content Cluster -->
            <div class="flex-col text-center translate-y-18 md:translate-y-28 px-4 z-10">

                <!-- Event Title -->
                <h2
                    class="text-4xl md:text-6xl font-serif italic font-normal tracking-wide drop-shadow-[0_1px_4px_rgba(0,0,0,0.9)] text-white">
                    {{ $event->current_title ?: $event->category->label() }}
                </h2>

                <!-- Date and Time -->
                <p class="font-sans text-xs md:text-base tracking-wider text-white mt-4">
                    <i class="fa-solid fa-calendar mr-1" aria-hidden="true"></i>{{ $when }}
                </p>

                <!-- Location: opens Google Maps when the event has an address -->
                @if ($event->schedule_where)
                    <p class="font-sans text-xs md:text-base tracking-wider text-white mt-1">
                        <i class="fa-solid fa-map-marker mr-1" aria-hidden="true"></i>
                        @if ($event->maps_url)
                            <a href="{{ $event->maps_url }}" target="_blank" rel="noopener noreferrer"
                                class="hover:underline">{{ $event->schedule_where }}</a>
                        @else
                            {{ $event->schedule_where }}
                        @endif
                    </p>
                @endif

                <!-- More info: the event's page (group Bible studies have none) -->
                @unless ($event->isBibleStudy())
                    <a href="{{ route(__('nav.events.name') . '.show', $event) }}"
                        class="inline-block mt-5 px-5 py-2 leading-tight border border-transparent bg-sky-900 hover:bg-sky-950 text-white text-sm font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                        {{ __('events/index.more_info') }}
                    </a>
                @endunless

            </div>

        </div>

    </section>
@endforeach
