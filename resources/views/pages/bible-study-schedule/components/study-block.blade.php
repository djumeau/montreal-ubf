@props([
    'study', // Group Bible study, or any other event on Manage Schedule (Event)
    'from' => 8, // First hour of the grid, to place studies starting earlier at its top
    'to' => 22, // Grid end hour, to cut studies running later
    'editable' => false, // Manage Schedule: the block is a button opening the Event modal (openEdit() of the page)
    'stacked' => false, // Day list (List view and phones): full width, one under the other, instead of placed on the week grid
])

@php
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

    // Title (cut with "…" when too long for the block; the short category without one), then the contact person below,
    // then the location: its name, or "Via Zoom" / "Par Zoom" for a Zoom link (see Event::locationName)
    $title = $study->current_title ?: $study->category->label();
    $leader = $study->contact_name;
    $location = $study->location_name;

    // "Via Zoom" only links to the meeting for signed-in users with the User role or above; visitors and Guests see the words alone
    $canJoinZoom = (bool) auth()->user()?->role->atLeast(\App\Enums\Role::USER);

    // In person: group icon; online: video icon (see EventCategory::icon)
    $icon = $study->category->icon();

    $time = $startsAt->isoFormat(__('bible-study-schedule/index.time_format'));
    $endTime = $endsAt->isoFormat(__('bible-study-schedule/index.time_format'));

    // "9 h 00 – 10 h 30" when the event has an end time that day, the start alone otherwise
    $hasEndTime = $study->has_end_date && $study->end_date?->isSameDay($startsAt) && $study->end_date->gt($startsAt);
    $shownTime = $hasEndTime ? "{$time} – {$endTime}" : $time;

    // On the grid: inside its cell, at its start time and as tall as it is long; in the day list: in the flow
    $layoutClass = $stacked ? 'relative w-full text-sm' : 'absolute inset-x-1 z-10 text-xs';
    $layoutStyle = $stacked ? '' : "top: {$top}rem; height: calc({$height}rem - 2px);";
@endphp

<!-- Study Block: time, title, contact person and location; on the week grid its height follows its length -->
<{{ $editable ? 'button' : 'div' }}
    @if ($editable) type="button" @click="openEdit({{ $study->id }})" @endif
    {{ $attributes->merge(['class' => "$layoutClass flex items-start gap-2 px-2 py-1 border border-white rounded-md shadow overflow-hidden leading-tight text-left $colour"
        . ($editable ? ' border-white cursor-pointer hover:ring-2 hover:ring-white focus-visible:ring-2 focus-visible:ring-white focus:outline-none' : '')]) }}
    style="{{ $layoutStyle }} {{ $colourStyle }}"
    title="{{ $study->category->label() }} · {{ $time }} – {{ $endTime }} · {{ $study->current_title }}">
    <i class="fas {{ $icon }} mt-0.5 text-sm" aria-hidden="true"></i>
    <span class="block min-w-0">
        <span class="block font-medium">{{ $shownTime }}</span>
        <span class="block truncate" aria-hidden="true">{{ $title }}</span>
        @if ($leader)
            <span class="block truncate">{{ $leader }}</span>
        @endif
        @if ($location)
            {{-- On Manage Schedule the whole block is already a button, so no link there --}}
            @if ($study->zoom_url && $canJoinZoom && !$editable)
                <a href="{{ $study->zoom_url }}" target="_blank" rel="noopener noreferrer" class="block truncate underline hover:no-underline">{{ $location }}</a>
            @else
                <span class="block truncate">{{ $location }}</span>
            @endif
        @endif
        <span class="sr-only">{{ $study->category->label() }}, {{ $time }} – {{ $endTime }}, {{ $study->current_title }}</span>
    </span>
</{{ $editable ? 'button' : 'div' }}>
