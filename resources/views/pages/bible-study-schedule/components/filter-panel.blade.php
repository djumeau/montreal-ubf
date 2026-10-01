@props([
    'view', // week | month | list
    'start', // First day of the period shown (Carbon)
    'previous', // First day of the previous / next period (Carbon)
    'next',
    'rangeLabel', // e.g. "27 sept. – 3 oct. 2026" or "septembre 2026"
    'overlap' => true, // Pull up over the page banner; false where there is none (Manage Schedule dashboard)
])

@php
    // Toolbar links keep the current view; "Today" drops the date
    $pageUrl = request()->url();
    $linkTo = fn (array $query) => $pageUrl . '?' . http_build_query($query);
    $buttonClass = 'inline-flex items-center justify-center gap-2 px-3 py-2 leading-tight border border-slate-500 bg-slate-900 hover:bg-slate-700 text-sm rounded-sm';
@endphp

<!-- Filter Panel: overlaps the bottom of the banner (pulled up past <main>'s top margin and padding) -->
<div {{ $attributes->merge(['class' => ($overlap ? '-mt-20 mx-2 md:mx-6 ' : '') . 'relative z-10 bg-slate-800 border border-slate-700 rounded-sm shadow-xl p-4
    flex flex-col md:flex-row md:items-center md:justify-between gap-3']) }}>

    <!-- Period: previous / date (opens a date picker) / next, then Today -->
    <div class="flex items-center gap-2">
        <a href="{{ $linkTo(['view' => $view, 'date' => $previous->toDateString()]) }}" class="{{ $buttonClass }}"
            aria-label="{{ __('bible-study-schedule/index.previous') }}">
            <i class="fa-solid fa-chevron-left"></i>
        </a>

        <!-- GET form: picking a date reloads the page on the period holding it -->
        <form action="{{ $pageUrl }}" method="GET" x-data class="relative flex-1 md:flex-none">
            <input type="hidden" name="view" value="{{ $view }}">
            <input type="date" name="date" x-ref="picker" value="{{ $start->toDateString() }}"
                @change="$el.form.submit()" class="absolute inset-0 opacity-0 pointer-events-none" tabindex="-1">

            <button type="button" @click="$refs.picker.showPicker()" class="{{ $buttonClass }} w-full md:min-w-56"
                aria-label="{{ __('bible-study-schedule/index.pick_date') }}">
                <i class="fa-regular fa-calendar"></i>
                <span class="whitespace-nowrap">{{ $rangeLabel }}</span>
            </button>
        </form>

        <a href="{{ $linkTo(['view' => $view, 'date' => $next->toDateString()]) }}" class="{{ $buttonClass }}"
            aria-label="{{ __('bible-study-schedule/index.next') }}">
            <i class="fa-solid fa-chevron-right"></i>
        </a>

        <a href="{{ $linkTo(['view' => $view]) }}" class="{{ $buttonClass }} ml-2">
            {{ __('bible-study-schedule/index.today') }}
        </a>
    </div>

    <!-- View: Week / Month / List, keeping the date -->
    <div class="flex border border-slate-500 rounded-sm overflow-hidden self-start md:self-auto">
        @foreach (['week', 'month', 'list'] as $option)
            <a href="{{ $linkTo(['view' => $option, 'date' => $start->toDateString()]) }}"
                @if ($view === $option) aria-current="page" @endif
                class="px-4 py-2 leading-tight text-sm {{ $view === $option ? 'bg-blue-600 text-white' : 'bg-slate-900 text-slate-200 hover:bg-slate-700' }} {{ $loop->first ? '' : 'border-l border-slate-500' }}">
                {{ __('bible-study-schedule/index.view_' . $option) }}
            </a>
        @endforeach
    </div>
</div>
