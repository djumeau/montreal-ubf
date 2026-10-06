{{-- Inquiry Details Modal (Manage Inquiries): opened by clicking an inquiry item, filled from the page's Alpine data:
     showInquiryModal, and inquiry = { subject, name, email, signedIn, sentAt, message, status, readAt, answeredAt, answeredBy, note }
     (see manage-inquiries); closeInquiry() closes it. Opening a new message marks it read (see the page);
     the Answer section records who answered (the signed-in Elder or Administrator, filled in) and the note --}}

<template x-teleport="body">
    <div x-show="showInquiryModal" x-cloak @keydown.escape.window="showInquiryModal && closeInquiry()" x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs text-white">

        <div @click.outside="closeInquiry()" role="dialog" aria-modal="true" aria-labelledby="inquiry_modal_title"
            class="flex flex-col max-w-2xl w-full max-h-[90vh] bg-slate-900 rounded-lg shadow-xl border border-white">

            <template x-if="inquiry">
                <div class="flex flex-col min-h-0">

                    <!-- Title (the subject), status badge and Close -->
                    <div class="flex items-center justify-between gap-3 px-5 py-3 bg-slate-800 rounded-t-lg">
                        <div class="flex flex-wrap items-center gap-3 min-w-0">
                            <h3 id="inquiry_modal_title" class="text-lg font-bold" x-text="inquiry.subject"></h3>

                            <!-- Same badges as the items (inquiry-items/notification) -->
                            <span x-show="inquiry.status === 'answered'" class="px-2 py-0.5 text-xs rounded-full border bg-emerald-800 outline-white text-white">
                                <i class="fa-solid fa-check mr-1" aria-hidden="true"></i>{{ __('dashboard/manage-inquiries/index.inquiry_answered') }}
                            </span>
                            <span x-show="inquiry.status === 'read'" class="px-2 py-0.5 text-xs rounded-full border bg-slate-600 outline-white text-white">
                                <i class="fa-solid fa-eye mr-1" aria-hidden="true"></i>{{ __('dashboard/manage-inquiries/index.inquiry_read') }}
                            </span>
                            <span x-show="inquiry.status === 'new'" class="px-2 py-0.5 text-xs border outline-white rounded-full bg-sky-500 text-white font-medium">
                                <i class="fa-solid fa-bell mr-1" aria-hidden="true"></i>{{ __('dashboard/manage-inquiries/index.inquiry_new') }}
                            </span>
                        </div>

                        <button type="button" @click="closeInquiry()" aria-label="{{ __('dashboard/index.close') }}"
                            class="grid place-items-center size-8 shrink-0 text-slate-300 hover:text-white cursor-pointer">
                            <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                        </button>
                    </div>

                    <!-- Body (scrolls when the message is long): sender and date, the message, then the follow-up -->
                    <div class="p-5 overflow-y-auto space-y-4 text-sm">

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-slate-300">
                            <span><i class="fa-solid fa-user mr-1" aria-hidden="true"></i><span x-text="inquiry.name"></span></span>
                            <a :href="'mailto:' + inquiry.email" class="text-sky-400 hover:text-sky-300 hover:underline">
                                <i class="fa-solid fa-envelope mr-1" aria-hidden="true"></i><span x-text="inquiry.email"></span>
                            </a>
                            <span x-show="inquiry.signedIn" class="text-xs italic">{{ __('dashboard/manage-inquiries/index.inquiry_signed_in') }}</span>
                            <span><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i><span x-text="inquiry.sentAt"></span></span>
                        </div>

                        <!-- The message, line breaks kept -->
                        <p class="whitespace-pre-line text-base text-slate-100 p-3 bg-slate-800 border border-slate-700 rounded-sm" x-text="inquiry.message"></p>

                        <!-- When it was read, and who answered and when (only the parts there are) -->
                        <div x-show="inquiry.readAt || inquiry.answeredAt" class="text-slate-300 space-y-1">
                            <p x-show="inquiry.readAt">
                                <i class="fa-solid fa-eye mr-1" aria-hidden="true"></i>
                                <span x-text="@js(__('dashboard/manage-inquiries/index.inquiry_read_on')).replace(':date', inquiry.readAt)"></span>
                            </p>
                            <p x-show="inquiry.answeredAt">
                                <i class="fa-solid fa-reply mr-1" aria-hidden="true"></i>
                                <span x-text="@js(__('dashboard/manage-inquiries/index.inquiry_answered_on')).replace(':date', inquiry.answeredAt)"></span>
                                <span x-show="inquiry.answeredBy" x-text="@js(__('dashboard/manage-inquiries/index.inquiry_answered_by')).replace(':name', inquiry.answeredBy)"></span>
                            </p>
                        </div>

                        <!-- Answer: who answered (already answered: that person; otherwise the signed-in Elder or Administrator,
                             saved as the one answering) and the note. Saving the first time marks the message answered -->
                        <form id="inquiry_answer_form" :action="inquiry.answerUrl" method="POST"
                            class="pt-4 border-t border-slate-700 space-y-3">
                            @csrf
                            @method('PUT')

                            <h4 class="border-l-4 border-blue-600 pl-3 text-base font-bold text-slate-100">{{ __('dashboard/manage-inquiries/index.answer_section') }}</h4>

                            <div class="grid grid-cols-1 sm:grid-cols-[8rem_1fr] gap-x-4 gap-y-1 items-center">
                                <label for="inquiry_answered_by" class="font-medium">{{ __('dashboard/manage-inquiries/index.answered_by_label') }}</label>
                                <!-- Shown only: the server records the signed-in user, never a typed name -->
                                <input id="inquiry_answered_by" type="text" readonly
                                    :value="inquiry.answeredBy ?? @js(auth()->user()->name)"
                                    class="w-full px-3 py-2 bg-slate-800 border border-slate-600 rounded-sm text-slate-300 cursor-default focus:outline-none">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-[8rem_1fr] gap-x-4 gap-y-1">
                                <label for="inquiry_note" class="font-medium sm:pt-2">{{ __('dashboard/manage-inquiries/index.note_label') }}</label>
                                <textarea id="inquiry_note" name="note" rows="4" maxlength="5000" x-model="inquiry.note"
                                    placeholder="{{ __('dashboard/manage-inquiries/index.note_placeholder') }}"
                                    class="w-full px-3 py-2 bg-slate-900 border border-slate-500 rounded-sm text-sm focus:outline-none focus:border-white"></textarea>
                            </div>
                        </form>
                    </div>

                    <!-- Close, and Mark as answered (Save once it is answered) -->
                    <div class="flex justify-end gap-3 px-5 py-3 border-t border-slate-700">
                        <button type="button" @click="closeInquiry()"
                            class="px-6 py-2 bg-sky-900/50 hover:bg-sky-950/50 text-white font-medium border border-white rounded-sm hover:outline-2 outline-white cursor-pointer">
                            {{ __('dashboard/index.close') }}
                        </button>
                        <button type="submit" form="inquiry_answer_form"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium border border-white rounded-sm hover:outline-2 outline-white cursor-pointer">
                            <i class="fa-solid fa-reply mr-1" aria-hidden="true"></i>
                            <span x-text="inquiry.answeredAt ? @js(__('dashboard/index.save')) : @js(__('dashboard/manage-inquiries/index.mark_answered'))"></span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
