@php
    // Current locale's name first, the other locale's name underneath in parentheses
    $isFrench = app()->getLocale() === 'fr_CA';

    // Search panel filters; any of them set shows the "Clear filters" link
    $hasFilters = $search !== '' || $currentSeries || $currentBook;

    // Series and books by id, as "Name" and, for the filters, "Name (study count)"; no count when there are none
    $seriesName = fn ($series) => $isFrench ? $series->name_fr : $series->name_en;
    $bookName = fn ($book) => $isFrench ? $book->name_fr : $book->name_en;
    $withCount = fn ($name) => fn ($item) => $name($item) . ($item->bible_studies_count ? " ({$item->bible_studies_count})" : '');

    // Filter options without studies are greyed out and can't be picked (they would show an empty list)
    $emptySeriesIds = $seriesList->where('bible_studies_count', 0)->pluck('id')->all();
    $emptyBookIds = $books->where('bible_studies_count', 0)->pluck('id')->all();

    // Books grouped by testament, labelled by $label
    $bookGroups = function ($label) use ($books) {
        $groups = [];
        foreach (['ot' => 'old_testament', 'nt' => 'new_testament'] as $testament => $groupKey) {
            $groups[__('dashboard/index.' . $groupKey)] = $books->where('testament', $testament)
                ->mapWithKeys(fn ($book) => [$book->id => $label($book)])
                ->all();
        }
        return $groups;
    };

    // Filter dropdowns: counts next to each name, e.g. "L'évangile de Jean (7)"
    $seriesFilterOptions = ['' => __('dashboard/index.all_series')]
        + $seriesList->mapWithKeys(fn ($series) => [$series->id => $withCount($seriesName)($series)])->all();
    $bookFilterOptions = ['' => __('dashboard/index.all_books')] + $bookGroups($withCount($bookName));

    // Add / Edit modal selects: plain names; empty option means no series / no book yet
    $seriesOptions = ['' => __('dashboard/index.no_series_option')]
        + $seriesList->mapWithKeys(fn ($series) => [$series->id => $seriesName($series)])->all();
    $bookOptions = ['' => __('dashboard/index.select_book')] + $bookGroups($bookName);

    // Image slots (shared by EN and FR), in the order shown in the Add / Edit modals
    $imageTypes = ['square' => 'image_square', 'desktop' => 'image_desktop', 'mobile' => 'image_mobile'];

    // "Jean 3.1–21" / "John 3:1–21" (see BibleStudy::displayPassage), a dash when there's no book or passage
    $formatPassage = fn ($study) => $study->display_passage ?: '—';

    // Attachments modal: one section per language, one group per type
    $attachmentLocales = \App\Models\StudyAttachment::LOCALES;
    $attachmentTypes = \App\Models\StudyAttachment::TYPES;

    // A study's attachments as [locale][type] => [{id, name, extension, views, show_url}], for the Attachments modal
    $groupAttachments = fn ($study) => collect($attachmentLocales)->mapWithKeys(fn ($locale) => [
        $locale => collect($attachmentTypes)->mapWithKeys(fn ($type) => [
            $type => $study->attachments->where('locale', $locale)->where('type', $type)->values()
                ->map(fn ($attachment) => [
                    'id' => $attachment->id,
                    'name' => $attachment->name_with_extension,
                    'extension' => $attachment->extension,
                    'views' => $attachment->views,
                    'show_url' => route('attachments.show', $attachment), // PDF opens in the browser, DOCX downloads; not counted for Admin / Elder
                    'destroy_url' => route('attachments.destroy', $attachment),
                ]),
        ]),
    ]);

    // Row data handed to the Edit / Delete / Attachments modals, keyed by study id
    $rowData = $studies->getCollection()->mapWithKeys(fn ($study) => [
        $study->id => [
            'id' => $study->id,
            'study_series_id' => (string) ($study->study_series_id ?? ''), // String: Alpine matches <option> values strictly
            'book_id' => (string) ($study->book_id ?? ''),
            'bible_passage' => $study->bible_passage ?? '',
            'title_en' => $study->title_en ?? '',
            'title_fr' => $study->title_fr ?? '',
            'images' => $study->image_links ?? [], // Current file names: square, desktop, mobile
            'desktop' => $study->imageUrl('desktop'), // Preview at the top of the Edit modal
            'name' => $isFrench ? $study->title_fr : $study->title_en,
            'attachments' => $study->attachments_count,
            'attachments_deleted' => trans_choice('dashboard/index.delete_study_attachments', $study->attachments_count, ['count' => $study->attachments_count]),
            'passage' => $formatPassage($study),
            'files' => $groupAttachments($study),
            'attachments_url' => route('attachments.store', $study),
            'update_url' => route('study.update', $study),
            'destroy_url' => route('study.destroy', $study),
        ],
    ]);

    // Attachments modal reopens for the same study after an upload / delete (flashed id) or a failed upload (old input)
    $failedUpload = $errors->uploadAttachments->any();
    $attachmentsStudyId = session('attachments_study') ?? ($failedUpload ? (int) old('attachments_study_id') : null);
    $attachmentsStudy = $rowData[$attachmentsStudyId] ?? null; // Null if that study isn't on this page
    $uploadForm = [
        'locale' => $failedUpload ? old('locale', '') : ($isFrench ? 'fr_CA' : 'en_CA'),
        'type' => $failedUpload ? old('type', '') : 'question_sheet',
    ];
    $localeOptions = collect($attachmentLocales)->mapWithKeys(fn ($locale) => [$locale => __('dashboard/index.language_' . $locale)])->all();
    $typeOptions = collect($attachmentTypes)->mapWithKeys(fn ($type) => [$type => __('dashboard/index.attachment_' . $type)])->all();

    // Add form state; refilled from old input only after a failed create
    $failedCreate = $errors->createStudy->any();
    $addStudy = [
        'study_series_id' => $failedCreate ? (string) old('study_series_id', '') : (string) ($currentSeries?->id ?? ''), // Defaults to the filtered series
        'book_id' => $failedCreate ? (string) old('book_id', '') : (string) ($currentBook?->id ?? ''),
        'bible_passage' => $failedCreate ? old('bible_passage', '') : '',
        'title_en' => $failedCreate ? old('title_en', '') : '',
        'title_fr' => $failedCreate ? old('title_fr', '') : '',
    ];

    // Edit form state; after a failed update, reopen for the same study with the old input
    $failedUpdateId = $errors->updateStudy->any() ? (int) old('study_id') : null;
    // (row data first, so images and the Attachments button keep working; the typed values on top)
    $editStudy = $failedUpdateId
        ? array_merge($rowData[$failedUpdateId] ?? [], [
            'id' => $failedUpdateId,
            'study_series_id' => (string) old('study_series_id', ''),
            'book_id' => (string) old('book_id', ''),
            'bible_passage' => old('bible_passage', ''),
            'title_en' => old('title_en', ''),
            'title_fr' => old('title_fr', ''),
            'update_url' => route('study.update', $failedUpdateId),
        ])
        : ['id' => null, 'study_series_id' => '', 'book_id' => '', 'bible_passage' => '', 'title_en' => '', 'title_fr' => '', 'images' => [], 'desktop' => null, 'update_url' => ''];

    // List heading: "Bible Studies – {series}" when filtered by series
    $listHeading = __('dashboard/index.bible_studies')
        . ($currentSeries ? ' – ' . ($isFrench ? $currentSeries->name_fr : $currentSeries->name_en) : '');
@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('dashboard/index.title') }}</x-slot>

    <h2 class='text-right text-2xl font-bold pt-20 pb-6'>{{ __('dashboard/index.welcome', ['name' => $user->name]) }}
    </h2>

    <!-- UI - Left sidebar with main area -->
    <div x-data="{
        sidebarOpen: true,
        showAddStudiesModal: {{ $failedCreate ? 'true' : 'false' }},
        showEditStudiesModal: {{ $failedUpdateId ? 'true' : 'false' }},
        showDeleteStudiesModal: false,
        showAttachmentsModal: {{ $attachmentsStudy ? 'true' : 'false' }},
        addStudy: @js($addStudy),
        editStudy: @js($editStudy),
        deleteStudy: {},
        attachmentsStudy: @js($attachmentsStudy ?? (object) []),
        uploadForm: @js($uploadForm),
        openEdit(study) {
            // Clear file inputs left over from a previously edited row (change event resets the shown file name)
            this.$refs.editStudyForm.querySelectorAll('input[type=file]').forEach(input => {
                input.value = '';
                input.dispatchEvent(new Event('change'));
            });
            this.editStudy = { ...study };
            this.showEditStudiesModal = true;
        },
        openDelete(study) {
            this.deleteStudy = { ...study };
            this.showDeleteStudiesModal = true;
        },
        openAttachments(study) {
            // Clear files chosen for a previously opened study (change event resets the shown file names)
            this.$refs.uploadAttachmentsForm.querySelectorAll('input[type=file]').forEach(input => {
                input.value = '';
                input.dispatchEvent(new Event('change'));
            });
            this.attachmentsStudy = { ...study };
            this.showAttachmentsModal = true;
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

            <!-- UI Segment: Manage Studies -->
            @if (auth()->user()->canManageRoles())

                <div class="w-full border rounded-sm border-slate-100">

                    <div class="p-4 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-100">{{ __('dashboard/index.manage_studies') }}
                        </h2>

                        <button type="button" @click="showAddStudiesModal = true"
                            class="px-4 py-2 bg-sky-900 hover:bg-sky-950 text-white font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                            <i class="fas fa-plus mr-1"></i>{{ __('dashboard/index.add_study') }}
                        </button>
                    </div>

                    <!-- Display Success Notifications -->
                    @if (session('status'))
                        <div class="flex items-center justify-left ml-4 mb-4">
                            <div class="w-fit p-2 border rounded-sm border-emerald-600 text-emerald-400 text-sm">
                                {{ __(session('status')) }}
                            </div>
                        </div>
                    @endif

                    <!-- Search & Filters: GET form, so results are bookmarkable and survive pagination; selects apply on change.
                         Only one filter applies at a time:
                         - Choosing a series clears the search text and sets the book back to "All books".
                         - Choosing a book clears the search text and sets the series back to "All series".
                         - Searching (button or Enter) sets series and book back to "All".
                         @submit only fires for the button / Enter, since $el.submit() from the selects skips the submit event -->
                    <form action="{{ request()->url() }}" method="GET"
                        @change="
                            if ($event.target.name === 'series') { $el.elements.q.value = ''; $el.elements.book.value = ''; }
                            if ($event.target.name === 'book') { $el.elements.q.value = ''; $el.elements.series.value = ''; }
                            if ($event.target.tagName === 'SELECT') $el.submit();
                        "
                        @submit="$el.elements.series.value = ''; $el.elements.book.value = ''"
                        class="px-4 pb-4 grid grid-cols-1 md:grid-cols-2 gap-x-3">

                        <div class="md:col-span-2 flex flex-col sm:flex-row gap-3 mb-4">
                            <div class="relative flex-1">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400 pointer-events-none"></i>
                                <input type="search" name="q" value="{{ $search }}"
                                    aria-label="{{ __('dashboard/index.search') }}"
                                    placeholder="{{ __('dashboard/index.search_studies_placeholder') }}"
                                    class="w-full shadow appearance-none border border-slate-300 rounded-sm py-2 pl-9 pr-2 focus:outline-none focus:shadow-outline text-sm">
                            </div>

                            <button type="submit"
                                class="px-4 py-2 bg-sky-900 hover:bg-sky-950 text-white text-sm font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                {{ __('dashboard/index.search') }}
                            </button>
                        </div>

                        <x-inputs.select id="filter_series" name="series" :value="$currentSeries?->id"
                            :options="$seriesFilterOptions" :disabled="$emptySeriesIds" :label="__('dashboard/index.study_series')" />

                        <x-inputs.select id="filter_book" name="book" :value="$currentBook?->id"
                            :options="$bookFilterOptions" :disabled="$emptyBookIds" :label="__('dashboard/index.bible_book')" />

                        @if ($hasFilters)
                            <div class="md:col-span-2 text-right">
                                <a href="{{ request()->url() }}" class="text-sm text-sky-400 hover:text-sky-300 hover:underline">
                                    {{ __('dashboard/index.clear_filters') }}
                                </a>
                            </div>
                        @endif
                    </form>

                    <!-- List paginated Bible studies (5) -->
                    <div class="px-4 pb-3">
                        <h3 class="text-base font-bold text-slate-100">{{ $listHeading }} ({{ $studies->total() }})</h3>
                    </div>

                    <!-- Desktop: Table Layout -->
                    <div class="hidden md:block overflow-x-auto px-4 pb-4">

                        <table class="w-full text-center text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-300 uppercase text-xs tracking-wider">
                                    <th class="py-2 px-2">#</th>
                                    <th class="py-2 px-2">{{ __('dashboard/index.image') }}</th>
                                    <th class="py-2 px-2 text-left">{{ __('dashboard/index.title_column') }}</th>
                                    <th class="py-2 px-2">{{ __('dashboard/index.bible_passage') }}</th>
                                    <th class="py-2 px-2">{{ __('dashboard/index.attachments_count') }}</th>
                                    <th class="py-2 px-2">{{ __('dashboard/index.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($studies as $study)
                                    <tr class="border-b border-slate-800 align-middle odd:bg-black/50">
                                        <td class="py-3 px-2 text-slate-300">{{ $study->id }}</td>
                                        <td class="py-3 px-2">
                                            <img src="{{ $study->imageUrl('square') }}" alt="{{ $study->current_title }}"
                                                class="size-14 mx-auto rounded-sm object-cover border border-slate-700">
                                        </td>
                                        <td class="py-3 px-2 text-left">
                                            <div class="text-slate-100">{{ $isFrench ? $study->title_fr : $study->title_en }}</div>
                                            <div class="text-slate-400 text-xs">
                                                ({{ $isFrench ? $study->title_en : $study->title_fr }})
                                            </div>
                                        </td>
                                        <td class="py-3 px-2 text-slate-300 whitespace-nowrap">{{ $formatPassage($study) }}</td>
                                        <td class="py-3 px-2">
                                            <button type="button" @click="openAttachments(@js($rowData[$study->id]))"
                                                title="{{ __('dashboard/index.manage_attachments') }}"
                                                aria-label="{{ __('dashboard/index.attachments') }}: {{ $study->attachments_count }}"
                                                class="inline-flex items-center justify-center gap-1.5 min-w-9 px-2.5 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                                <i class="fa-solid fa-paperclip" aria-hidden="true"></i>{{ $study->attachments_count }}
                                            </button>
                                        </td>
                                        <td class="py-3 px-2 whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" @click="openEdit(@js($rowData[$study->id]))"
                                                    class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                                    <i class="fas fa-pen mr-1"></i>{{ __('dashboard/index.edit') }}
                                                </button>
                                                <button type="button" @click="openDelete(@js($rowData[$study->id]))" aria-label="{{ __('dashboard/index.delete_study') }}"
                                                    class="px-2 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-slate-400">
                                            {{ __('dashboard/index.no_studies') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile: Stacked Card Layout -->
                    <div class="md:hidden px-4 pb-4 space-y-3">
                        @forelse ($studies as $study)
                            <div class="flex gap-3 border border-slate-800 rounded-sm p-3">
                                <img src="{{ $study->imageUrl('square') }}" alt="{{ $study->current_title }}"
                                    class="size-16 shrink-0 rounded-sm object-cover border border-slate-700">

                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-slate-100">
                                        <span class="text-slate-400 font-normal">#{{ $study->id }}</span>
                                        {{ $isFrench ? $study->title_fr : $study->title_en }}
                                    </div>
                                    <div class="text-slate-400 text-xs mb-2">
                                        ({{ $isFrench ? $study->title_en : $study->title_fr }})
                                    </div>

                                    <div class="text-slate-300 text-sm">
                                        {{ __('dashboard/index.bible_passage') }}: {{ $formatPassage($study) }}
                                    </div>
                                    <div class="text-slate-300 text-sm flex items-center gap-2 mt-1 mb-3">
                                        {{ __('dashboard/index.attachments') }}:
                                        <button type="button" @click="openAttachments(@js($rowData[$study->id]))"
                                            title="{{ __('dashboard/index.manage_attachments') }}"
                                            aria-label="{{ __('dashboard/index.attachments') }}: {{ $study->attachments_count }}"
                                            class="inline-flex items-center justify-center gap-1.5 min-w-9 px-2.5 py-1 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                            <i class="fa-solid fa-paperclip" aria-hidden="true"></i>{{ $study->attachments_count }}
                                        </button>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="openEdit(@js($rowData[$study->id]))"
                                            class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                            <i class="fas fa-pen mr-1"></i>{{ __('dashboard/index.edit') }}
                                        </button>
                                        <button type="button" @click="openDelete(@js($rowData[$study->id]))" aria-label="{{ __('dashboard/index.delete_study') }}"
                                            class="px-2 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-slate-400">{{ __('dashboard/index.no_studies') }}</div>
                        @endforelse
                    </div>

                    @if ($studies->hasPages())
                        <div class="px-4 pb-4">
                            {{ $studies->links('pagination.dashboard') }}
                        </div>
                    @endif

                </div>

            @endif

            <!-- END UI Segment: Manage Studies -->

        </main>

        <!-- AlpineJS Modal for Adding a New Bible Study -->
        <div x-show="showAddStudiesModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition>

            <div @click.away="showAddStudiesModal = false"
                class="bg-slate-800 rounded-sm max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 shadow-xl border dark:border-slate-700">

                <h3 class="text-lg font-bold mb-4">{{ __('dashboard/index.add_study') }}</h3>

                <form class="w-full" action="{{ route('study.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Series, Book, Passage and Titles (EN / FR), all optional; errors come from the "createStudy" bag -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3">
                        <x-inputs.select id="add_study_series_id" name="study_series_id" bag="createStudy" model="addStudy.study_series_id"
                            :options="$seriesOptions" :label="__('dashboard/index.study_series')" />

                        <x-inputs.select id="add_book_id" name="book_id" bag="createStudy" model="addStudy.book_id"
                            :options="$bookOptions" :label="__('dashboard/index.bible_book')" />
                    </div>

                    <x-inputs.text class="mb-3" id="add_bible_passage" name="bible_passage" bag="createStudy" model="addStudy.bible_passage"
                        :label="__('dashboard/index.bible_passage')" :placeholder="__('dashboard/index.passage_placeholder')" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-3 mb-4">
                        <x-inputs.text id="add_title_en" name="title_en" bag="createStudy" model="addStudy.title_en"
                            :label="__('dashboard/index.title_en')" />

                        <x-inputs.text id="add_title_fr" name="title_fr" bag="createStudy" model="addStudy.title_fr"
                            :label="__('dashboard/index.title_fr')" />
                    </div>

                    <!-- Optional images (shared by EN and FR); empty slots fall back to the series image -->
                    <p class="text-xs text-slate-400 mb-2">{{ __('dashboard/index.images_hint') }}</p>

                    @foreach ($imageTypes as $type => $typeLabelKey)
                        <x-inputs.file :id="'add_' . $type" :name="$type" bag="createStudy" accept="image/jpeg,image/png,image/webp"
                            :label="__('dashboard/index.' . $typeLabelKey)" />
                    @endforeach

                    <!-- Modal Action Controls -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showAddStudiesModal = false"
                            class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors hover:outline-2 cursor-pointer">
                            {{ __('dashboard/index.cancel') }}
                        </button>

                        <x-submit>
                            {{ __('dashboard/index.create') }}
                        </x-submit>
                    </div>
                </form>
            </div>
        </div>

        <!-- AlpineJS Modal for Editing a Bible Study (one shared modal, filled by openEdit()) -->
        <div x-show="showEditStudiesModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition>

            <div @click.away="showEditStudiesModal = false"
                class="bg-slate-800 rounded-sm max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 shadow-xl border dark:border-slate-700">

                <h3 class="text-lg font-bold mb-4">
                    {{ __('dashboard/index.edit_study') }}
                </h3>

                <form x-ref="editStudyForm" class="w-full" :action="editStudy.update_url" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Lets the page reopen this modal for the same study after a failed update -->
                    <input type="hidden" name="study_id" :value="editStudy.id">

                    <!-- Current desktop image (16:9), full width, for reference while replacing images -->
                    <div class="mb-4 flex items-center justify-center" x-show="editStudy.desktop">
                        <img :src="editStudy.desktop" alt="" class="w-full aspect-video rounded-sm object-cover border-2 border-slate-600">
                    </div>

                    <!-- Series, Book, Passage and Titles (EN / FR); errors come from the "updateStudy" bag -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3">
                        <x-inputs.select id="edit_study_series_id" name="study_series_id" bag="updateStudy" model="editStudy.study_series_id"
                            :options="$seriesOptions" :label="__('dashboard/index.study_series')" />

                        <x-inputs.select id="edit_book_id" name="book_id" bag="updateStudy" model="editStudy.book_id"
                            :options="$bookOptions" :label="__('dashboard/index.bible_book')" />
                    </div>

                    <x-inputs.text class="mb-3" id="edit_bible_passage" name="bible_passage" bag="updateStudy" model="editStudy.bible_passage"
                        :label="__('dashboard/index.bible_passage')" :placeholder="__('dashboard/index.passage_placeholder')" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-3 mb-4">
                        <x-inputs.text id="edit_title_en" name="title_en" bag="updateStudy" model="editStudy.title_en"
                            :label="__('dashboard/index.title_en')" />

                        <x-inputs.text id="edit_title_fr" name="title_fr" bag="updateStudy" model="editStudy.title_fr"
                            :label="__('dashboard/index.title_fr')" />
                    </div>

                    <!-- Optional replacements (shared by EN and FR); slots left empty keep their current image -->
                    <p class="text-xs text-slate-400 mb-2">{{ __('dashboard/index.images_replace_hint') }}</p>

                    @foreach ($imageTypes as $type => $typeLabelKey)
                        <x-inputs.file :id="'edit_' . $type" :name="$type" bag="updateStudy"
                            :current="'editStudy.images?.' . $type" accept="image/jpeg,image/png,image/webp"
                            :label="__('dashboard/index.' . $typeLabelKey)" />
                    @endforeach

                    <!-- Modal Action Controls: Attachments on the left, Cancel / Update on the right -->
                    <div class="flex items-center justify-between gap-3">
                        <!-- Same as the list's paperclip button: swaps this modal for the Attachments modal (unsaved edits are dropped) -->
                        <button type="button" x-show="editStudy.attachments_url"
                            @click="showEditStudiesModal = false; openAttachments(editStudy)"
                            title="{{ __('dashboard/index.manage_attachments') }}"
                            :aria-label="@js(__('dashboard/index.attachments')) + ': ' + editStudy.attachments"
                            class="inline-flex items-center justify-center gap-1.5 min-w-9 px-3 py-2 bg-sky-900 hover:bg-sky-950 text-white text-sm font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                            <i class="fa-solid fa-paperclip" aria-hidden="true"></i><span x-text="editStudy.attachments"></span>
                        </button>

                        <div class="flex space-x-3 ml-auto">
                            <button type="button" @click="showEditStudiesModal = false"
                                class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors hover:outline-2 cursor-pointer">
                                {{ __('dashboard/index.cancel') }}
                            </button>

                            <x-submit>
                                {{ __('dashboard/index.update') }}
                            </x-submit>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- AlpineJS Modal for Deleting a Bible Study (filled by openDelete()) -->
        <div x-show="showDeleteStudiesModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition>

            <div @click.away="showDeleteStudiesModal = false"
                class="bg-slate-800 rounded-sm max-w-md w-full p-6 shadow-xl border dark:border-slate-700 text-center whitespace-normal">

                <h3 class="text-lg font-bold mb-2">{{ __('dashboard/index.delete_study_confirm') }}</h3>
                <p class="text-slate-100 mb-2" x-text="deleteStudy.name || '#' + deleteStudy.id"></p>

                <!-- Attachment rows are deleted with the study (cascadeOnDelete) -->
                <p class="text-sm text-amber-300 mb-4" x-show="deleteStudy.attachments > 0"
                    x-text="deleteStudy.attachments_deleted"></p>

                <form :action="deleteStudy.destroy_url" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="flex justify-center space-x-3">
                        <button type="button" @click="showDeleteStudiesModal = false"
                            class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors cursor-pointer">
                            {{ __('dashboard/index.no') }}
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-medium rounded-sm transition-colors cursor-pointer">
                            {{ __('dashboard/index.yes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- AlpineJS Modal for Managing a Bible Study's Attachments (filled by openAttachments()) -->
        <div x-show="showAttachmentsModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition>

            <div @click.away="showAttachmentsModal = false"
                class="bg-slate-800 rounded-sm max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 shadow-xl border dark:border-slate-700">

                <h3 class="text-lg font-bold">{{ __('dashboard/index.manage_attachments') }}</h3>
                <p class="text-sm text-slate-300 mb-4">
                    <span x-text="attachmentsStudy.name || '#' + attachmentsStudy.id"></span>
                    <span class="text-slate-400" x-text="'· ' + attachmentsStudy.passage"></span>
                </p>

                <!-- Result of the last upload / delete (the modal reopens on top of the page's own message) -->
                @if ($attachmentsStudy && session('status'))
                    <div class="w-fit p-2 mb-4 border rounded-sm border-emerald-600 text-emerald-400 text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- One column per language, one group per type; each column scrolls on its own when its list is long -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    @foreach ($attachmentLocales as $locale)
                        <section class="border border-slate-600 rounded-sm px-3 pb-3 max-h-80 overflow-y-auto">
                            <!-- Language name stays visible while scrolling -->
                            <h4 class="sticky top-0 z-10 bg-slate-800 pt-3 pb-2 font-bold text-slate-100">
                                {{ __('dashboard/index.language_' . $locale) }}
                                <!-- Files in this language, all types together; nothing shown when there are none -->
                                <span x-data="{ get count() { return Object.values(attachmentsStudy.files?.{{ $locale }} ?? {}).reduce((total, files) => total + files.length, 0) } }"
                                    x-text="count ? '(' + count + ')' : ''"></span>
                            </h4>

                            @foreach ($attachmentTypes as $type)
                                <div class="mb-3 last:mb-0">
                                    <h5 class="text-xs uppercase tracking-wider text-slate-400 mb-1">
                                        {{ __('dashboard/index.attachment_' . $type) }}
                                    </h5>

                                    <ul class="space-y-1">
                                        <template x-for="file in attachmentsStudy.files?.{{ $locale }}?.{{ $type }} ?? []" :key="file.id">
                                            <li x-data="{ confirming: false }" class="flex items-center gap-2 text-sm odd:bg-black/50 rounded-sm px-2 py-1">
                                                <i class="fa-solid" aria-hidden="true"
                                                    :class="file.extension === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-file-word text-sky-400'"></i>
                                                <span class="flex-1 min-w-0 truncate text-slate-100" x-text="file.name" :title="file.name"></span>

                                                <!-- PDF previews in a new tab, DOCX downloads; Admin / Elder clicks aren't counted as views -->
                                                <a x-show="!confirming" :href="file.show_url"
                                                    x-data="{ get label() { return file.extension === 'pdf' ? @js(__('dashboard/index.preview_attachment')) : @js(__('dashboard/index.download_attachment')) } }"
                                                    :target="file.extension === 'pdf' ? '_blank' : null" rel="noopener"
                                                    :title="label" :aria-label="label + ' (' + file.views + ')'"
                                                    class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 bg-sky-900 hover:bg-sky-950 text-white text-xs rounded outline-1 outline-white hover:outline-2 cursor-pointer">
                                                    <i class="fa-solid fa-eye" aria-hidden="true"></i><span x-text="file.views"></span>
                                                </a>
                                                <button type="button" x-show="!confirming" @click="confirming = true"
                                                    aria-label="{{ __('dashboard/index.delete_attachment') }}"
                                                    class="shrink-0 px-1.5 py-0.5 bg-red-700 hover:bg-red-800 text-white text-xs rounded outline-1 outline-white hover:outline-2 cursor-pointer">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>

                                                <!-- Inline confirmation instead of a second modal -->
                                                <form x-show="confirming" :action="file.destroy_url" method="POST"
                                                    class="shrink-0 flex items-center gap-1 text-xs">
                                                    @csrf
                                                    @method('DELETE')
                                                    <span class="text-amber-300">{{ __('dashboard/index.delete_attachment') }}?</span>
                                                    <button type="button" @click="confirming = false"
                                                        class="px-1.5 py-0.5 border rounded-sm hover:bg-sky-950/50 cursor-pointer">
                                                        {{ __('dashboard/index.no') }}
                                                    </button>
                                                    <button type="submit"
                                                        class="px-1.5 py-0.5 bg-red-700 hover:bg-red-800 text-white rounded-sm cursor-pointer">
                                                        {{ __('dashboard/index.yes') }}
                                                    </button>
                                                </form>
                                            </li>
                                        </template>

                                        <li class="text-sm text-slate-500 italic px-2"
                                            x-show="!(attachmentsStudy.files?.{{ $locale }}?.{{ $type }} ?? []).length">
                                            {{ __('dashboard/index.no_attachments') }}
                                        </li>
                                    </ul>
                                </div>
                            @endforeach
                        </section>
                    @endforeach
                </div>

                <!-- Upload: language, type and one or more files; errors come from the "uploadAttachments" bag -->
                <form x-ref="uploadAttachmentsForm" :action="attachmentsStudy.attachments_url" method="POST" enctype="multipart/form-data"
                    class="border border-slate-600 rounded-sm p-3 mb-4">
                    @csrf

                    <!-- Lets the page reopen this modal for the same study after a failed upload -->
                    <input type="hidden" name="attachments_study_id" :value="attachmentsStudy.id">

                    <h4 class="font-bold text-slate-100 mb-2">{{ __('dashboard/index.upload_attachments') }}</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3">
                        <x-inputs.select id="upload_locale" name="locale" bag="uploadAttachments" model="uploadForm.locale"
                            :options="$localeOptions" :label="__('dashboard/index.language')" />

                        <x-inputs.select id="upload_type" name="type" bag="uploadAttachments" model="uploadForm.type"
                            :options="$typeOptions" :label="__('dashboard/index.attachment_type')" />
                    </div>

                    <x-inputs.file class="mb-2" id="upload_files" name="files" multiple bag="uploadAttachments"
                        accept=".pdf,.docx,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                        :label="__('dashboard/index.attachment_files')" />

                    <p class="text-xs text-slate-400 mb-3">{{ __('dashboard/index.attachment_files_hint') }}</p>

                    <div class="flex justify-end">
                        <x-submit>
                            <i class="fas fa-upload mr-1"></i>{{ __('dashboard/index.upload') }}
                        </x-submit>
                    </div>
                </form>

                <!-- Modal Action Controls: Edit Bible Study on the left, Close on the right -->
                <div class="flex items-center justify-between gap-3">
                    <!-- Same as the list's Edit button: swaps this modal for the Edit modal of the same study -->
                    <button type="button" @click="showAttachmentsModal = false; openEdit(attachmentsStudy)"
                        class="px-4 py-2 bg-sky-900 hover:bg-sky-950 text-white text-sm font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                        <i class="fas fa-pen mr-1"></i>{{ __('dashboard/index.edit_study') }}
                    </button>

                    <button type="button" @click="showAttachmentsModal = false"
                        class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors hover:outline-2 cursor-pointer">
                        {{ __('dashboard/index.close') }}
                    </button>
                </div>
            </div>
        </div>

    </div>

</x-layout>
