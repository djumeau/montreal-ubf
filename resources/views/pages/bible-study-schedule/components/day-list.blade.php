@props([
    'start', // Sunday of the week shown (Carbon)
    'studies' => collect(), // Events of the week, in start order
    'editable' => false, // Manage Schedule: "+" adds an event that day, blocks open the Event modal (openAdd() / openEdit() of the page)
    'addTime' => '19:00', // Start time proposed by "+"
])

@php
    $days = collect(range(0, 6))->map(fn ($offset) => $start->copy()->addDays($offset));
    $studiesByDay = $studies->groupBy(fn ($study) => $study->start_date->toDateString());
@endphp

<!-- Day List (List view, and phones in Week view): Sunday to Saturday, one item per day —
     a header with the date, its number of events and "+" on Manage Schedule, then its events one under the other -->

<ol {{ $attributes->merge(['class' => 'space-y-3']) }}>
    @foreach ($days as $day)
        @php
            $dayStudies = $studiesByDay->get($day->toDateString(), collect());
            $dayLabel = ucfirst($day->isoFormat(__('bible-study-schedule/index.list_day_format')));
        @endphp

        <li class="border rounded-sm overflow-hidden {{ $day->isToday() ? 'border-white' : 'border-slate-700' }}"
            @if ($day->isToday()) aria-current="date" @endif>

            <!-- Header: today's white on blue, as on the week grid -->
            <div class="flex items-center justify-between gap-3 px-3 py-2 {{ $day->isToday() ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-100' }}">
                <h3 class="font-medium">
                    {{ $dayLabel }}
                    <span class="font-normal {{ $day->isToday() ? '' : 'text-slate-300' }}" aria-hidden="true">({{ $dayStudies->count() }})</span>
                    <span class="sr-only">, {{ trans_choice('bible-study-schedule/index.event_count', $dayStudies->count(), ['count' => $dayStudies->count()]) }}</span>
                </h3>

                @if ($editable)
                    <button type="button" @click="openAdd('{{ $day->toDateString() }}', '{{ $addTime }}')"
                        class="grid place-items-center size-8 shrink-0 border border-white rounded-sm bg-slate-900 hover:bg-slate-700 text-white cursor-pointer"
                        aria-label="{{ __('dashboard/index.add_event_at') }} {{ $dayLabel }}">
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                    </button>
                @endif
            </div>

            @if ($dayStudies->isNotEmpty())
                <div class="p-2 space-y-2">
                    @foreach ($dayStudies as $study)
                        <x-study-schedule::study-block :study="$study" :editable="$editable" stacked />
                    @endforeach
                </div>
            @endif
        </li>
    @endforeach
</ol>
