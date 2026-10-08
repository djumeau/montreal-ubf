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
                'image' => $prayerTopic->image ?? '', // Current file name
                'image_url' => $prayerTopic->image_url,
                'remove_image' => false,
                'answered' => $prayerTopic->answered,
                'subtopics' => $subtopics,
                'subtopics_kept' => trans_choice('dashboard/manage-prayer-topics/index.delete_subtopics_kept', $subtopics, ['count' => $subtopics]),
                'update_url' => route('prayer-topics.update', $prayerTopic),
                'destroy_url' => route('prayer-topics.destroy', $prayerTopic),
            ],
        ];
    });

    // Move up / Move down buttons, keyed by prayer topic id: off for the first and the last of a list
    // (the main topics, over every page, or the subtopics of one main topic)
    $canMove = [];
    foreach ($prayerTopics as $index => $mainTopic) {
        $canMove[$mainTopic->id] = [
            'up' => !($prayerTopics->onFirstPage() && $index === 0),
            'down' => $prayerTopics->hasMorePages() || $index < $prayerTopics->count() - 1,
        ];

        foreach ($mainTopic->subtopics->values() as $subIndex => $subtopic) {
            $canMove[$subtopic->id] = [
                'up' => $subIndex > 0,
                'down' => $subIndex < $mainTopic->subtopics->count() - 1,
            ];
        }
    }

    // Subtopics modal: the row data of each main topic's subtopics, keyed by the main topic's id
    $subtopicRows = $prayerTopics->getCollection()->mapWithKeys(fn ($prayerTopic) => [
        $prayerTopic->id => $prayerTopic->subtopics->map(fn ($subtopic) => $rowData[$subtopic->id])->values(),
    ]);

    // Add form state; refilled from old input only after a failed create
    $failedCreate = $errors->createPrayerTopic->any();
    // "+ Add Sub-topic" on a main topic opens this form for a subtopic of it: parent_id is sent, parent_label is shown
    $addParent = $failedCreate ? $mainTopics->firstWhere('id', (int) old('parent_id')) : null;
    $addTopic = [
        'parent_id' => $addParent ? (string) $addParent->id : '',
        'parent_label' => $addParent ? '#' . $addParent->id . ' ' . $addParent->current_topic : '',
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
        'image' => $failedUpdateId ? $rowData[$failedUpdateId]['image'] ?? '' : '',
        'image_url' => $failedUpdateId ? $rowData[$failedUpdateId]['image_url'] ?? null : null,
        'remove_image' => $failedUpdateId ? (bool) old('remove_image', false) : false,
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
        showSubtopicsModal: false,
        subtopicsTopic: {}, // The main topic whose subtopics the modal lists
        subtopics: [],
        addTopic: @js($addTopic),
        editTopic: @js($editTopic),
        deleteTopic: {},
        // Create New Prayer Topic button (a main topic), or the Add Sub-topic button on a main topic: a subtopic of it, starting with its category and minimum role
        openAdd(parent = null) {
            this.addTopic = {
                ...this.addTopic,
                parent_id: parent ? String(parent.id) : '',
                parent_label: parent ? '#' + parent.id + ' ' + parent.topic : '',
                ...(parent ? { category: parent.category, min_role: parent.min_role } : {}),
            };
            this.showAddTopicModal = true;
        },
        // Subtopics button on a main topic: the list of its subtopics, each with its Edit button
        openSubtopics(topic, subtopics) {
            this.subtopicsTopic = { ...topic };
            this.subtopics = subtopics;
            this.showSubtopicsModal = true;
        },
        openEdit(topic) {
            // Clear the file input left over from a previously edited row (change event resets the shown file name)
            this.$refs.editTopicForm.querySelectorAll('input[type=file]').forEach(input => {
                input.value = '';
                input.dispatchEvent(new Event('change'));
            });
            this.editTopic = { ...topic };
            this.showEditTopicModal = true;
        },
        openDelete(topic) {
            this.deleteTopic = { ...topic };
            this.showDeleteTopicModal = true;
        },
    }" class="flex min-h-screen text-white">

        <x-dashboards::left-column />

        <!-- Right Column: Interactive Work Space Context -->
        <main :class="sidebarOpen ? 'hidden md:block' : 'block'" class="flex-1 pl-6 overflow-y-auto">

            <!-- UI Segment: Manage Prayer Topics -->
            @if (auth()->user()->canManageRoles())

                <div class="w-full border rounded-sm border-slate-100">

                    <div class="p-4 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-100">{{ __('dashboard/manage-prayer-topics/index.manage-prayer-topics') }}
                        </h2>

                        <button type="button" @click="openAdd()"
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

                    <!-- List paginated main topics (5), in their chosen order (Move up / Move down), each followed by its subtopics -->

                    <!-- Desktop: Table Layout -->
                    <div class="hidden md:block overflow-x-auto px-4 pb-4">

                        <table class="w-full text-center text-sm">
                            <x-manage-prayer-topics::list.table-head />
                            <tbody>
                                @forelse ($rows as $prayerTopic)
                                    <tr @class([
                                        'border-b border-slate-800 align-middle',
                                        'bg-black/50' => $loop->odd,
                                    ])>

                                        <x-manage-prayer-topics::list.topic-id :prayer-topic="$prayerTopic" />

                                        <x-manage-prayer-topics::list.topic-move :prayer-topic="$prayerTopic" :can-move="$canMove[$prayerTopic->id]" />

                                        <x-manage-prayer-topics::list.topic-subject :prayer-topic="$prayerTopic" />

                                        <x-manage-prayer-topics::list.topic-category :prayer-topic="$prayerTopic" />

                                        <x-manage-prayer-topics::list.topic-role :prayer-topic="$prayerTopic" />

                                        <x-manage-prayer-topics::list.topic-answered :prayer-topic="$prayerTopic" />

                                        <td class="py-3 px-2 text-slate-300 whitespace-nowrap">{{ $prayerTopic->updated_at->isoFormat('ll') }}</td>
                                        <td class="py-3 px-2 whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-2">
                                                @unless ($prayerTopic->parent_id)
                                                    <button type="button" @click="openSubtopics(@js($rowData[$prayerTopic->id]), @js($subtopicRows[$prayerTopic->id]))"
                                                        class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                                        <i class="fa-solid fa-list mr-1"></i>{{ trans_choice('dashboard/manage-prayer-topics/index.subtopics_count', $prayerTopic->subtopics->count(), ['count' => $prayerTopic->subtopics->count()]) }}
                                                    </button>
                                                    <button type="button" @click="openAdd(@js($rowData[$prayerTopic->id]))"
                                                        class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                                        <i class="fas fa-plus mr-1"></i>{{ __('dashboard/manage-prayer-topics/index.add_subtopic') }}
                                                    </button>
                                                @endunless
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
                                        <td colspan="8" class="py-6 text-center text-slate-400">
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
                                @if ($prayerTopic->image_url)
                                    <img src="{{ $prayerTopic->image_url }}" alt=""
                                        class="size-16 mb-2 rounded-sm object-cover border border-slate-700">
                                @endif
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
                                    @foreach (['up' => 'fa-arrow-up', 'down' => 'fa-arrow-down'] as $direction => $icon)
                                        <form action="{{ route('prayer-topics.move', $prayerTopic) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="direction" value="{{ $direction }}">
                                            <button type="submit" @disabled(!$canMove[$prayerTopic->id][$direction])
                                                title="{{ __('dashboard/manage-prayer-topics/index.move_' . $direction) }}" aria-label="{{ __('dashboard/manage-prayer-topics/index.move_' . $direction) }}"
                                                class="px-2 py-1.5 bg-slate-700 hover:bg-slate-600 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer disabled:opacity-30 disabled:cursor-default disabled:hover:bg-slate-700 disabled:hover:outline-1">
                                                <i class="fa-solid {{ $icon }}"></i>
                                            </button>
                                        </form>
                                    @endforeach
                                    @unless ($prayerTopic->parent_id)
                                        <button type="button" @click="openSubtopics(@js($rowData[$prayerTopic->id]), @js($subtopicRows[$prayerTopic->id]))"
                                            class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                            <i class="fa-solid fa-list mr-1"></i>{{ trans_choice('dashboard/manage-prayer-topics/index.subtopics_count', $prayerTopic->subtopics->count(), ['count' => $prayerTopic->subtopics->count()]) }}
                                        </button>
                                        <button type="button" @click="openAdd(@js($rowData[$prayerTopic->id]))"
                                            class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                            <i class="fas fa-plus mr-1"></i>{{ __('dashboard/manage-prayer-topics/index.add_subtopic') }}
                                        </button>
                                    @endunless
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

                <h3 class="text-lg font-bold mb-4"
                    x-text="addTopic.parent_id ? @js(__('dashboard/manage-prayer-topics/index.add_subtopic')) : @js(__('dashboard/manage-prayer-topics/index.add-topic'))">
                    {{ __('dashboard/manage-prayer-topics/index.add-topic') }}</h3>

                <form class="w-full" action="{{ route('prayer-topics.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Opened with "+ Add Sub-topic": the main topic it goes under (sent as parent_id); empty for a main topic -->
                    <input type="hidden" name="parent_id" :value="addTopic.parent_id">

                    <div x-show="addTopic.parent_id" class="w-full mb-3">
                        <div class="block text-sm font-medium text-slate-100 mb-1.5">{{ __('dashboard/manage-prayer-topics/index.parent') }}</div>
                        <p class="px-3 py-2 text-sm text-slate-200 bg-slate-900 border border-slate-600 rounded-sm line-clamp-3" x-text="addTopic.parent_label"></p>
                        @if ($errors->createPrayerTopic->has('parent_id'))
                            <p class="text-xs text-red-500 mt-1">{{ $errors->createPrayerTopic->first('parent_id') }}</p>
                        @endif
                    </div>

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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3">
                        <x-inputs.select class="mb-3" id="add_category" name="category" bag="createPrayerTopic" model="addTopic.category"
                            :options="$categoryOptions" :label="__('dashboard/manage-prayer-topics/index.category')" />

                        <x-inputs.select class="mb-1" id="add_min_role" name="min_role" bag="createPrayerTopic" model="addTopic.min_role"
                            :options="$roleOptions" :label="__('dashboard/manage-prayer-topics/index.min_role')" />
                    </div>
                    <p class="text-xs text-slate-400 mb-3">{{ __('dashboard/manage-prayer-topics/index.min_role_hint') }}</p>

                    <x-inputs.text class="mb-4" id="add_url" name="url" type="url" bag="createPrayerTopic" model="addTopic.url"
                        :label="__('dashboard/manage-prayer-topics/index.url')" :placeholder="__('dashboard/manage-prayer-topics/index.url_placeholder')" />

                    <!-- Optional image -->
                    <x-inputs.file class="mb-1" id="add_image" name="image" bag="createPrayerTopic" accept="image/jpeg,image/png,image/webp"
                        :label="__('dashboard/manage-prayer-topics/index.image')" />
                    <p class="text-xs text-slate-400 mb-4">{{ __('dashboard/manage-prayer-topics/index.image_hint') }}</p>

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

                <form x-ref="editTopicForm" class="w-full" :action="editTopic.update_url" method="POST" enctype="multipart/form-data">
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

                    <!-- Optional image: a new file replaces the current one, shown here for reference; ticking the box removes it -->
                    <div class="flex items-start gap-3 mb-4">
                        <img x-show="editTopic.image_url" :src="editTopic.image_url" alt=""
                            class="size-16 shrink-0 mt-6 rounded-sm object-cover border-2 border-slate-600">

                        <div class="flex-1 min-w-0">
                            <x-inputs.file class="mb-1" id="edit_image" name="image" bag="updatePrayerTopic" current="editTopic.image" accept="image/jpeg,image/png,image/webp"
                                :label="__('dashboard/manage-prayer-topics/index.image')" />
                            <p class="text-xs text-slate-400 mb-2">{{ __('dashboard/manage-prayer-topics/index.image_hint') }}</p>

                            <label x-show="editTopic.image" class="flex items-center gap-2 text-sm text-slate-100 cursor-pointer">
                                <input type="checkbox" name="remove_image" value="1" x-model="editTopic.remove_image" class="size-4 cursor-pointer">
                                {{ __('dashboard/manage-prayer-topics/index.remove_image') }}
                            </label>
                        </div>
                    </div>

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

        <!-- AlpineJS Modal Listing a Main Topic's Subtopics (filled by openSubtopics()); Edit swaps it for the Edit modal of that subtopic -->
        <div x-show="showSubtopicsModal" x-cloak @keydown.escape.window="showSubtopicsModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition>

            <div @click.away="showSubtopicsModal = false"
                class="bg-slate-800 rounded-sm max-w-lg w-full max-h-[90vh] overflow-y-auto p-6 shadow-xl border dark:border-slate-700">

                <h3 class="text-lg font-bold">{{ __('dashboard/manage-prayer-topics/index.subtopics') }}</h3>
                <p class="text-sm text-slate-300 mb-4 line-clamp-3">
                    <span class="text-slate-400" x-text="'#' + subtopicsTopic.id"></span>
                    <span x-text="subtopicsTopic.topic"></span>
                </p>

                <ul class="space-y-1 mb-4">
                    <template x-for="subtopic in subtopics" :key="subtopic.id">
                        <li class="flex items-start gap-3 text-sm odd:bg-black/50 rounded-sm px-2 py-2">
                            <span class="shrink-0 text-slate-400" x-text="'#' + subtopic.id"></span>

                            <div class="flex-1 min-w-0">
                                <p class="text-slate-100 whitespace-pre-line" x-text="subtopic.topic"></p>
                                <p class="text-xs text-emerald-400" x-show="subtopic.answered">
                                    <i class="fa-solid fa-square-check mr-1" aria-hidden="true"></i>{{ __('dashboard/manage-prayer-topics/index.answered') }}
                                </p>
                            </div>

                            <button type="button" @click="showSubtopicsModal = false; openEdit(subtopic)"
                                class="shrink-0 px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                <i class="fas fa-pen mr-1"></i>{{ __('dashboard/index.edit') }}
                            </button>
                        </li>
                    </template>

                    <li class="text-sm text-slate-500 italic px-2" x-show="!subtopics.length">
                        {{ __('dashboard/manage-prayer-topics/index.no_subtopics') }}
                    </li>
                </ul>

                <!-- Modal Action Controls: Add Sub-topic on the left (swaps this modal for the Add form), Close on the right -->
                <div class="flex items-center justify-between gap-3">
                    <button type="button" @click="showSubtopicsModal = false; openAdd(subtopicsTopic)"
                        class="px-4 py-2 bg-sky-900 hover:bg-sky-950 text-white text-sm font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                        <i class="fas fa-plus mr-1"></i>{{ __('dashboard/manage-prayer-topics/index.add_subtopic') }}
                    </button>

                    <button type="button" @click="showSubtopicsModal = false"
                        class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors hover:outline-2 cursor-pointer">
                        {{ __('dashboard/index.close') }}
                    </button>
                </div>
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
                            class="px-4 py-2 bg-red-700 hover:bg-red-800 border text-white font-medium rounded-sm transition-colors cursor-pointer">
                            {{ __('dashboard/index.yes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-layout>
