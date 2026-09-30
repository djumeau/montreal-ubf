@props([
    'start', // Sunday of the week shown (Carbon)
])

@php
    // Sunday to Saturday: short weekday without its period ("dim." → "Dim") and day + month
    $days = collect(range(0, 6))->map(fn ($offset) => $start->copy()->addDays($offset));
    $weekdayName = fn ($day) => ucfirst(rtrim($day->isoFormat('ddd'), '.'));
@endphp

<!-- Week Grid Header: a time column, then one column per day;
     today's column header white on blue (≈ 5.2:1, WCAG AA) with a 1px white outline,
     drawn with borders so it lines up with the column's outline in hour-rows -->

<thead {{ $attributes }}>
    <tr class="bg-slate-800">
        <th class="w-14"><span class="sr-only">{{ __('bible-study-schedule/index.time') }}</span></th>
        @foreach ($days as $day)
            <th scope="col" class="py-2 px-1 font-normal {{ $day->isToday() ? 'bg-blue-600 text-white border border-white' : 'border-l border-slate-700 text-slate-100' }}"
                @if ($day->isToday()) aria-current="date" @endif>
                <div class="text-base font-medium">{{ $weekdayName($day) }}</div>
                <div>{{ $day->isoFormat(__('bible-study-schedule/index.day_format')) }}</div>
            </th>
        @endforeach
    </tr>
</thead>
