<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('bible-study-schedule/index.title') }}</x-slot>

    <x-slot name="hero">
        <x-page-banner :title="__('bible-study-schedule/index.title')" desktop='storage/images/events/bible_study_schedule-desktop.jpg'
            mobile='storage/images/events/bible_study_schedule-mobile.jpg' />
    </x-slot>

    <!-- Visitors (not signed in): a block opens the "contact us" modal, whose link goes to the contact page for that study -->
    <div x-data="{
        showContactModal: false,
        contactStudy: { url: '', title: '', when: '', where: '', map: '', colourClass: '', colourStyle: '' },
        openContact(study) {
            this.contactStudy = study;
            this.showContactModal = true;
        },
    }">

    <x-study-schedule::filter-panel :view="$view" :start="$start" :previous="$previous" :next="$next" :range-label="$rangeLabel" :type="$type" />

    @if ($view === 'week')
        <!-- Week Grid (desktop): Sunday to Saturday header, then the hour rows -->
        <div class="hidden md:block mt-6 mx-2 md:mx-6 border border-slate-700 rounded-sm overflow-hidden">
            <table class="w-full table-fixed text-sm">
                <x-study-schedule::week-head :start="$start" />
                <x-study-schedule::hour-rows :start="$start" :studies="$studies" />
            </table>
        </div>
    @endif

    <!-- Day List: the List view, and the Week view on phones (where the grid is hidden) -->
    <x-study-schedule::day-list :start="$start" :studies="$studies"
        class="mt-6 mx-2 md:mx-6 {{ $view === 'week' ? 'md:hidden' : '' }}" />

    @guest
        <!-- AlpineJS Modal for visitors: joining a study goes through the contact page -->
        <div x-show="showContactModal" x-cloak @keydown.escape.window="showContactModal = false" x-transition
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity">

            <div @click.away="showContactModal = false" role="dialog" aria-modal="true" aria-labelledby="contact_modal_title"
                class="bg-slate-900 rounded-lg max-w-md w-full shadow-xl border border-slate-500">

                <!-- Title and Close -->
                <div class="flex items-center justify-between px-5 py-3 bg-slate-800 rounded-t-lg">
                    <h3 id="contact_modal_title" class="text-lg font-bold">{{ __('bible-study-schedule/index.contact_modal_title') }}</h3>
                    <button type="button" @click="showContactModal = false" aria-label="{{ __('bible-study-schedule/index.close') }}"
                        class="grid place-items-center size-8 text-slate-300 hover:text-white cursor-pointer">
                        <i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="p-5">
                    <p class="mb-4">{{ __('bible-study-schedule/index.contact_modal_message') }}</p>

                    <!-- The study clicked, on the colours of its block: title, when, and where when it has a location
                         (linked to Google Maps when it is a place) -->
                    <div class="mb-5 p-3 border border-white rounded-md text-sm space-y-1"
                        :class="contactStudy.colourClass" :style="contactStudy.colourStyle">
                        <p class="font-medium text-base" x-text="contactStudy.title"></p>
                        <p class="flex gap-2">
                            <i class="fa-regular fa-clock w-4 mt-0.5 text-center" aria-hidden="true"></i>
                            <span class="sr-only">{{ __('bible-study-schedule/index.when') }}</span>
                            <span x-text="contactStudy.when"></span>
                        </p>
                        <p class="flex gap-2" x-show="contactStudy.where">
                            <i class="fa-solid fa-location-dot w-4 mt-0.5 text-center" aria-hidden="true"></i>
                            <span class="sr-only">{{ __('bible-study-schedule/index.where') }}</span>
                            <!-- A physical location opens Google Maps in a new tab; "Via Zoom" and online studies stay plain text -->
                            <a x-show="contactStudy.map" :href="contactStudy.map" target="_blank" rel="noopener noreferrer"
                                class="underline hover:no-underline">
                                <span x-text="contactStudy.where"></span>
                                <i class="fa-solid fa-arrow-up-right-from-square ml-1 text-xs" aria-hidden="true"></i>
                                <span class="sr-only">{{ __('bible-study-schedule/index.opens_map') }}</span>
                            </a>
                            <span x-show="!contactStudy.map" x-text="contactStudy.where"></span>
                        </p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showContactModal = false"
                            class="px-6 py-2 bg-slate-900 hover:bg-slate-700 text-white font-medium border border-white rounded-sm cursor-pointer">
                            {{ __('bible-study-schedule/index.close') }}
                        </button>

                        <a :href="contactStudy.url"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium border border-white rounded-sm">
                            {{ __('bible-study-schedule/index.contact_modal_link') }}<i class="fa-solid fa-arrow-right ml-2" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endguest

    </div>

</x-layout>
