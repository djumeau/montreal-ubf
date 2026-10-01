@props([
    'view', // week | list
    'start', // Sunday of the week shown (Carbon)
    'previous', // Sunday of the previous / next week (Carbon)
    'next',
    'rangeLabel', // e.g. "27 sept. – 3 oct. 2026"
    'overlap' => true, // Pull up over the page banner; false where there is none (Manage Schedule dashboard)
    'manage' => false, // Manage Schedule dashboard: "Reset" instead of "Today", and the date button opens the week modal
])

@php
    // Toolbar links keep the current view; "Today" / "Reset" drops the date
    $pageUrl = request()->url();
    $linkTo = fn (array $query) => $pageUrl . '?' . http_build_query($query);
    $buttonClass = 'inline-flex items-center justify-center gap-2 px-3 py-2 leading-tight border border-slate-500 bg-slate-900 hover:bg-slate-700 text-sm rounded-sm';
@endphp

<!-- Filter Panel: overlaps the bottom of the banner (pulled up past <main>'s top margin and padding) -->
<div {{ $attributes->merge(['class' => ($overlap ? '-mt-20 mx-2 md:mx-6 ' : '') . 'relative z-10 bg-slate-800 border border-slate-700 rounded-sm shadow-xl p-4
    flex flex-col md:flex-row md:items-center md:justify-between gap-3']) }}>

    <!-- Period: previous / date (opens a date picker, or the week modal on Manage Schedule) / next,
         then Today (Reset on Manage Schedule) -->
    <div class="flex items-center gap-2">
        <a href="{{ $linkTo(['view' => $view, 'date' => $previous->toDateString()]) }}" class="{{ $buttonClass }}"
            aria-label="{{ __('bible-study-schedule/index.previous') }}">
            <i class="fa-solid fa-chevron-left"></i>
        </a>

        @if ($manage)
            <!-- Week modal: a month at a time, one row per week (Sunday to Saturday); picking a row reloads the page on that week -->
            <div class="relative flex-1 md:flex-none" x-data="{
                open: false,
                current: @js($start->toDateString()), // Sunday of the week shown
                month: null, // First day of the month shown in the modal
                locale: @js(str_replace('_', '-', app()->getLocale())),
                iso(date) {
                    return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
                },
                show() {
                    const [year, month] = this.current.split('-').map(Number);
                    this.month = new Date(year, month - 1, 1);
                    this.open = true;
                },
                move(months) {
                    this.month = new Date(this.month.getFullYear(), this.month.getMonth() + months, 1);
                },
                get monthLabel() {
                    return this.month ? new Intl.DateTimeFormat(this.locale, { month: 'long', year: 'numeric' }).format(this.month) : '';
                },
                // Sunday to Saturday short names, from a week known to start on a Sunday (February 1, 2026)
                get weekdays() {
                    return [...Array(7).keys()].map(day => new Intl.DateTimeFormat(this.locale, { weekday: 'short' }).format(new Date(2026, 1, 1 + day)));
                },
                // Weeks touching the month: [{ start: 'YYYY-MM-DD', label, days: [{ day, inMonth, today }] }]
                get weeks() {
                    if (!this.month) return [];
                    const weeks = [];
                    const today = this.iso(new Date());
                    const cursor = new Date(this.month.getFullYear(), this.month.getMonth(), 1 - this.month.getDay());
                    const nextMonth = new Date(this.month.getFullYear(), this.month.getMonth() + 1, 1);
                    while (cursor < nextMonth) {
                        const start = new Date(cursor);
                        const days = [];
                        for (let day = 0; day < 7; day++) {
                            days.push({ day: cursor.getDate(), inMonth: cursor.getMonth() === this.month.getMonth(), today: this.iso(cursor) === today });
                            cursor.setDate(cursor.getDate() + 1);
                        }
                        weeks.push({
                            start: this.iso(start),
                            label: new Intl.DateTimeFormat(this.locale, { dateStyle: 'long' }).format(start),
                            days,
                        });
                    }
                    return weeks;
                },
                weekUrl(start) {
                    return @js($pageUrl) + '?view=' + @js($view) + '&date=' + start;
                },
            }">
                <button type="button" @click="show()" class="{{ $buttonClass }} w-full md:min-w-56 cursor-pointer"
                    aria-haspopup="dialog" aria-label="{{ __('bible-study-schedule/index.select_week') }}">
                    <i class="fa-regular fa-calendar"></i>
                    <span class="whitespace-nowrap">{{ $rangeLabel }}</span>
                </button>

                <!-- Teleported to <body> so it sits above the week grid's blocks -->
                <template x-teleport="body">
                    <div x-show="open" x-cloak @keydown.escape.window="open = false" x-transition
                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs text-white">

                        <div @click.outside="open = false" role="dialog" aria-modal="true" aria-labelledby="week_modal_title"
                            class="bg-slate-900 rounded-lg max-w-sm w-full shadow-xl border border-slate-500">

                            <!-- Title and Close -->
                            <div class="flex items-center justify-between px-5 py-3 bg-slate-800 rounded-t-lg">
                                <h3 id="week_modal_title" class="text-lg font-bold">{{ __('bible-study-schedule/index.select_week') }}</h3>
                                <button type="button" @click="open = false" aria-label="{{ __('bible-study-schedule/index.close') }}"
                                    class="grid place-items-center size-8 text-slate-300 hover:text-white cursor-pointer">
                                    <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div class="p-5 text-sm">
                                <!-- Month: previous / name / next -->
                                <div class="flex items-center justify-between mb-3">
                                    <button type="button" @click="move(-1)" class="{{ $buttonClass }} cursor-pointer"
                                        aria-label="{{ __('bible-study-schedule/index.previous_month') }}">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </button>
                                    <span class="text-base font-medium first-letter:uppercase" x-text="monthLabel" aria-live="polite"></span>
                                    <button type="button" @click="move(1)" class="{{ $buttonClass }} cursor-pointer"
                                        aria-label="{{ __('bible-study-schedule/index.next_month') }}">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </div>

                                <!-- Weekday names, then one link per week; the week shown is blue, today has a ring -->
                                <div class="grid grid-cols-7 text-center text-xs text-slate-400 mb-1" aria-hidden="true">
                                    <template x-for="weekday in weekdays" :key="weekday">
                                        <span class="py-1 first-letter:uppercase" x-text="weekday.replace('.', '')"></span>
                                    </template>
                                </div>

                                <div class="space-y-1">
                                    <template x-for="week in weeks" :key="week.start">
                                        <a :href="weekUrl(week.start)" :aria-current="week.start === current ? 'true' : null"
                                            :aria-label="@js(__('bible-study-schedule/index.week_of')) + ' ' + week.label"
                                            class="grid grid-cols-7 text-center rounded-sm border"
                                            :class="week.start === current ? 'bg-blue-600 border-blue-600 text-white' : 'border-slate-700 hover:bg-slate-700 hover:border-slate-500'">
                                            <template x-for="(day, index) in week.days" :key="index">
                                                <span class="py-2 rounded-sm" x-text="day.day"
                                                    :class="{ 'text-slate-500': !day.inMonth && week.start !== current, 'ring-1 ring-inset ring-white font-bold': day.today }"></span>
                                            </template>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        @else
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
        @endif

        <a href="{{ $linkTo(['view' => $view, 'date' => $next->toDateString()]) }}" class="{{ $buttonClass }}"
            aria-label="{{ __('bible-study-schedule/index.next') }}">
            <i class="fa-solid fa-chevron-right"></i>
        </a>

        <!-- Back to the current week: icon only, named for screen readers and on hover -->
        <a href="{{ $linkTo(['view' => $view]) }}" class="{{ $buttonClass }} ml-2"
            aria-label="{{ __('bible-study-schedule/index.' . ($manage ? 'reset' : 'today')) }}"
            title="{{ __('bible-study-schedule/index.' . ($manage ? 'reset' : 'today')) }}">
            <i class="fa-solid fa-rotate-left" aria-hidden="true"></i>
        </a>
    </div>

    <!-- View: Week / List, keeping the date; hidden on phones, which always get the day list -->
    <div class="hidden md:flex border border-slate-500 rounded-sm overflow-hidden">
        @foreach (['week', 'list'] as $option)
            <a href="{{ $linkTo(['view' => $option, 'date' => $start->toDateString()]) }}"
                @if ($view === $option) aria-current="page" @endif
                class="px-4 py-2 leading-tight text-sm {{ $view === $option ? 'bg-blue-600 text-white' : 'bg-slate-900 text-slate-200 hover:bg-slate-700' }} {{ $loop->first ? '' : 'border-l border-slate-500' }}">
                {{ __('bible-study-schedule/index.view_' . $option) }}
            </a>
        @endforeach
    </div>
</div>
