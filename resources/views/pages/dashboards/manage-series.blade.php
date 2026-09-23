@props(['user' => null])

@php
    // Current locale's name first, the other locale's name underneath in parentheses
    $isFrench = app()->getLocale() === 'fr_CA';

    // Row data handed to the Edit / Delete modals, keyed by series id
    $rowData = $seriesList->getCollection()->mapWithKeys(fn ($series) => [
        $series->id => [
            'id' => $series->id,
            'name' => $isFrench ? $series->name_fr : $series->name_en,
            'name_en' => $series->name_en,
            'name_fr' => $series->name_fr,
            'book_id' => (string) ($series->book_id ?? ''), // String: Alpine matches <option> values strictly
            'dates' => $series->dates ?? '',
            'studies' => $series->bible_studies_count,
            'studies_kept' => trans_choice('dashboard/index.delete_series_studies_kept', $series->bible_studies_count, ['count' => $series->bible_studies_count]),
            'thumbnail' => $series->imageUrl('thumbnail'),
            'images' => $series->images ?? [], // Current file names: desktop, mobile, thumbnail
            'update_url' => route('series.update', $series),
            'destroy_url' => route('series.destroy', $series),
        ],
    ]);

    // Add form state; refilled from old input only after a failed create
    $failedCreate = $errors->createSeries->any();
    $addSeries = [
        'name_en' => $failedCreate ? old('name_en', '') : '',
        'name_fr' => $failedCreate ? old('name_fr', '') : '',
        'book_id' => $failedCreate ? (string) old('book_id', '') : '',
        'dates' => $failedCreate ? old('dates', '') : '',
    ];

    // Edit form state; after a failed update, reopen for the same series with the old input
    $failedUpdateId = $errors->updateSeries->any() ? (int) old('series_id') : null;
    $editSeries = [
        'id' => $failedUpdateId,
        'name_en' => $failedUpdateId ? old('name_en', '') : '',
        'name_fr' => $failedUpdateId ? old('name_fr', '') : '',
        'book_id' => $failedUpdateId ? (string) old('book_id', '') : '',
        'dates' => $failedUpdateId ? old('dates', '') : '',
        'thumbnail' => $failedUpdateId ? $rowData[$failedUpdateId]['thumbnail'] ?? null : null,
        'images' => $failedUpdateId ? $rowData[$failedUpdateId]['images'] ?? [] : [],
        'update_url' => $failedUpdateId ? route('series.update', $failedUpdateId) : '',
    ];

    // Related Book select: empty option means multiple books, then books grouped by testament
    $bookOptions = ['' => __('dashboard/index.multiple')];
    foreach (['ot' => 'old_testament', 'nt' => 'new_testament'] as $testament => $groupKey) {
        $bookOptions[__('dashboard/index.' . $groupKey)] = $books->where('testament', $testament)
            ->mapWithKeys(fn ($book) => [$book->id => $isFrench ? $book->name_fr : $book->name_en])
            ->all();
    }
@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <!-- dashboard/index.blade.php -->

    <x-slot name="title">{{ __('header.name') }} - {{ __('dashboard/index.title') }}</x-slot>

    <h2 class='text-right text-2xl font-bold pt-18 pb-6'>{{ __('dashboard/index.welcome', ['name' => $user->name]) }}
    </h2>

    <!-- UI - Left sidebar with main area -->
    <div x-data="{
        sidebarOpen: true,
        showAddSeriesModal: {{ $failedCreate ? 'true' : 'false' }},
        showEditSeriesModal: {{ $failedUpdateId ? 'true' : 'false' }},
        showDeleteSeriesModal: false,
        addSeries: @js($addSeries),
        editSeries: @js($editSeries),
        deleteSeries: {},
        openEdit(series) {
            // Clear file inputs left over from a previously edited row (change event resets the shown file name)
            this.$refs.editSeriesForm.querySelectorAll('input[type=file]').forEach(input => {
                input.value = '';
                input.dispatchEvent(new Event('change'));
            });
            this.editSeries = { ...series };
            this.showEditSeriesModal = true;
        },
        openDelete(series) {
            this.deleteSeries = { ...series };
            this.showDeleteSeriesModal = true;
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

            <!-- UI Segment: Manage Study Series -->
            @if (auth()->user()->canManageRoles())

                <div class="w-full border rounded-sm border-slate-100">

                    <div class="p-4 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-100">{{ __('dashboard/index.manage-series') }}
                        </h2>

                        <button type="button" @click="showAddSeriesModal = true"
                            class="px-4 py-2 bg-sky-900 hover:bg-sky-950 text-white font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                            <i class="fas fa-plus mr-1"></i>{{ __('dashboard/index.add-series') }}
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

                    <!-- List paginated study series (10) -->

                    <!-- Desktop: Table Layout -->
                    <div class="hidden md:block overflow-x-auto px-4 pb-4">

                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-300 uppercase text-xs tracking-wider">
                                    <th class="py-2 pr-4">#</th>
                                    <th class="py-2 pr-4">{{ __('dashboard/index.image') }}</th>
                                    <th class="py-2 pr-4">{{ __('dashboard/index.name') }}</th>
                                    <th class="py-2 pr-4">{{ __('dashboard/index.related_book') }}</th>
                                    <th class="py-2 pr-4 text-center">{{ __('dashboard/index.studies') }}</th>
                                    <th class="py-2 pr-4">{{ __('dashboard/index.dates') }}</th>
                                    <th class="py-2 pr-4 text-right">{{ __('dashboard/index.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($seriesList as $series)
                                    <tr class="border-b border-slate-800 align-middle">
                                        <td class="py-3 pr-4 text-slate-300">{{ $series->id }}</td>
                                        <td class="py-3 pr-4">
                                            <img src="{{ $series->imageUrl('thumbnail') }}"
                                                alt="{{ $series->name_en }}"
                                                class="size-14 rounded-sm object-cover border border-slate-700">
                                        </td>
                                        <td class="py-3 pr-4">
                                            <div class="text-slate-100">
                                                {{ $isFrench ? $series->name_fr : $series->name_en }}</div>
                                            <div class="text-slate-400 text-xs">
                                                ({{ $isFrench ? $series->name_en : $series->name_fr }})
                                            </div>
                                        </td>
                                        <td class="py-3 pr-4">
                                            @if ($book = $series->book)
                                                <div class="text-slate-100">
                                                    {{ $isFrench ? $book->name_fr : $book->name_en }}</div>
                                                <div class="text-slate-400 text-xs">
                                                    ({{ $isFrench ? $book->name_en : $book->name_fr }})</div>
                                            @else
                                                <span
                                                    class="text-slate-100">{{ __('dashboard/index.multiple') }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4 text-center">
                                            <!-- TODO: replace "#" with the route to this series' Bible studies -->
                                            <a href="#" title="{{ __('dashboard/index.view_studies') }}"
                                                aria-label="{{ __('dashboard/index.view_studies') }}: {{ $series->bible_studies_count }}"
                                                class="inline-flex items-center justify-center min-w-9 px-2.5 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                                {{ $series->bible_studies_count }}
                                            </a>
                                        </td>
                                        <td class="py-3 pr-4 text-slate-300">{{ $series->localized_dates ?? '—' }}</td>
                                        <td class="py-3 pr-4 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button" @click="openEdit(@js($rowData[$series->id]))"
                                                    class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                                    <i class="fas fa-pen mr-1"></i>{{ __('dashboard/index.edit') }}
                                                </button>
                                                <button type="button" @click="openDelete(@js($rowData[$series->id]))" aria-label="{{ __('dashboard/index.delete_series') }}"
                                                    class="px-2 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-slate-400">
                                            {{ __('dashboard/index.no_series') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile: Stacked Card Layout -->
                    <div class="md:hidden px-4 pb-4 space-y-3">
                        @forelse ($seriesList as $series)
                            <div class="flex gap-3 border border-slate-800 rounded-sm p-3">
                                <img src="{{ $series->imageUrl('thumbnail') }}" alt="{{ $series->name_en }}"
                                    class="size-16 shrink-0 rounded-sm object-cover border border-slate-700">

                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-slate-100">
                                        <span class="text-slate-400 font-normal">#{{ $series->id }}</span>
                                        {{ $isFrench ? $series->name_fr : $series->name_en }}
                                    </div>
                                    <div class="text-slate-400 text-xs mb-2">
                                        ({{ $isFrench ? $series->name_en : $series->name_fr }})
                                    </div>

                                    <div class="text-slate-300 text-sm">
                                        {{ __('dashboard/index.related_book') }}:
                                        @if ($book = $series->book)
                                            {{ $isFrench ? $book->name_fr : $book->name_en }}
                                            ({{ $isFrench ? $book->name_en : $book->name_fr }})
                                        @else
                                            {{ __('dashboard/index.multiple') }}
                                        @endif
                                    </div>
                                    <div class="text-slate-300 text-sm flex items-center gap-2 my-1">
                                        {{ __('dashboard/index.studies') }}:
                                        <!-- TODO: replace "#" with the route to this series' Bible studies -->
                                        <a href="#" title="{{ __('dashboard/index.view_studies') }}"
                                            aria-label="{{ __('dashboard/index.view_studies') }}: {{ $series->bible_studies_count }}"
                                            class="inline-flex items-center justify-center min-w-9 px-2.5 py-1 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                            {{ $series->bible_studies_count }}
                                        </a>
                                    </div>
                                    <div class="text-slate-300 text-sm mb-3">
                                        {{ __('dashboard/index.dates') }}: {{ $series->localized_dates ?? '—' }}
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="openEdit(@js($rowData[$series->id]))"
                                            class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                            <i class="fas fa-pen mr-1"></i>{{ __('dashboard/index.edit') }}
                                        </button>
                                        <button type="button" @click="openDelete(@js($rowData[$series->id]))" aria-label="{{ __('dashboard/index.delete_series') }}"
                                            class="px-2 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-slate-400">{{ __('dashboard/index.no_series') }}</div>
                        @endforelse
                    </div>

                    @if ($seriesList->hasPages())
                        <div class="px-4 pb-4 mx-4 mb-4 bg-white text-slate-900 rounded-sm">
                            {{ $seriesList->links() }}
                        </div>
                    @endif

                </div>

            @endif

            <!-- END UI Segment: Manage Study Series -->

        </main>

        <!-- AlpineJS Modal for Adding a New Study Series -->
        <div x-show="showAddSeriesModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition>

            <div @click.away="showAddSeriesModal = false"
                class="bg-slate-800 rounded-sm max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 shadow-xl border dark:border-slate-700">

                <h3 class="text-lg font-bold mb-4">{{ __('dashboard/index.add-series') }}</h3>

                <form class="w-full" action="{{ route('series.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Names (EN / FR) and Dates; errors come from the "createSeries" bag -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-3 mb-3">
                        <x-inputs.text id="add_name_en" name="name_en" bag="createSeries" model="addSeries.name_en"
                            :label="__('dashboard/index.name_en')" />

                        <x-inputs.text id="add_name_fr" name="name_fr" bag="createSeries" model="addSeries.name_fr"
                            :label="__('dashboard/index.name_fr')" />
                    </div>

                    <x-inputs.select class="mb-3" id="add_book_id" name="book_id" bag="createSeries" model="addSeries.book_id"
                        :options="$bookOptions" :label="__('dashboard/index.related_book')" />

                    <x-inputs.text class="mb-4" id="add_dates" name="dates" bag="createSeries" model="addSeries.dates"
                        :label="__('dashboard/index.dates')" :placeholder="__('dashboard/index.dates_placeholder')" />

                    <!-- Optional images; any slot left empty uses the default image -->
                    <p class="text-xs text-slate-400 mb-2">{{ __('dashboard/index.images_hint') }}</p>

                    <x-inputs.file id="add_desktop" name="desktop" bag="createSeries" accept="image/jpeg,image/png,image/webp"
                        :label="__('dashboard/index.image_desktop')" />

                    <x-inputs.file id="add_mobile" name="mobile" bag="createSeries" accept="image/jpeg,image/png,image/webp"
                        :label="__('dashboard/index.image_mobile')" />

                    <x-inputs.file id="add_thumbnail" name="thumbnail" bag="createSeries" accept="image/jpeg,image/png,image/webp"
                        :label="__('dashboard/index.image_thumbnail')" />

                    <!-- Modal Action Controls -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showAddSeriesModal = false"
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

        <!-- AlpineJS Modal for Editing a Study Series (one shared modal, filled by openEdit()) -->
        <div x-show="showEditSeriesModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition>

            <div @click.away="showEditSeriesModal = false"
                class="bg-slate-800 rounded-sm max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 shadow-xl border dark:border-slate-700">

                <h3 class="text-lg font-bold mb-4">
                    {{ __('dashboard/index.edit_series') }} <span class="text-slate-400 font-normal" x-text="'#' + editSeries.id"></span>
                </h3>

                <form x-ref="editSeriesForm" class="w-full" :action="editSeries.update_url" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Lets the page reopen this modal for the same series after a failed update -->
                    <input type="hidden" name="series_id" :value="editSeries.id">

                    <!-- Current thumbnail, for reference while replacing images -->
                    <div class="mb-4 flex items-center justify-center" x-show="editSeries.thumbnail">
                        <img :src="editSeries.thumbnail" alt="" class="size-20 rounded-sm object-cover border-2 border-slate-600">
                    </div>

                    <!-- Names (EN / FR) and Dates; errors come from the "updateSeries" bag -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3 gap-y-3 mb-3">
                        <x-inputs.text id="edit_name_en" name="name_en" bag="updateSeries" model="editSeries.name_en"
                            :label="__('dashboard/index.name_en')" />

                        <x-inputs.text id="edit_name_fr" name="name_fr" bag="updateSeries" model="editSeries.name_fr"
                            :label="__('dashboard/index.name_fr')" />
                    </div>

                    <x-inputs.select class="mb-3" id="edit_book_id" name="book_id" bag="updateSeries" model="editSeries.book_id"
                        :options="$bookOptions" :label="__('dashboard/index.related_book')" />

                    <x-inputs.text class="mb-4" id="edit_dates" name="dates" bag="updateSeries" model="editSeries.dates"
                        :label="__('dashboard/index.dates')" :placeholder="__('dashboard/index.dates_placeholder')" />

                    <!-- Optional replacements; slots left empty keep their current image -->
                    <p class="text-xs text-slate-400 mb-2">{{ __('dashboard/index.images_replace_hint') }}</p>

                    <x-inputs.file id="edit_desktop" name="desktop" bag="updateSeries" current="editSeries.images?.desktop" accept="image/jpeg,image/png,image/webp"
                        :label="__('dashboard/index.image_desktop')" />

                    <x-inputs.file id="edit_mobile" name="mobile" bag="updateSeries" current="editSeries.images?.mobile" accept="image/jpeg,image/png,image/webp"
                        :label="__('dashboard/index.image_mobile')" />

                    <x-inputs.file id="edit_thumbnail" name="thumbnail" bag="updateSeries" current="editSeries.images?.thumbnail" accept="image/jpeg,image/png,image/webp"
                        :label="__('dashboard/index.image_thumbnail')" />

                    <!-- Modal Action Controls -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showEditSeriesModal = false"
                            class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors hover:outline-2 cursor-pointer">
                            {{ __('dashboard/index.cancel') }}
                        </button>

                        <x-submit>
                            {{ __('dashboard/index.update') }}
                        </x-submit>
                    </div>
                </form>
            </div>
        </div>

        <!-- AlpineJS Modal for Deleting a Study Series (filled by openDelete()) -->
        <div x-show="showDeleteSeriesModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition>

            <div @click.away="showDeleteSeriesModal = false"
                class="bg-slate-800 rounded-sm max-w-md w-full p-6 shadow-xl border dark:border-slate-700 text-center whitespace-normal">

                <h3 class="text-lg font-bold mb-2">{{ __('dashboard/index.delete_series_confirm') }}</h3>
                <p class="text-slate-100 mb-2" x-text="deleteSeries.name"></p>

                <!-- Studies are kept; the foreign key sets their study_series_id to null -->
                <p class="text-sm text-amber-300 mb-4" x-show="deleteSeries.studies > 0"
                    x-text="deleteSeries.studies_kept"></p>

                <form :action="deleteSeries.destroy_url" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="flex justify-center space-x-3">
                        <button type="button" @click="showDeleteSeriesModal = false"
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

    </div>

</x-layout>
