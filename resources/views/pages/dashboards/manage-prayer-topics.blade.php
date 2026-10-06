@php
    use App\Enums\PrayerCategory;
    use App\Enums\Role;
    use Illuminate\Support\Str;

    // Current locale's text first, the other locale's text underneath in parentheses
    $isFrench = app()->getLocale() === 'fr_CA';

    // Each main topic of the page, followed by its subtopics
    $rows = $prayerTopics->getCollection()->flatMap(fn ($prayerTopic) => [$prayerTopic, ...$prayerTopic->subtopics]);

    // Row data handed to the Edit / Delete modals, keyed by prayer topic id
    $rowData = $rows->mapWithKeys(function ($prayerTopic) {
        // Subtopics have none themselves (one level only)
        $subtopics = $prayerTopic->parent_id ? 0 : $prayerTopic->subtopics->count();

        return [
            $prayerTopic->id => [
                'id' => $prayerTopic->id,
                'parent_id' => (string) ($prayerTopic->parent_id ?? ''), // String: Alpine matches <option> values strictly
                'topic' => $prayerTopic->current_topic,
                'topic_en' => $prayerTopic->topic_en,
                'topic_fr' => $prayerTopic->topic_fr,
                'category' => $prayerTopic->category->value,
                'min_role' => $prayerTopic->min_role->value,
                'url' => $prayerTopic->url ?? '',
                'answered' => $prayerTopic->answered,
                'subtopics' => $subtopics,
                'subtopics_kept' => trans_choice('dashboard/manage-prayer-topics/index.delete_subtopics_kept', $subtopics, ['count' => $subtopics]),
                'update_url' => route('prayer-topics.update', $prayerTopic),
                'destroy_url' => route('prayer-topics.destroy', $prayerTopic),
            ],
        ];
    });

    // Add form state; refilled from old input only after a failed create
    $failedCreate = $errors->createPrayerTopic->any();
    $addTopic = [
        'parent_id' => $failedCreate ? (string) old('parent_id', '') : '',
        'topic_en' => $failedCreate ? old('topic_en', '') : '',
        'topic_fr' => $failedCreate ? old('topic_fr', '') : '',
        'category' => $failedCreate ? old('category', PrayerCategory::GENERAL->value) : PrayerCategory::GENERAL->value,
        'min_role' => $failedCreate ? old('min_role', Role::GUEST->value) : Role::GUEST->value,
        'url' => $failedCreate ? old('url', '') : '',
        'answered' => $failedCreate ? (bool) old('answered', false) : false,
    ];

    // Edit form state; after a failed update, reopen for the same topic with the old input
    $failedUpdateId = $errors->updatePrayerTopic->any() ? (int) old('prayer_topic_id') : null;
    $editTopic = [
        'id' => $failedUpdateId,
        'parent_id' => $failedUpdateId ? (string) old('parent_id', '') : '',
        'topic_en' => $failedUpdateId ? old('topic_en', '') : '',
        'topic_fr' => $failedUpdateId ? old('topic_fr', '') : '',
        'category' => $failedUpdateId ? old('category', '') : '',
        'min_role' => $failedUpdateId ? old('min_role', '') : '',
        'url' => $failedUpdateId ? old('url', '') : '',
        'answered' => $failedUpdateId ? (bool) old('answered', false) : false,
        'subtopics' => $failedUpdateId ? $rowData[$failedUpdateId]['subtopics'] ?? 0 : 0,
        'update_url' => $failedUpdateId ? route('prayer-topics.update', $failedUpdateId) : '',
    ];

    // Category and Minimum role selects: value => label
    $categoryOptions = array_column(PrayerCategory::options(), 'label', 'value');
    $roleOptions = array_column(Role::options(), 'label', 'value');

    // "Subtopic of" select: empty option means a main topic, then every main topic, e.g. "#12 Pray for the fall conference..."
    $parentOptions = ['' => __('dashboard/manage-prayer-topics/index.no_parent')]
        + $mainTopics->mapWithKeys(fn ($mainTopic) => [$mainTopic->id => '#' . $mainTopic->id . ' ' . Str::limit($mainTopic->current_topic, 60)])->all();

    // Field classes of <x-inputs.text-area> and <x-inputs.select>, for the fields written here (named error bag, AlpineJS bindings)
    $textAreaClass = fn ($bag, $name) => 'w-full shadow appearance-none border rounded-sm py-2 px-3 leading-tight focus:outline-none focus:shadow-outline text-sm '
        . ($errors->getBag($bag)->has($name) ? 'border-red-500' : 'border-slate-300');
@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <!-- dashboards/manage-prayer-topics.blade.php -->

    <x-slot name="title">{{ __('header.name') }} - {{ __('dashboard/index.title') }}</x-slot>

    <h2 class='text-right text-2xl font-bold pt-18 pb-6'>{{ __('dashboard/index.welcome', ['name' => $user->name]) }}
    </h2>

    <!-- UI - Left sidebar with main area -->
    <div x-data="{
        sidebarOpen: true,
        showAddTopicModal: {{ $failedCreate ? 'true' : 'false' }},
        showEditTopicModal: {{ $failedUpdateId ? 'true' : 'false' }},
        showDeleteTopicModal: false,
        addTopic: @js($addTopic),
        editTopic: @js($editTopic),
        deleteTopic: {},
        openEdit(topic) {
            this.editTopic = { ...topic };
            this.showEditTopicModal = true;
        },
        openDelete(topic) {
            this.deleteTopic = { ...topic };
            this.showDeleteTopicModal = true;
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

            <!-- UI Segment: Manage Prayer Topics -->
            @if (auth()->user()->canManageRoles())

                <div class="w-full border rounded-sm border-slate-100">

                    <div class="p-4 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-100">{{ __('dashboard/manage-prayer-topics/index.manage-prayer-topics') }}
                        </h2>

                        <button type="button" @click="showAddTopicModal = true"
                            class="px-4 py-2 bg-sky-900 hover:bg-sky-950 text-white font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                            <i class="fas fa-plus mr-1"></i>{{ __('dashboard/manage-prayer-topics/index.add-topic') }}
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

                    <!-- List paginated main topics (10), newest first, each followed by its subtopics -->

                    <!-- Desktop: Table Layout -->
                    <div class="hidden md:block overflow-x-auto px-4 pb-4">

                        <table class="w-full text-center text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-300 uppercase text-xs tracking-wider">
                                    <th class="py-2 px-2">#</th>
                                    <th class="py-2 px-2 text-left">{{ __('dashboard/manage-prayer-topics/index.topic') }}</th>
                                    <th class="py-2 px-2">{{ __('dashboard/manage-prayer-topics/index.category') }}</th>
                                    <th class="py-2 px-2">{{ __('dashboard/manage-prayer-topics/index.min_role') }}</th>
                                    <th class="py-2 px-2">{{ __('dashboard/manage-prayer-topics/index.answered') }}</th>
                                    <th class="py-2 px-2">{{ __('dashboard/manage-prayer-topics/index.updated') }}</th>
                                    <th class="py-2 px-2">{{ __('dashboard/index.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rows as $prayerTopic)
                                    <tr @class([
                                        'border-b border-slate-800 align-middle',
                                        'bg-black/50' => !$prayerTopic->parent_id,
                                    ])>
                                        <td class="py-3 px-2 text-slate-300">{{ $prayerTopic->id }}</td>
                                        <td @class(['py-3 px-2 text-left max-w-md', 'pl-8' => $prayerTopic->parent_id])>
                                            <div class="flex gap-2">
                                                @if ($prayerTopic->parent_id)
                                                    <i class="fa-solid fa-turn-up rotate-90 text-slate-500 mt-1" title="{{ __('dashboard/manage-prayer-topics/index.subtopic') }}"></i>
                                                    <span class="sr-only">{{ __('dashboard/manage-prayer-topics/index.subtopic') }}</span>
                                                @endif
                                                <div class="min-w-0">
                                                    <div class="text-slate-100 line-clamp-3 whitespace-pre-line">{{ $prayerTopic->current_topic }}</div>
                                                    <div class="text-slate-400 text-xs line-clamp-2 whitespace-pre-line">({{ $isFrench ? $prayerTopic->topic_en : $prayerTopic->topic_fr }})</div>
                                                    @if ($prayerTopic->url)
                                                        <a href="{{ $prayerTopic->url }}" target="_blank" rel="noopener noreferrer"
                                                            title="{{ __('dashboard/manage-prayer-topics/index.open_link') }}"
                                                            class="inline-block max-w-full truncate text-xs text-sky-400 hover:text-sky-300 underline">
                                                            <i class="fa-solid fa-link mr-1" aria-hidden="true"></i>{{ $prayerTopic->url }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-2 text-slate-300">{{ $prayerTopic->category->label() }}</td>
                                        <td class="py-3 px-2 text-slate-300">{{ $prayerTopic->min_role->label() }}</td>
                                        <td class="py-3 px-2">
                                            @if ($prayerTopic->answered)
                                                <i class="fa-solid fa-circle-check text-emerald-400" title="{{ __('dashboard/manage-prayer-topics/index.answered') }}"></i>
                                                <span class="sr-only">{{ __('dashboard/manage-prayer-topics/index.answered') }}</span>
                                            @else
                                                <span class="text-slate-500" title="{{ __('dashboard/manage-prayer-topics/index.not_answered') }}">—</span>
                                                <span class="sr-only">{{ __('dashboard/manage-prayer-topics/index.not_answered') }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-2 text-slate-300 whitespace-nowrap">{{ $prayerTopic->updated_at->isoFormat('ll') }}</td>
                                        <td class="py-3 px-2 whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-2">
                                                <button type="button" @click="openEdit(@js($rowData[$prayerTopic->id]))"
                                                    class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                                    <i class="fas fa-pen mr-1"></i>{{ __('dashboard/index.edit') }}
                                                </button>
                                                <button type="button" @click="openDelete(@js($rowData[$prayerTopic->id]))" aria-label="{{ __('dashboard/manage-prayer-topics/index.delete_topic') }}"
                                                    class="px-2 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-slate-400">
                                            {{ __('dashboard/manage-prayer-topics/index.no_topics') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile: Stacked Card Layout (subtopics indented under their main topic) -->
                    <div class="md:hidden px-4 pb-4 space-y-3">
                        @forelse ($rows as $prayerTopic)
                            <div @class(['border border-slate-800 rounded-sm p-3', 'ml-6' => $prayerTopic->parent_id])>
                                <div class="text-slate-100 whitespace-pre-line">
                                    <span class="text-slate-400">#{{ $prayerTopic->id }}</span>
                                    {{ $prayerTopic->current_topic }}
                                </div>
                                <div class="text-slate-400 text-xs whitespace-pre-line mb-2">
                                    ({{ $isFrench ? $prayerTopic->topic_en : $prayerTopic->topic_fr }})
                                </div>

                                @if ($prayerTopic->url)
                                    <a href="{{ $prayerTopic->url }}" target="_blank" rel="noopener noreferrer"
                                        title="{{ __('dashboard/manage-prayer-topics/index.open_link') }}"
                                        class="block truncate text-xs text-sky-400 hover:text-sky-300 underline mb-2">
                                        <i class="fa-solid fa-link mr-1" aria-hidden="true"></i>{{ $prayerTopic->url }}
                                    </a>
                                @endif

                                @if ($prayerTopic->parent_id)
                                    <div class="text-slate-300 text-sm">
                                        {{ __('dashboard/manage-prayer-topics/index.parent') }}: #{{ $prayerTopic->parent_id }}
                                    </div>
                                @endif
                                <div class="text-slate-300 text-sm">
                                    {{ __('dashboard/manage-prayer-topics/index.category') }}: {{ $prayerTopic->category->label() }}
                                </div>
                                <div class="text-slate-300 text-sm">
                                    {{ __('dashboard/manage-prayer-topics/index.min_role') }}: {{ $prayerTopic->min_role->label() }}
                                </div>
                                <div class="text-slate-300 text-sm">
                                    {{ $prayerTopic->answered ? __('dashboard/manage-prayer-topics/index.answered') : __('dashboard/manage-prayer-topics/index.not_answered') }}
                                </div>
                                <div class="text-slate-300 text-sm mb-3">
                                    {{ __('dashboard/manage-prayer-topics/index.updated') }}: {{ $prayerTopic->updated_at->isoFormat('ll') }}
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button" @click="openEdit(@js($rowData[$prayerTopic->id]))"
                                        class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                        <i class="fas fa-pen mr-1"></i>{{ __('dashboard/index.edit') }}
                                    </button>
                                    <button type="button" @click="openDelete(@js($rowData[$prayerTopic->id]))" aria-label="{{ __('dashboard/manage-prayer-topics/index.delete_topic') }}"
                                        class="px-2 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-slate-400">{{ __('dashboard/manage-prayer-topics/index.no_topics') }}</div>
                        @endforelse
                    </div>

                    @if ($prayerTopics->hasPages())
                        <div class="px-4 pb-4">
                            {{ $prayerTopics->links('pagination.dashboard') }}
                        </div>
                    @endif

                </div>

            @endif

            <!-- END UI Segment: Manage Prayer Topics -->

        </main>

        <!-- AlpineJS Modal for Adding a New Prayer Topic -->
        <div x-show="showAddTopicModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition>

            <div @click.away="showAddTopicModal = false"
                class="bg-slate-800 rounded-sm max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 shadow-xl border dark:border-slate-700">

                <h3 class="text-lg font-bold mb-4">{{ __('dashboard/manage-prayer-topics/index.add-topic') }}</h3>

                <form class="w-full" action="{{ route('prayer-topics.store') }}" method="POST">
                    @csrf

                    <!-- Topic (EN / FR); errors come from the "createPrayerTopic" bag -->
                    @foreach (['topic_en', 'topic_fr'] as $field)
                        <div class="w-full mb-3">
                            <label class="block text-sm font-medium text-slate-100 mb-1.5"
                                for="add_{{ $field }}">{{ __('dashboard/manage-prayer-topics/index.' . $field) }}</label>
                            <textarea id="add_{{ $field }}" name="{{ $field }}" rows="4" maxlength="2048" x-model="addTopic.{{ $field }}"
                                class="{{ $textAreaClass('createPrayerTopic', $field) }}"></textarea>
                            @if ($errors->createPrayerTopic->has($field))
                                <p class="text-xs text-red-500 mt-1">{{ $errors->createPrayerTopic->first($field) }}</p>
                            @endif
                        </div>
                    @endforeach

                    <!-- Subtopic of: a main topic, or none for a main topic -->
                    <x-inputs.select class="mb-3" id="add_parent_id" name="parent_id" bag="createPrayerTopic" model="addTopic.parent_id"
                        :options="$parentOptions" :label="__('dashboard/manage-prayer-topics/index.parent')" />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3">
                        <x-inputs.select class="mb-3" id="add_category" name="category" bag="createPrayerTopic" model="addTopic.category"
                            :options="$categoryOptions" :label="__('dashboard/manage-prayer-topics/index.category')" />

                        <x-inputs.select class="mb-1" id="add_min_role" name="min_role" bag="createPrayerTopic" model="addTopic.min_role"
                            :options="$roleOptions" :label="__('dashboard/manage-prayer-topics/index.min_role')" />
                    </div>
                    <p class="text-xs text-slate-400 mb-3">{{ __('dashboard/manage-prayer-topics/index.min_role_hint') }}</p>

                    <x-inputs.text class="mb-4" id="add_url" name="url" type="url" bag="createPrayerTopic" model="addTopic.url"
                        :label="__('dashboard/manage-prayer-topics/index.url')" :placeholder="__('dashboard/manage-prayer-topics/index.url_placeholder')" />

                    <label class="flex items-center gap-2 text-sm font-medium text-slate-100 mb-4 cursor-pointer">
                        <input type="checkbox" name="answered" value="1" x-model="addTopic.answered" class="size-4 cursor-pointer">
                        {{ __('dashboard/manage-prayer-topics/index.answered') }}
                    </label>

                    <!-- Modal Action Controls -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showAddTopicModal = false"
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

        <!-- AlpineJS Modal for Editing a Prayer Topic (one shared modal, filled by openEdit()) -->
        <div x-show="showEditTopicModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition>

            <div @click.away="showEditTopicModal = false"
                class="bg-slate-800 rounded-sm max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 shadow-xl border dark:border-slate-700">

                <h3 class="text-lg font-bold mb-4">
                    {{ __('dashboard/manage-prayer-topics/index.edit_topic') }} <span class="text-slate-400 font-normal" x-text="'#' + editTopic.id"></span>
                </h3>

                <form class="w-full" :action="editTopic.update_url" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Lets the page reopen this modal for the same topic after a failed update -->
                    <input type="hidden" name="prayer_topic_id" :value="editTopic.id">

                    <!-- Topic (EN / FR); errors come from the "updatePrayerTopic" bag -->
                    @foreach (['topic_en', 'topic_fr'] as $field)
                        <div class="w-full mb-3">
                            <label class="block text-sm font-medium text-slate-100 mb-1.5"
                                for="edit_{{ $field }}">{{ __('dashboard/manage-prayer-topics/index.' . $field) }}</label>
                            <textarea id="edit_{{ $field }}" name="{{ $field }}" rows="4" maxlength="2048" x-model="editTopic.{{ $field }}"
                                class="{{ $textAreaClass('updatePrayerTopic', $field) }}"></textarea>
                            @if ($errors->updatePrayerTopic->has($field))
                                <p class="text-xs text-red-500 mt-1">{{ $errors->updatePrayerTopic->first($field) }}</p>
                            @endif
                        </div>
                    @endforeach

                    <!-- Subtopic of: any main topic but itself; a topic that has subtopics stays a main topic (one level only) -->
                    <div class="w-full mb-3">
                        <label class="block text-sm font-medium text-slate-100 mb-1.5"
                            for="edit_parent_id">{{ __('dashboard/manage-prayer-topics/index.parent') }}</label>

                        <div x-show="!editTopic.subtopics"
                            class="relative shadow border rounded {{ $errors->updatePrayerTopic->has('parent_id') ? 'border-red-500' : 'border-slate-300' }}">
                            <select id="edit_parent_id" name="parent_id" x-model="editTopic.parent_id"
                                class="w-full appearance-none border-0 rounded py-2 pl-3 pr-8 leading-tight focus:outline-none text-sm bg-slate-900">
                                @foreach ($parentOptions as $optionValue => $optionLabel)
                                    <option value="{{ $optionValue }}" @if ($optionValue !== '') :disabled="editTopic.id == {{ $optionValue }}" @endif>
                                        {{ $optionLabel }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fa-solid fa-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-300 pointer-events-none"></i>
                        </div>

                        <p class="text-xs text-slate-400" x-show="editTopic.subtopics">{{ __('dashboard/manage-prayer-topics/index.has_subtopics_hint') }}</p>

                        @if ($errors->updatePrayerTopic->has('parent_id'))
                            <p class="text-xs text-red-500 mt-1">{{ $errors->updatePrayerTopic->first('parent_id') }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3">
                        <x-inputs.select class="mb-3" id="edit_category" name="category" bag="updatePrayerTopic" model="editTopic.category"
                            :options="$categoryOptions" :label="__('dashboard/manage-prayer-topics/index.category')" />

                        <x-inputs.select class="mb-1" id="edit_min_role" name="min_role" bag="updatePrayerTopic" model="editTopic.min_role"
                            :options="$roleOptions" :label="__('dashboard/manage-prayer-topics/index.min_role')" />
                    </div>
                    <p class="text-xs text-slate-400 mb-3">{{ __('dashboard/manage-prayer-topics/index.min_role_hint') }}</p>

                    <x-inputs.text class="mb-4" id="edit_url" name="url" type="url" bag="updatePrayerTopic" model="editTopic.url"
                        :label="__('dashboard/manage-prayer-topics/index.url')" :placeholder="__('dashboard/manage-prayer-topics/index.url_placeholder')" />

                    <label class="flex items-center gap-2 text-sm font-medium text-slate-100 mb-4 cursor-pointer">
                        <input type="checkbox" name="answered" value="1" x-model="editTopic.answered" class="size-4 cursor-pointer">
                        {{ __('dashboard/manage-prayer-topics/index.answered') }}
                    </label>

                    <!-- Modal Action Controls -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showEditTopicModal = false"
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

        <!-- AlpineJS Modal for Deleting a Prayer Topic (filled by openDelete()) -->
        <div x-show="showDeleteTopicModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition>

            <div @click.away="showDeleteTopicModal = false"
                class="bg-slate-800 rounded-sm max-w-md w-full p-6 shadow-xl border dark:border-slate-700 text-center whitespace-normal">

                <h3 class="text-lg font-bold mb-2">{{ __('dashboard/manage-prayer-topics/index.delete_topic_confirm') }}</h3>
                <p class="text-slate-100 mb-2 line-clamp-4" x-text="deleteTopic.topic"></p>

                <!-- Subtopics are kept; the foreign key sets their parent_id to null -->
                <p class="text-sm text-amber-300 mb-2" x-show="deleteTopic.subtopics > 0"
                    x-text="deleteTopic.subtopics_kept"></p>

                <form class="mt-4" :action="deleteTopic.destroy_url" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="flex justify-center space-x-3">
                        <button type="button" @click="showDeleteTopicModal = false"
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
