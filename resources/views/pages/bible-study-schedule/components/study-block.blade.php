@props([
    'study', // Group Bible study (Event)
    'from' => 8, // First hour of the grid, to place studies starting earlier at its top
    'to' => 22, // Grid end hour, to cut studies running later
])

@php
    use App\Enums\EventCategory;

    // Placed inside the cell of its starting hour; each hour row is 3rem (h-12) tall
    $rowHeight = 3;
    $defaultMinutes = 90; // Length shown when the study has no end time

    $startsAt = $study->start_date;
    $endsAt = $study->has_end_date && $study->end_date?->isSameDay($startsAt) && $study->end_date->gt($startsAt)
        ? $study->end_date
        : $startsAt->copy()->addMinutes($defaultMinutes);

    $gridStart = $startsAt->copy()->setTime($from, 0);
    $gridEnd = $startsAt->copy()->setTime($to, 0);
    // Kept inside the grid: at least half an hour shows, also for studies outside its hours
    $shownStart = $startsAt->max($gridStart)->min($gridEnd->copy()->subMinutes(30));
    $shownEnd = $endsAt->min($gridEnd);

    $top = $shownStart->minute / 60 * $rowHeight;
    $height = max($shownStart->diffInMinutes($shownEnd), 30) / 60 * $rowHeight;

    // The colour picked by the Administrator, with white or dark text (see Event::colorText);
    // without one, one colour per leader (or per title) from the palette:
    // white text keeps ≥ 4.5:1 on each background, dark text on amber
    $palette = [
        'bg-blue-600 text-white',
        'bg-amber-400 text-slate-900',
        'bg-violet-600 text-white',
        'bg-red-600 text-white',
        'bg-green-700 text-white',
    ];
    $colour = $study->color_text
        ? ''
        : $palette[crc32((string) ($study->contact_name ?: $study->current_title)) % count($palette)];
    $colourStyle = $study->color_text ? "background-color: {$study->color}; color: {$study->color_text};" : '';

    // Leader, else the short category ("GBS In-Person" / "EBG - En Personne"): titles are too long for the block
    $leader = $study->contact_name ?: $study->category->label();

    // In person: group icon; online: video icon
    $icon = $study->category === EventCategory::GBS_ONLINE ? 'fa-video' : 'fa-users';

    $time = $startsAt->isoFormat(__('bible-study-schedule/index.time_format'));
    $endTime = $endsAt->isoFormat(__('bible-study-schedule/index.time_format'));
@endphp

<!-- Study Block: time and leader, height following its length -->
<div {{ $attributes->merge(['class' => "absolute inset-x-1 z-10 flex items-start gap-2 px-2 py-1 rounded-md shadow overflow-hidden text-xs leading-tight $colour"]) }}
    style="top: {{ $top }}rem; height: calc({{ $height }}rem - 2px); {{ $colourStyle }}"
    title="{{ $study->category->label() }} · {{ $time }} – {{ $endTime }} · {{ $study->current_title }}">
    <i class="fas {{ $icon }} mt-0.5 text-sm" aria-hidden="true"></i>
    <div class="min-w-0">
        <div class="font-medium">{{ $time }}</div>
        <div class="truncate">{{ $leader }}</div>
        <span class="sr-only">{{ $study->category->label() }}, {{ $time }} – {{ $endTime }}, {{ $study->current_title }}</span>
    </div>
</div>
