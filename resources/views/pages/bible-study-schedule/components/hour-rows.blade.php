@props([
    'start', // Sunday of the week shown (Carbon)
    'from' => 8, // First hour shown (8 h 00)
    'to' => 22, // Grid ends at this hour (22 h 00): the last row is 21 h – 22 h
])

@php
    $days = collect(range(0, 6))->map(fn ($offset) => $start->copy()->addDays($offset));
    $hours = range($from, $to - 1);
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
                <td class="{{ $day->isToday()
                    ? 'bg-blue-600/10 border-x border-white' . ($loop->parent->last ? ' border-b' : '')
                    : 'border-l border-slate-700' }}"></td>
            @endforeach
        </tr>
    @endforeach
</tbody>
