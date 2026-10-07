@props([
    'reopened' => false, // The modal reopens after an upload / delete: the result message shows inside it
    'localeOptions' => [], // Language select of the attachment upload: value => label
    'typeOptions' => [], // Type select of the attachment upload: value => label
])

@php
    // Attachment lists: the documents of each language, then the media (no language, shown in both)
    $documentLists = collect(\App\Models\EventAttachment::LOCALES)->mapWithKeys(fn ($locale) => [
        "filesEvent.documents?.{$locale}" => __('dashboard/manage-studies/index.language_' . $locale),
    ]);

    $imageTypes = \App\Models\Event::IMAGE_TYPES;
@endphp

<!-- AlpineJS Modal for Managing an Event's Attachments and Images (filled by openFiles() on the Manage Schedule page,
     which holds showFilesModal, filesEvent and uploadForm) -->
<div x-show="showFilesModal" x-cloak @keydown.escape.window="showFilesModal = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
    x-transition>

    <div x-ref="filesModal" @click.away="showFilesModal = false" role="dialog" aria-modal="true" aria-labelledby="files_modal_title"
        class="bg-slate-800 rounded-sm max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6 shadow-xl border dark:border-slate-700">

        <h3 id="files_modal_title" class="text-lg font-bold">{{ __('dashboard/manage-study-schedule/index.manage_files') }}</h3>
        <p class="text-sm text-slate-300 mb-4">
            <span x-text="filesEvent.name"></span>
            <span class="text-slate-400" x-text="'· ' + filesEvent.when"></span>
        </p>

        <!-- Result of the last upload / delete (the modal reopens on top of the page's own message) -->
        @if ($reopened && session('status'))
            <div class="w-fit p-2 mb-4 border rounded-sm border-emerald-600 text-emerald-400 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <!-- Images: the three slots, each with its current image and a Delete button, then the upload -->
        <section class="border border-slate-600 rounded-sm p-3 mb-4">
            <h4 class="font-bold text-slate-100 mb-2">{{ __('dashboard/manage-study-schedule/index.images') }}</h4>

            <!-- An event linked to a Bible study shows that study's image (or its series') before its own -->
            <p class="text-xs text-amber-300 mb-2" x-show="filesEvent.has_study_image">
                <i class="fa-solid fa-circle-info mr-1" aria-hidden="true"></i>{{ __('dashboard/manage-study-schedule/index.images_study_note') }}
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                @foreach ($imageTypes as $type)
                    <div x-data="{ confirming: false }" class="text-sm">
                        <div class="text-xs uppercase tracking-wider text-slate-400 mb-1">{{ __('dashboard/index.image_' . $type) }}</div>

                        <template x-if="filesEvent.images?.{{ $type }}">
                            <div>
                                <a :href="filesEvent.images.{{ $type }}.url" target="_blank" rel="noopener" title="{{ __('dashboard/index.preview_attachment') }}">
                                    <img :src="filesEvent.images.{{ $type }}.url" alt=""
                                        class="h-24 w-full rounded-sm object-cover border border-slate-600">
                                </a>

                                <div class="flex items-center gap-2 mt-1">
                                    <span class="flex-1 min-w-0 truncate text-xs text-slate-300" x-text="filesEvent.images.{{ $type }}.name" :title="filesEvent.images.{{ $type }}.name"></span>

                                    <button type="button" x-show="!confirming" @click="confirming = true"
                                        aria-label="{{ __('dashboard/manage-study-schedule/index.delete_image') }}" title="{{ __('dashboard/manage-study-schedule/index.delete_image') }}"
                                        class="shrink-0 px-1.5 py-0.5 bg-red-700 hover:bg-red-800 text-white text-xs rounded outline-1 outline-white hover:outline-2 cursor-pointer">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>

                                <!-- Inline confirmation instead of a second modal -->
                                <form x-show="confirming" :action="filesEvent.image_destroy_urls.{{ $type }}" method="POST"
                                    class="flex flex-wrap items-center gap-1 mt-1 text-xs">
                                    @csrf
                                    @method('DELETE')
                                    <span class="text-amber-300">{{ __('dashboard/manage-study-schedule/index.delete_image') }}?</span>
                                    <button type="button" @click="confirming = false"
                                        class="px-1.5 py-0.5 border rounded-sm hover:bg-sky-950/50 cursor-pointer">
                                        {{ __('dashboard/index.no') }}
                                    </button>
                                    <button type="submit"
                                        class="px-1.5 py-0.5 bg-red-700 hover:bg-red-800 text-white rounded-sm cursor-pointer">
                                        {{ __('dashboard/index.yes') }}
                                    </button>
                                </form>
                            </div>
                        </template>

                        <div x-show="!filesEvent.images?.{{ $type }}"
                            class="grid place-items-center h-24 rounded-sm border border-dashed border-slate-600 text-slate-500 italic">
                            {{ __('dashboard/manage-studies/index.no_attachments') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Upload: only the slots given a file are replaced; errors come from the "uploadEventImages" bag -->
            <form :action="filesEvent.images_url" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Lets the page reopen this modal for the same event after a failed upload -->
                <input type="hidden" name="files_event_id" :value="filesEvent.id">

                <p class="text-xs text-slate-400 mb-2">{{ __('dashboard/index.images_replace_hint') }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-3">
                    @foreach ($imageTypes as $type)
                        <x-inputs.file class="mb-2 min-w-0" id="event_image_{{ $type }}" name="{{ $type }}" bag="uploadEventImages"
                            accept="image/jpeg,image/png,image/webp" :label="__('dashboard/index.image_' . $type)" />
                    @endforeach
                </div>

                <div class="flex justify-end">
                    <x-submit>
                        <i class="fas fa-upload mr-1"></i>{{ __('dashboard/index.upload') }}
                    </x-submit>
                </div>
            </form>
        </section>

        <!-- Attachments: one column per document language, then the media; each list scrolls on its own when it is long -->
        <section class="border border-slate-600 rounded-sm p-3 mb-4">
            <h4 class="font-bold text-slate-100 mb-2">{{ __('dashboard/manage-studies/index.attachments') }}</h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3 mb-3">
                @foreach ($documentLists->all() + ['filesEvent.media' => __('dashboard/manage-study-schedule/index.media')] as $list => $heading)
                    <div @class(['max-h-56 overflow-y-auto', 'sm:col-span-2' => $list === 'filesEvent.media'])>
                        <h5 class="sticky top-0 z-10 bg-slate-800 pb-1 text-xs uppercase tracking-wider text-slate-400">
                            {{ $list === 'filesEvent.media' ? $heading : __('dashboard/manage-study-schedule/index.documents') . ' – ' . $heading }}
                            <span x-text="({{ $list }} ?? []).length ? '(' + {{ $list }}.length + ')' : ''"></span>
                        </h5>

                        <ul class="space-y-1">
                            <template x-for="file in {{ $list }} ?? []" :key="file.id">
                                <li x-data="{ confirming: false }" class="flex items-center gap-2 text-sm odd:bg-black/50 rounded-sm px-2 py-1">
                                    <i class="fa-solid" :class="file.icon" aria-hidden="true"></i>
                                    <span class="flex-1 min-w-0 truncate text-slate-100" x-text="file.name" :title="file.name"></span>
                                    <span class="shrink-0 text-xs text-slate-400" x-text="file.size ?? ''"></span>

                                    <!-- PDF, images and videos open in a new tab, DOCX downloads -->
                                    <a x-show="!confirming" :href="file.show_url"
                                        x-data="{ get label() { return file.downloads ? @js(__('dashboard/index.download_attachment')) : @js(__('dashboard/index.preview_attachment')) } }"
                                        :target="file.downloads ? null : '_blank'" rel="noopener" :title="label" :aria-label="label"
                                        class="shrink-0 px-1.5 py-0.5 bg-sky-900 hover:bg-sky-950 text-white text-xs rounded outline-1 outline-white hover:outline-2 cursor-pointer">
                                        <i class="fa-solid" :class="file.downloads ? 'fa-download' : 'fa-eye'" aria-hidden="true"></i>
                                    </a>
                                    <button type="button" x-show="!confirming" @click="confirming = true"
                                        aria-label="{{ __('dashboard/manage-studies/index.delete_attachment') }}"
                                        class="shrink-0 px-1.5 py-0.5 bg-red-700 hover:bg-red-800 text-white text-xs rounded outline-1 outline-white hover:outline-2 cursor-pointer">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>

                                    <!-- Inline confirmation instead of a second modal -->
                                    <form x-show="confirming" :action="file.destroy_url" method="POST"
                                        class="shrink-0 flex items-center gap-1 text-xs">
                                        @csrf
                                        @method('DELETE')
                                        <span class="text-amber-300">{{ __('dashboard/manage-studies/index.delete_attachment') }}?</span>
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

                            <li class="text-sm text-slate-500 italic px-2" x-show="!({{ $list }} ?? []).length">
                                {{ __('dashboard/manage-studies/index.no_attachments') }}
                            </li>
                        </ul>
                    </div>
                @endforeach
            </div>

            <!-- Upload: type, language (documents only) and one or more files; errors come from the "uploadEventFiles" bag -->
            <form :action="filesEvent.attachments_url" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Lets the page reopen this modal for the same event after a failed upload -->
                <input type="hidden" name="files_event_id" :value="filesEvent.id">

                <h5 class="font-bold text-slate-100 mb-2">{{ __('dashboard/manage-studies/index.upload_attachments') }}</h5>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-3">
                    <x-inputs.select id="event_upload_type" name="type" bag="uploadEventFiles" model="uploadForm.type"
                        :options="$typeOptions" :label="__('dashboard/manage-studies/index.attachment_type')" />

                    <!-- Media have no language: they show in both -->
                    <x-inputs.select x-show="uploadForm.type !== 'media'" id="event_upload_locale" name="locale" bag="uploadEventFiles" model="uploadForm.locale"
                        :options="$localeOptions" :label="__('dashboard/manage-studies/index.language')" />
                </div>

                <x-inputs.file class="mb-2" id="event_upload_files" name="files" multiple bag="uploadEventFiles"
                    accept=".pdf,.docx,.png,.jpg,.jpeg,.mp4" :label="__('dashboard/manage-study-schedule/index.attachment_files')" />

                <p class="text-xs text-slate-400 mb-3">{{ __('dashboard/manage-study-schedule/index.attachment_files_hint') }}</p>

                <div class="flex justify-end">
                    <x-submit>
                        <i class="fas fa-upload mr-1"></i>{{ __('dashboard/index.upload') }}
                    </x-submit>
                </div>
            </form>
        </section>

        <!-- Modal Action Controls: back to the Event modal on the left, Close on the right -->
        <div class="flex items-center justify-between gap-3">
            <button type="button" @click="showFilesModal = false; openEdit(filesEvent.id)"
                class="px-4 py-2 bg-sky-900 hover:bg-sky-950 text-white text-sm font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                <i class="fas fa-pen mr-1"></i>{{ __('dashboard/manage-study-schedule/index.edit_event') }}
            </button>

            <button type="button" @click="showFilesModal = false"
                class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors hover:outline-2 cursor-pointer">
                {{ __('dashboard/index.close') }}
            </button>
        </div>
    </div>
</div>
