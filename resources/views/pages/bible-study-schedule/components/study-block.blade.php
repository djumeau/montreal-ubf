@props([
    'study', // Group Bible study, or any other event on Manage Schedule (Event)
    'from' => 8, // First hour of the grid, to place studies starting earlier at its top
    'to' => 22, // Grid end hour, to cut studies running later
    'editable' => false, // Manage Schedule: the block is a button opening the Event modal (openEdit() of the page)
    'stacked' => false, // Day list (List view and phones): full width, one under the other, instead of placed on the week grid
    'column' => 0, // Week grid, among studies at the same time: its place from the left (see hour-rows)
    'columns' => 1, // Week grid: how many studies share the day's width at that time
])

@php
    // Placed inside the cell of its starting hour; each hour row is 3rem (h-12) tall
    $rowHeight = 3;

    $startsAt = $study->start_date;
    $endsAt = $study->scheduleEnd(); // Its end time, or a default length without one

    // Kept inside the grid: at least half an hour shows, also for studies outside its hours
    [$shownStart, $shownEnd] = $study->scheduleSpan($from, $to);

    $top = $shownStart->minute / 60 * $rowHeight;
    $height = $shownStart->diffInMinutes($shownEnd) / 60 * $rowHeight;

    // Studies at the same time share the day's width, side by side: 0.25rem off each edge of the cell, 2px between them
    $width = "(100% - 0.5rem) / {$columns}";
    $left = "calc(0.25rem + {$column} * {$width})";
    $width = $columns > 1 ? "calc({$width} - 2px)" : "calc({$width})";

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
    // Visitors (not signed in) are not shown the contact person's name
    $leader = auth()->guest() ? null : $study->contact_name;
    $location = $study->location_name;

    // "Via Zoom" only links to the meeting for a signed-in user whose role reaches the event's minimum profile;
    // everyone else (visitors included, also for a Guest minimum profile) sees the words alone
    $canJoinZoom = (bool) auth()->user()?->role->atLeast($study->minimum_profile);

    // Public schedule, visitors (not signed in): the block is a button opening the "contact us" modal (openContact() of the page),
    // whose link leads to the contact page with the subject and message filled in for this study (see ContactController::prefill)
    $inquire = !$editable && auth()->guest();
    $contactStudy = [
        'url' => url(__('nav.contact.url')) . '?study=' . $study->id,
        'title' => $study->current_title ?: $study->category->label(),
        'when' => $study->schedule_when,
        'where' => $study->schedule_where ?? '',
        // Google Maps search for a physical location (not an online study, not a Zoom link); empty otherwise
        'map' => $study->location && !$study->zoom_url && $study->category !== \App\Enums\EventCategory::GBS_ONLINE
            ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($study->location)
            : '',
        // The block's colours, for the details box of the modal: the Administrator's colour as a style, else the palette's classes
        'colourClass' => $colour,
        'colourStyle' => $colourStyle,
    ];
    $clickable = $editable || $inquire;

    // In person: group icon; online: video icon (see EventCategory::icon)
    $icon = $study->category->icon();

    $time = $startsAt->isoFormat(__('bible-study-schedule/index.time_format'));
    $endTime = $endsAt->isoFormat(__('bible-study-schedule/index.time_format'));

    // "9 h 00 – 10 h 30" when the event has an end time that day, the start alone otherwise
    $hasEndTime = $study->has_end_date && $study->end_date?->isSameDay($startsAt) && $study->end_date->gt($startsAt);
    $shownTime = $hasEndTime ? "{$time} – {$endTime}" : $time;

    // On the grid: inside its cell, at its start time and as tall as it is long; in the day list: in the flow
    $layoutClass = $stacked ? 'relative w-full text-sm' : 'absolute z-10 text-xs';
    $layoutStyle = $stacked ? '' : "top: {$top}rem; height: calc({$height}rem - 2px); left: {$left}; width: {$width};";
    $narrow = !$stacked && $columns > 1; // Sharing the day's width: the icon is left out to keep room for the text
@endphp

<!-- Study Block: time, title, contact person and location; on the week grid its height follows its length -->
<{{ $clickable ? 'button' : 'div' }}
    @if ($editable) type="button" @click="openEdit({{ $study->id }})" @endif
    @if ($inquire) type="button" @click="openContact(@js($contactStudy))" @endif
    {{ $attributes->merge(['class' => "$layoutClass flex items-start gap-2 px-2 py-1 border border-white rounded-md shadow overflow-hidden leading-tight text-left $colour"
        . ($clickable ? ' border-white cursor-pointer hover:ring-2 hover:ring-white focus-visible:ring-2 focus-visible:ring-white focus:outline-none' : '')]) }}
    style="{{ $layoutStyle }} {{ $colourStyle }}"
    title="{{ $study->category->label() }} · {{ $time }} – {{ $endTime }} · {{ $study->current_title }}">
    @unless ($narrow)
        <i class="fas {{ $icon }} mt-0.5 text-sm" aria-hidden="true"></i>
    @endunless
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
</{{ $clickable ? 'button' : 'div' }}>
