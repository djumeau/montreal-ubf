@php
    use App\Enums\EventCategory;

    $isFrench = app()->getLocale() === 'fr_CA';

    // Event modal: errors come from the "saveSchedule" bag
    $scheduleErrors = $errors->saveSchedule;
    $failedSave = $scheduleErrors->any();
    $fieldClass = fn (string $field) => 'bg-slate-900 border rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-white '
        . ($scheduleErrors->has($field) ? 'border-red-500' : 'border-slate-500');

    // Type radios, with the icons of the blocks; the Bible study row only shows for the group Bible studies
    $eventTypes = collect(EventCategory::cases())->mapWithKeys(fn ($category) => [$category->value => $category->icon()]);
    $bibleStudyTypes = array_column(EventCategory::WITH_BIBLE_STUDY, 'value');

    $defaultColour = '#2563EB'; // Shown in the colour picker for events without a colour

    // Events of the period, keyed by event id: what openEdit() puts in the modal
    $rowData = $studies->mapWithKeys(fn ($study) => [
        $study->id => [
            'id' => $study->id,
            'category' => $study->category->value,
            'title_en' => $study->title_en ?? '',
            'title_fr' => $study->title_fr ?? '',
            'date' => $study->start_date->toDateString(),
            // Only for an event ending on another day (e.g. a conference)
            'end_day' => $study->has_end_date && $study->end_date && !$study->end_date->isSameDay($study->start_date)
                ? $study->end_date->toDateString() : '',
            'start_time' => $study->start_date->format('H:i'),
            'end_time' => $study->has_end_date && $study->end_date ? $study->end_date->format('H:i') : '',
            'series_id' => (string) ($study->bibleStudy?->study_series_id ?? ''), // String: Alpine matches <option> values strictly
            'bible_study_id' => (string) ($study->bible_study_id ?? ''),
            'contact_name' => $study->contact_name ?? '',
            'location' => $study->location ?? '',
            'color' => $study->color_text ? $study->color : $defaultColour,
            'recurring' => (bool) $study->recurring,
            'minimum_profile' => $study->minimum_profile->value,
            'update_url' => route('schedule.update', $study),
            'delete_url' => route('schedule.destroy', $study),
        ],
    ]);

    // New event: openAdd() fills in the day and hour of the cell clicked
    $newStudy = [
        'id' => null,
        'category' => EventCategory::GBS_IN_PERSON->value,
        'title_en' => '',
        'title_fr' => '',
        'date' => $start->toDateString(),
        'end_day' => '',
        'start_time' => '19:00',
        'end_time' => '20:30',
        'series_id' => '',
        'bible_study_id' => '',
        'contact_name' => '',
        'location' => '',
        'color' => $defaultColour,
        'recurring' => false,
        'minimum_profile' => 'guest',
        'update_url' => '',
        'delete_url' => '',
    ];

    // After a failed save, the modal reopens with what was typed (event_id is empty for a new event)
    $failedStudy = old('bible_study_id') ? $bibleStudies->find(old('bible_study_id')) : null;
    $formState = $failedSave
        ? [
            'id' => old('event_id') ? (int) old('event_id') : null,
            'category' => old('category', ''),
            'title_en' => old('title_en', ''),
            'title_fr' => old('title_fr', ''),
            'date' => old('date', ''),
            'end_day' => old('end_day', ''),
            'start_time' => old('start_time', ''),
            'end_time' => old('end_time', ''),
            'series_id' => (string) old('series_id', $failedStudy?->study_series_id ?? ''),
            'bible_study_id' => (string) old('bible_study_id', ''),
            'contact_name' => old('contact_name', ''),
            'location' => old('location', ''),
            'color' => old('color', $defaultColour),
            'recurring' => (bool) old('recurring', false),
            'minimum_profile' => old('minimum_profile', 'guest'),
            'update_url' => old('event_id') ? route('schedule.update', (int) old('event_id')) : '',
            'delete_url' => old('event_id') ? route('schedule.destroy', (int) old('event_id')) : '',
        ]
        : $newStudy;

    // Series select, then the studies of that series: "Jean 3.1–21 – Title" (see BibleStudy::displayPassage)
    $seriesOptions = $seriesList->map(fn ($series) => [
        'id' => (string) $series->id,
        'name' => $isFrench ? $series->name_fr : $series->name_en,
    ]);

    $studyOptions = $bibleStudies->map(fn ($bibleStudy) => [
        'id' => (string) $bibleStudy->id,
        'series_id' => (string) ($bibleStudy->study_series_id ?? ''),
        'label' => implode(' – ', array_filter([$bibleStudy->display_passage, $bibleStudy->current_title])) ?: '#' . $bibleStudy->id,
    ]);

@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('dashboard/index.title') }}</x-slot>

    <h2 class='text-right text-2xl font-bold pt-20 pb-6'>{{ __('dashboard/index.welcome', ['name' => $user->name]) }}
    </h2>

    <!-- UI - Left sidebar with main area -->
    <div x-data="{
        sidebarOpen: true,
        showStudyModal: {{ $failedSave ? 'true' : 'false' }},
        confirmDelete: false, // Delete was clicked in the Event modal: asks before deleting
        deleteFiles: false, // Ticked in that confirmation: the images and attachments are deleted too
        form: @js($formState),
        newStudy: @js($newStudy),
        studyRows: @js($rowData),
        studyOptions: @js($studyOptions),
        storeUrl: @js(route('schedule.store')),
        bibleStudyTypes: @js($bibleStudyTypes),
        // Studies offered in the modal: those of the chosen series (every study when none is chosen)
        get seriesStudies() {
            return this.studyOptions.filter(study => !this.form.series_id || study.series_id === this.form.series_id);
        },
        // Clicking a cell of the grid: a new event on that day, from that hour, for an hour and a half
        openAdd(date, time) {
            const [hour, minute] = time.split(':').map(Number);
            const end = Math.min(hour * 60 + minute + 90, 23 * 60 + 59);
            const endTime = String(Math.floor(end / 60)).padStart(2, '0') + ':' + String(end % 60).padStart(2, '0');
            this.form = { ...this.newStudy, date, start_time: time, end_time: endTime };
            this.confirmDelete = false;
            this.deleteFiles = false;
            this.showStudyModal = true;
        },
        openEdit(id) {
            this.form = { ...this.studyRows[id] };
            this.confirmDelete = false;
            this.deleteFiles = false;
            this.showStudyModal = true;
        },
    }" class="flex min-h-screen text-white">

        <!-- Left Column: Collapsible Sidebar -->
        <aside
            :class="{
                'w-full block': sidebarOpen,
                'md:block md:w-16': !sidebarOpen,
                'md:w-64': sidebarOpen && window.innerWidth >= 768
            }"
            class="transition-all duration-300 ease-in-out border rounded-sm border-slate-100 flex flex-col justify-between">

            <div>
                <!-- Header & Toggle Chevron Button -->
                <div class="p-2 flex items-center justify-between border-b-2">
                    <span x-show="sidebarOpen" class="font-bold text-lg text-slate-100">
                        {{ __('dashboard/index.dashboard') }}
                    </span>
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="grid place-items-center size-10 pl-2 text-slate-100 hover:text-slate-300 transition-colors focus:outline-none cursor-pointer">
                        <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                    </button>
                </div>

                <x-profile-info-block />

                <x-dashboard-features></x-dashboard-features>
            </div>

        </aside>

        <!-- Right Column: Interactive Work Space Context -->
        <main :class="sidebarOpen ? 'hidden md:block' : 'block'" class="flex-1 pl-6 overflow-y-auto">

            <!-- UI Segment: Manage Study Schedule -->
            @if (auth()->user()->canManageRoles())
                <div class="w-full border rounded-sm border-slate-100">

                    <div class="p-4">
                        <h2 class="text-lg font-bold text-slate-100">{{ __('dashboard/manage-study-schedule/index.manage_schedule') }}</h2>

                        <!-- How to use the grid -->
                        <p class="mt-1 text-sm text-slate-300">
                            <i class="fa-solid fa-circle-info mr-1" aria-hidden="true"></i><em>{{ __('dashboard/manage-study-schedule/index.schedule_hint') }}</em>
                        </p>
                    </div>

                    <!-- Display Success Notifications -->
                    @if (session('status'))
                        <div class="flex items-center justify-left ml-4 mb-4">
                            <div class="w-fit p-2 border rounded-sm border-emerald-600 text-emerald-400 text-sm">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif

                    <!-- Saved, but at the same time as other events of that day (they show side by side on the week grid) -->
                    @if (session('warning'))
                        <div class="flex items-center justify-left ml-4 mb-4">
                            <div role="alert" class="w-fit p-2 border rounded-sm border-amber-500 text-amber-300 text-sm">
                                <i class="fa-solid fa-triangle-exclamation mr-1" aria-hidden="true"></i>{{ session('warning') }}
                            </div>
                        </div>
                    @endif

                    <!-- Period and view toolbar (links stay on this page) -->
                    <x-study-schedule::filter-panel :view="$view" :start="$start" :previous="$previous"
                        :next="$next" :range-label="$rangeLabel" :overlap="false" manage class="mx-4 mb-4" />

                    @if ($view === 'week')
                        <!-- Week Grid (desktop): Sunday to Saturday header, then the hour rows;
                             a block opens the Event modal to edit, a cell opens it to add -->
                        <div class="hidden md:block m-4 border border-slate-700 rounded-sm overflow-hidden">
                            <table class="w-full table-fixed text-sm">
                                <x-study-schedule::week-head :start="$start" />
                                <x-study-schedule::hour-rows :start="$start" :studies="$studies" editable />
                            </table>
                        </div>
                    @endif

                    <!-- Day List: the List view, and the Week view on phones (where the grid is hidden);
                         "+" opens the Event modal to add that day, a block opens it to edit -->
                    <x-study-schedule::day-list :start="$start" :studies="$studies" editable
                        class="m-4 {{ $view === 'week' ? 'md:hidden' : '' }}" />

                </div>
            @endif

            <!-- END UI Segment: Manage Study Schedule -->

        </main>

        @if (auth()->user()->canManageRoles())
            <!-- AlpineJS Modal for Adding / Editing an Event of the schedule (one shared modal, filled by openAdd() / openEdit()) -->
            <div x-show="showStudyModal" x-cloak @keydown.escape.window="showStudyModal = false"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
                x-transition>

                <div @click.away="showStudyModal = false" role="dialog" aria-modal="true" aria-labelledby="study_modal_title"
                    class="flex flex-col bg-slate-900 rounded-lg max-w-lg w-full max-h-[90vh] overflow-hidden shadow-xl border border-slate-500">

                    <!-- Title and Close -->
                    <div class="shrink-0 flex items-center justify-between px-5 py-3 bg-slate-800 rounded-t-lg">
                        <h3 id="study_modal_title" class="text-lg font-bold">{{ __('dashboard/manage-study-schedule/index.schedule_event') }}</h3>
                        <button type="button" @click="showStudyModal = false" aria-label="{{ __('dashboard/index.close') }}"
                            class="grid place-items-center size-8 text-slate-300 hover:text-white cursor-pointer">
                            <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                        </button>
                    </div>

                    <!-- Posts to the store route for a new event, to the event's update route (PUT) otherwise -->
                    <form class="flex flex-col min-h-0 text-sm" :action="form.id ? form.update_url : storeUrl" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT" :disabled="!form.id">

                        <!-- Lets the page reopen this modal for the same event after a failed save -->
                        <input type="hidden" name="event_id" :value="form.id ?? ''">

                        <!-- Fields: the only part that scrolls, so the title and Delete / Save stay in view -->
                        <div class="px-5 overflow-y-auto">

                        <!-- Type: Event / Conference / Bible study (group, in person or online) -->
                        <fieldset class="grid grid-cols-1 sm:grid-cols-[10rem_1fr] gap-x-4 gap-y-2 py-4">
                            <legend class="sr-only">{{ __('dashboard/manage-study-schedule/index.event_type') }}</legend>
                            <span class="font-medium" aria-hidden="true">{{ __('dashboard/manage-study-schedule/index.event_type') }}</span>
                            <div class="space-y-2">
                                @foreach ($eventTypes as $type => $typeIcon)
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="category" value="{{ $type }}" x-model="form.category"
                                            class="size-4 accent-blue-600">
                                        <i class="fas {{ $typeIcon }} w-5 text-center" aria-hidden="true"></i>
                                        {{ __('dashboard/manage-study-schedule/index.event_type_' . $type) }}
                                    </label>
                                @endforeach
                                @if ($scheduleErrors->has('category'))
                                    <p class="text-xs text-red-500">{{ $scheduleErrors->first('category') }}</p>
                                @endif
                            </div>
                        </fieldset>

                        <!-- Titles (EN / FR) -->
                        <div class="grid grid-cols-1 sm:grid-cols-[10rem_1fr] gap-x-4 gap-y-2 py-3 border-t border-slate-700">
                            <span class="flex items-center gap-3 font-medium self-start sm:pt-2">
                                <i class="fa-solid fa-heading w-5 text-center text-base" aria-hidden="true"></i>{{ __('dashboard/index.title_column') }}
                            </span>
                            <div class="space-y-2">
                                @foreach (['title_en', 'title_fr'] as $titleField)
                                    <div>
                                        <input type="text" name="{{ $titleField }}" x-model="form.{{ $titleField }}" maxlength="80"
                                            aria-label="{{ __('dashboard/index.' . $titleField) }}" placeholder="{{ __('dashboard/index.' . $titleField) }}"
                                            class="{{ $fieldClass($titleField) }} w-full placeholder:text-slate-500">
                                        @if ($scheduleErrors->has($titleField))
                                            <p class="text-xs text-red-500 mt-1">{{ $scheduleErrors->first($titleField) }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Date, then an optional end date for an event ending on another day -->
                        <div class="grid grid-cols-1 sm:grid-cols-[10rem_1fr] gap-x-4 gap-y-2 py-3 border-t border-slate-700">
                            <label for="study_date" class="flex items-center gap-3 font-medium self-start sm:pt-2">
                                <i class="fa-regular fa-calendar w-5 text-center text-base" aria-hidden="true"></i>{{ __('dashboard/manage-study-schedule/index.date') }}
                            </label>
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <input id="study_date" type="date" name="date" x-model="form.date" required
                                        class="{{ $fieldClass('date') }} scheme-dark">
                                    <span>{{ __('dashboard/manage-study-schedule/index.time_to') }}</span>
                                    <input type="date" name="end_day" x-model="form.end_day" :min="form.date"
                                        aria-label="{{ __('dashboard/manage-study-schedule/index.end_day') }}" class="{{ $fieldClass('end_day') }} scheme-dark">
                                </div>
                                <p class="text-xs text-slate-400 mt-1">{{ __('dashboard/manage-study-schedule/index.end_day_hint') }}</p>
                                @foreach (['date', 'end_day'] as $dateField)
                                    @if ($scheduleErrors->has($dateField))
                                        <p class="text-xs text-red-500 mt-1">{{ $scheduleErrors->first($dateField) }}</p>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Time: start, then an optional end -->
                        <div class="grid grid-cols-1 sm:grid-cols-[10rem_1fr] gap-x-4 gap-y-2 items-center py-3 border-t border-slate-700">
                            <span class="flex items-center gap-3 font-medium">
                                <i class="fa-regular fa-clock w-5 text-center text-base" aria-hidden="true"></i>{{ __('dashboard/manage-study-schedule/index.time') }}
                            </span>
                            <div>
                                <div class="flex items-center gap-3">
                                    <input type="time" name="start_time" x-model="form.start_time" required step="300"
                                        aria-label="{{ __('dashboard/manage-study-schedule/index.start_time') }}" class="{{ $fieldClass('start_time') }} scheme-dark">
                                    <span>{{ __('dashboard/manage-study-schedule/index.time_to') }}</span>
                                    <input type="time" name="end_time" x-model="form.end_time" step="300"
                                        aria-label="{{ __('dashboard/manage-study-schedule/index.end_time') }}" class="{{ $fieldClass('end_time') }} scheme-dark">
                                </div>
                                @foreach (['start_time', 'end_time'] as $timeField)
                                    @if ($scheduleErrors->has($timeField))
                                        <p class="text-xs text-red-500 mt-1">{{ $scheduleErrors->first($timeField) }}</p>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <!-- Recurring: unchecked sends the hidden 0 -->
                        <div class="grid grid-cols-1 sm:grid-cols-[10rem_1fr] gap-x-4 gap-y-2 items-center py-3 border-t border-slate-700">
                            <label for="study_recurring" class="flex items-center gap-3 font-medium">
                                <i class="fa-solid fa-repeat w-5 text-center text-base" aria-hidden="true"></i>{{ __('dashboard/manage-study-schedule/index.recurring') }}
                            </label>
                            <div>
                                <input type="hidden" name="recurring" value="0">
                                <input id="study_recurring" type="checkbox" name="recurring" value="1" x-model="form.recurring"
                                    class="size-4 accent-blue-600 cursor-pointer align-middle">
                                @if ($scheduleErrors->has('recurring'))
                                    <p class="text-xs text-red-500 mt-1">{{ $scheduleErrors->first('recurring') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Minimum profile: who sees the event (this role and above; Guest is everyone, visitors included) -->
                        <div class="grid grid-cols-1 sm:grid-cols-[10rem_1fr] gap-x-4 gap-y-2 py-3 border-t border-slate-700">
                            <label for="study_minimum_profile" class="flex items-center gap-3 font-medium self-start sm:pt-2">
                                <i class="fa-solid fa-user-lock w-5 text-center text-base" aria-hidden="true"></i>{{ __('dashboard/manage-study-schedule/index.minimum_profile') }}
                            </label>
                            <div>
                                <select id="study_minimum_profile" name="minimum_profile" x-model="form.minimum_profile"
                                    class="{{ $fieldClass('minimum_profile') }} w-full sm:w-auto">
                                    @foreach (\App\Models\Event::MINIMUM_PROFILES as $role)
                                        <option value="{{ $role->value }}">{{ $role->label() }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-slate-400 mt-1">{{ __('dashboard/manage-study-schedule/index.minimum_profile_hint') }}</p>
                                @if ($scheduleErrors->has('minimum_profile'))
                                    <p class="text-xs text-red-500 mt-1">{{ $scheduleErrors->first('minimum_profile') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Bible study: the series, then one of its studies (group Bible studies only; not saved for other types) -->
                        <div x-show="bibleStudyTypes.includes(form.category)"
                            class="grid grid-cols-1 sm:grid-cols-[10rem_1fr] gap-x-4 gap-y-2 items-center py-3 border-t border-slate-700">
                            <label for="study_bible_study_id" class="flex items-center gap-3 font-medium">
                                <i class="fa-solid fa-book-open w-5 text-center text-base" aria-hidden="true"></i>{{ __('dashboard/manage-study-schedule/index.schedule_bible_study') }}
                            </label>
                            <div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <!-- Not saved: only narrows the list of studies -->
                                    <select name="series_id" x-model="form.series_id" @change="form.bible_study_id = ''"
                                        aria-label="{{ __('dashboard/index.study_series') }}" class="{{ $fieldClass('series_id') }} w-full">
                                        <option value="">{{ __('dashboard/index.all_series') }}</option>
                                        @foreach ($seriesOptions as $series)
                                            <option value="{{ $series['id'] }}">{{ $series['name'] }}</option>
                                        @endforeach
                                    </select>

                                    <!-- Options follow the series; :selected keeps the choice when the list is redrawn -->
                                    <select id="study_bible_study_id" name="bible_study_id" @change="form.bible_study_id = $event.target.value"
                                        class="{{ $fieldClass('bible_study_id') }} w-full">
                                        <option value="" :selected="!form.bible_study_id">{{ __('dashboard/manage-study-schedule/index.no_study_option') }}</option>
                                        <template x-for="study in seriesStudies" :key="study.id">
                                            <option :value="study.id" x-text="study.label" :selected="study.id === form.bible_study_id"></option>
                                        </template>
                                    </select>
                                </div>
                                @if ($scheduleErrors->has('bible_study_id'))
                                    <p class="text-xs text-red-500 mt-1">{{ $scheduleErrors->first('bible_study_id') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Leader -->
                        <div class="grid grid-cols-1 sm:grid-cols-[10rem_1fr] gap-x-4 gap-y-2 items-center py-3 border-t border-slate-700">
                            <label for="study_contact_name" class="flex items-center gap-3 font-medium">
                                <i class="fa-solid fa-user w-5 text-center text-base" aria-hidden="true"></i>{{ __('dashboard/manage-study-schedule/index.leader') }}
                            </label>
                            <div>
                                <input id="study_contact_name" type="text" name="contact_name" x-model="form.contact_name" maxlength="100"
                                    class="{{ $fieldClass('contact_name') }} w-full">
                                @if ($scheduleErrors->has('contact_name'))
                                    <p class="text-xs text-red-500 mt-1">{{ $scheduleErrors->first('contact_name') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="grid grid-cols-1 sm:grid-cols-[10rem_1fr] gap-x-4 gap-y-2 items-center py-3 border-t border-slate-700">
                            <label for="study_location" class="flex items-center gap-3 font-medium">
                                <i class="fa-solid fa-location-dot w-5 text-center text-base" aria-hidden="true"></i>{{ __('dashboard/manage-study-schedule/index.location') }}
                            </label>
                            <div>
                                <input id="study_location" type="text" name="location" x-model="form.location" maxlength="1024"
                                    placeholder="{{ __('dashboard/manage-study-schedule/index.location_placeholder') }}"
                                    class="{{ $fieldClass('location') }} w-full placeholder:text-slate-500">
                                @if ($scheduleErrors->has('location'))
                                    <p class="text-xs text-red-500 mt-1">{{ $scheduleErrors->first('location') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Colour of the event's block on the schedule -->
                        <div class="grid grid-cols-1 sm:grid-cols-[10rem_1fr] gap-x-4 gap-y-2 items-center py-3 border-t border-slate-700">
                            <label for="study_color" class="flex items-center gap-3 font-medium">
                                <i class="fa-solid fa-palette w-5 text-center text-base" aria-hidden="true"></i>{{ __('dashboard/manage-study-schedule/index.colour') }}
                            </label>
                            <div>
                                <div class="flex items-center gap-3">
                                    <input id="study_color" type="color" name="color" x-model="form.color"
                                        class="h-9 w-14 p-0.5 bg-slate-900 border rounded-sm cursor-pointer {{ $scheduleErrors->has('color') ? 'border-red-500' : 'border-slate-500' }}">
                                    <span class="uppercase text-slate-300" x-text="form.color"></span>
                                </div>
                                @if ($scheduleErrors->has('color'))
                                    <p class="text-xs text-red-500 mt-1">{{ $scheduleErrors->first('color') }}</p>
                                @endif
                            </div>
                        </div>

                        </div>

                        <!-- Modal Action Controls: always in view under the fields -->
                        <div class="shrink-0 flex flex-wrap items-center justify-end gap-3 px-5 py-4 border-t border-slate-500 bg-slate-800 rounded-b-lg">

                            <!-- Delete (trash can), on the left, for an existing event only: asks first, then sends the delete form under this one -->
                            <div x-show="form.id" class="flex-1 flex flex-wrap items-center gap-3">

                                <button type="button" x-show="!confirmDelete" @click="confirmDelete = true"
                                    title="{{ __('dashboard/manage-study-schedule/index.delete_event') }}" aria-label="{{ __('dashboard/manage-study-schedule/index.delete_event') }}"
                                    class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-medium border border-white rounded-sm cursor-pointer">
                                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                    {{ __('dashboard/manage-study-schedule/index.delete_event') }}
                                </button>

                                <div x-show="confirmDelete" x-cloak role="alert" class="flex flex-wrap items-center gap-3">
                                    <div class="text-amber-300">

                                        <p>{{ __('dashboard/manage-study-schedule/index.delete_event_confirm') }}</p>

                                        <!-- Belongs to the delete form (form="..."), so it is not sent with Save. Unticked: the files stay on the server -->
                                        <label class="flex items-center gap-2 mt-1 cursor-pointer">
                                            <input type="checkbox" name="delete_files" value="1" form="delete_event_form" x-model="deleteFiles"
                                                class="size-4 accent-blue-600 cursor-pointer">
                                            {{ __('dashboard/manage-study-schedule/index.delete_event_files') }}
                                        </label>

                                    </div>

                                    <button type="button" @click="confirmDelete = false"
                                        class="px-4 py-2 bg-slate-900 hover:bg-slate-700 text-white font-medium border border-white rounded-sm cursor-pointer">
                                        {{ __('dashboard/index.no') }}
                                    </button>

                                    <button type="submit" form="delete_event_form"
                                        class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-medium border border-white rounded-sm cursor-pointer">
                                        {{ __('dashboard/index.yes') }}
                                    </button>

                                </div>
                            </div>

                            <!-- Save (disk icon), hidden while the delete confirmation shows: "No" brings back the trash can and Save.
                                 No Cancel / Close button here: the X at the top, Escape or a click outside closes the modal -->

                            <button type="submit" x-show="!confirmDelete"
                                title="{{ __('dashboard/index.save') }}" aria-label="{{ __('dashboard/index.save') }}"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium border border-white rounded-sm cursor-pointer">
                                <i class="fa-solid fa-floppy-disk fa-lg" aria-hidden="true"></i> {{ __('dashboard/index.save') }}
                            </button>

                        </div>

                    </form>

                    <!-- Sent by "Yes" above (a form cannot sit inside another one) -->
                    <form id="delete_event_form" :action="form.delete_url" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>

                </div>

            </div>

        @endif

    </div>

</x-layout>
