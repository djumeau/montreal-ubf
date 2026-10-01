@props([
    'start', // Sunday of the week shown (Carbon)
    'studies' => collect(), // Group Bible studies of the week (Events), drawn in the cell of their starting hour
    'editable' => false, // Manage Schedule: blocks open the Event modal to edit, cells open it to add (openEdit() / openAdd() of the page)
    'from' => 8, // First hour shown (8 h 00)
    'to' => 22, // Grid ends at this hour (22 h 00): the last row is 21 h – 22 h
])

@php
    $days = collect(range(0, 6))->map(fn ($offset) => $start->copy()->addDays($offset));
    $hours = range($from, $to - 1);

    // Studies by "date|hour"; earlier / later ones go in the first / last row (see study-block)
    $studiesByCell = $studies->groupBy(fn ($study) => $study->start_date->toDateString() . '|'
        . min(max($study->start_date->hour, $from), $to - 1));

    // Studies at the same time are drawn side by side: each day, a study takes the first column free at its start,
    // and every study of a group of overlapping ones gets the same width (the day's width / the group's columns).
    // $layout: study id => ['column' => place from the left, 'columns' => columns of its group]
    $layout = [];
    foreach ($studies->groupBy(fn ($study) => $study->start_date->toDateString()) as $dayStudies) {
        $spans = $dayStudies->map(fn ($study) => [$study->id, ...$study->scheduleSpan($from, $to)])
            ->sortBy(fn ($span) => $span[1]->getTimestamp())->values();

        $group = []; // Ids of the overlapping studies being placed
        $columnEnds = []; // End of the last study in each column of the group
        foreach ($spans as [$id, $spanStart, $spanEnd]) {
            // Starts once every column is free: the group is complete, a new one begins
            if ($columnEnds && $spanStart->gte(max($columnEnds))) {
                foreach ($group as $groupId) {
                    $layout[$groupId]['columns'] = count($columnEnds);
                }
                [$group, $columnEnds] = [[], []];
            }

            $column = collect($columnEnds)->search(fn ($end) => $end->lte($spanStart));
            $column = $column === false ? count($columnEnds) : $column;

            $columnEnds[$column] = $spanEnd;
            $group[] = $id;
            $layout[$id] = ['column' => $column, 'columns' => 1];
        }
        foreach ($group as $groupId) {
            $layout[$groupId]['columns'] = count($columnEnds);
        }
    }
@endphp

<!-- Week Grid Hours: one row per hour, labelled in the time column ("8 h" / "8 AM");
     today's column lightly tinted with a 1px white outline (sides and bottom) below its header.
     Tables collapse borders, so today's white right border wins over the next day's grey left border -->

<tbody {{ $attributes }}>
    @foreach ($hours as $hour)
        <tr class="h-12 border-t border-slate-700">
            <th scope="row" class="align-top pt-1 pr-2 text-right text-xs font-normal text-slate-400 whitespace-nowrap">
                {{ $start->copy()->setTime($hour, 0)->isoFormat(__('bible-study-schedule/index.hour_format')) }}
            </th>
            @foreach ($days as $day)
                <td class="relative {{ $day->isToday()
                    ? 'bg-blue-600/10 border-x border-white' . ($loop->parent->last ? ' border-b' : '')
                    : 'border-l border-slate-700' }}">
                    @if ($editable)
                        <!-- Fills the cell, under its blocks: opens the Event modal on this day and hour -->
                        <button type="button" @click="openAdd('{{ $day->toDateString() }}', '{{ sprintf('%02d:00', $hour) }}')"
                            class="absolute inset-0 w-full cursor-pointer hover:bg-white/10 focus-visible:bg-white/10 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-white"
                            aria-label="{{ __('dashboard/index.add_event_at') }} {{ $day->copy()->setTime($hour, 0)->isoFormat(__('bible-study-schedule/index.cell_format')) }}"></button>
                    @endif
                    @foreach ($studiesByCell->get($day->toDateString() . '|' . $hour, []) as $study)
                        <x-study-schedule::study-block :study="$study" :editable="$editable" :from="$from" :to="$to"
                            :column="$layout[$study->id]['column']" :columns="$layout[$study->id]['columns']" />
                    @endforeach
                </td>
            @endforeach
        </tr>
    @endforeach
</tbody>
