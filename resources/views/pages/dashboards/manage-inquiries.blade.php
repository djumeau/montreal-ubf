@php
    // "Thursday, October 1st, 2026, 4:40 PM" / "Jeudi 1 octobre 2026, 16 h 40"
    $dateTime = fn ($date) => $date
        ? ucfirst($date->isoFormat(__('bible-study-schedule/index.list_day_format'))) . ', ' . $date->isoFormat(__('bible-study-schedule/index.time_format'))
        : null;

    // Details of each message of the page, by id, for the Inquiry modal (opened by clicking an item)
    $inquiryDetails = $inquiries->mapWithKeys(fn ($inquiry) => [$inquiry->id => [
        'subject' => $inquiry->inquiry->label(),
        'name' => $inquiry->name,
        'email' => $inquiry->email,
        'signedIn' => $inquiry->user,
        'sentAt' => $dateTime($inquiry->created_at),
        'message' => $inquiry->message,
        // Same order as the item's badge (see inquiry-items/notification): answered, then read, then new
        'status' => match (true) {
            (bool) $inquiry->management?->answered_at => 'answered',
            (bool) $inquiry->management?->read_at => 'read',
            default => 'new',
        },
        'readAt' => $dateTime($inquiry->management?->read_at),
        'answeredAt' => $dateTime($inquiry->management?->answered_at),
        'answeredBy' => $inquiry->management?->answeredBy?->name,
        'note' => $inquiry->management?->note,
        // Opening marks it read (in the background); the Answer section saves to answerUrl
        'readUrl' => route('inquiries.read', $inquiry),
        'answerUrl' => route('inquiries.answer', $inquiry),
    ]]);
@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <!-- dashboards/manage-inquiries.blade.php -->

    <x-slot name="title">{{ __('header.name') }} - {{ __('dashboard/index.title') }}</x-slot>

    <h2 class='text-right text-2xl font-bold pt-18 pb-6'>{{ __('dashboard/index.welcome', ['name' => $user->name]) }}
    </h2>

    <!-- UI - Left sidebar with main area -->
    <div x-data="{
        sidebarOpen: true,
        showAvatarModal: false,
        showDeleteSelectedModal: false,
        selectedCount: 0, // Messages ticked on this page (checkboxes of the inquiry items)
        boxes() {
            return [...document.querySelectorAll('input[name=\'inquiries[]\']')];
        },
        countSelected() {
            this.selectedCount = this.boxes().filter(box => box.checked).length;
        },
        // Select All: ticks every message of the page; when they all are already, unticks them
        selectAll() {
            const boxes = this.boxes();
            const all = boxes.length && boxes.every(box => box.checked);
            boxes.forEach(box => box.checked = !all);
            this.countSelected();
        },
        // Inquiry modal: an item sends open-inquiry with its id; the details come from the page (by id)
        inquiries: @js($inquiryDetails),
        inquiry: null,
        showInquiryModal: false,
        reloadOnClose: false, // A message was marked read: the list's badges are refreshed when the modal closes
        openInquiry(id) {
            this.inquiry = this.inquiries[id] ?? null;
            this.showInquiryModal = this.inquiry !== null;

            // A new message becomes read as soon as it is opened
            if (this.inquiry?.status === 'new') {
                const inquiry = this.inquiry;
                fetch(inquiry.readUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': @js(csrf_token()), 'Accept': 'application/json' },
                })
                    .then(response => response.ok ? response.json() : Promise.reject())
                    .then(data => {
                        inquiry.status = 'read';
                        inquiry.readAt = data.readAt;
                        this.reloadOnClose = true;
                    })
                    .catch(() => {}); // Stays New; it is marked read the next time it is opened
            }
        },
        closeInquiry() {
            this.showInquiryModal = false;
            if (this.reloadOnClose) {
                window.location.reload();
            }
        },
        }"
        @open-inquiry="openInquiry($event.detail)"
        class="flex min-h-screen text-white">

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
                    <button @click="sidebarOpen = !sidebarOpen" class="grid place-items-center size-10 pl-2 text-slate-100 hover:text-slate-300 transition-colors focus:outline-none cursor-pointer">
                        <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                    </button>
                </div>

                <x-profile-info-block/>

                <x-dashboard-features></x-dashboard-features>

            </div>

        </aside>

        <!-- Right Column: Interactive Work Space Context -->
        <main
            :class="sidebarOpen ? 'hidden md:block' : 'block'"
            class="flex-1 pl-6 overflow-y-auto">

            <!-- UI Segment: Contact Submissions (messages sent through the contact page) -->
            @if(auth()->user()->canManageRoles())

                <div class="w-full border rounded-sm border-slate-100">

                    <div class="p-4 flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-lg font-bold text-slate-100">{{ __('dashboard/index.manage_inquiries') }}</h2>

                        <!-- Select All / Delete Selected: Delete Selected removes the ticked messages (asks first), so it stays off until one is ticked.
                             The checkboxes of the items belong to this form through form="delete_selected_form" -->
                        @if ($inquiries->isNotEmpty())
                            <form id="delete_selected_form" action="{{ route('inquiries.destroy-selected') }}" method="POST"
                                class="flex items-center gap-2">
                                @csrf
                                @method('DELETE')

                                <button type="button" @click="selectAll()"
                                    class="px-4 py-2 bg-sky-900 hover:bg-sky-950 text-white text-sm font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                                    <i class="fa-regular fa-square-check mr-1" aria-hidden="true"></i>{{ __('dashboard/index.select_all') }}
                                </button>

                                <button type="button" @click="showDeleteSelectedModal = true" :disabled="selectedCount === 0"
                                    class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-red-700 disabled:hover:outline-1">
                                    <i class="fa-solid fa-trash mr-1" aria-hidden="true"></i>{{ __('dashboard/index.delete_selected') }}
                                </button>

                                <!-- Asks before deleting the ticked messages; teleported above the list -->
                                <template x-teleport="body">
                                    <div x-show="showDeleteSelectedModal" x-cloak @keydown.escape.window="showDeleteSelectedModal = false"
                                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity text-white"
                                        x-transition>

                                        <div @click.away="showDeleteSelectedModal = false" role="dialog" aria-modal="true"
                                            class="bg-slate-800 rounded-sm max-w-md w-full p-6 shadow-xl border border-white text-center whitespace-normal">

                                            <h3 class="text-lg font-bold mb-2">{{ __('dashboard/index.delete_selected_confirm') }}</h3>
                                            <p class="mb-4 text-sm text-slate-300"
                                                x-text="@js(__('dashboard/index.delete_selected_count')).replace(':count', selectedCount)"></p>

                                            <div class="flex justify-center space-x-3">
                                                <button type="button" @click="showDeleteSelectedModal = false"
                                                    class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors cursor-pointer">
                                                    {{ __('dashboard/index.no') }}
                                                </button>
                                                <!-- Outside the form once teleported, so it names the form it submits -->
                                                <button type="submit" form="delete_selected_form"
                                                    class="px-4 py-2 border outline-white bg-red-700 hover:bg-red-800 text-white font-medium rounded-sm transition-colors cursor-pointer">
                                                    {{ __('dashboard/index.yes') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </form>
                        @endif
                    </div>

                    <!-- Display Success Notifications -->
                    @if (session('status'))
                        <div class="flex items-center justify-left ml-4 mb-4">
                            <div class="w-fit p-2 border rounded-sm border-emerald-600 text-emerald-400 text-sm">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif

                        <!-- Inquiries list: one card per message, newest first, 5 per page; each card shows its own status -->
                        <section id="inquiries" class="px-4 pb-4 scroll-mt-24" @change="countSelected()">

                            <div class="space-y-3">
                                @forelse ($inquiries as $inquiry)
                                    <x-inquiry-items::inquiry-item :inquiry="$inquiry" :striped="$loop->even" />
                                @empty
                                    <p class="py-4 text-center text-slate-400">{{ __('dashboard/index.inquiries_none') }}</p>
                                @endforelse
                            </div>

                            @if ($inquiries->hasPages())
                                <div class="mt-4">
                                    {{ $inquiries->links('pagination.dashboard') }}
                                </div>
                            @endif
                        </section>

                </div>

            @endif
            <!-- END UI Segment: Contact Submissions -->

        </main>

        <x-inquiry-items::details-modal />

    </div>

</x-layout>
