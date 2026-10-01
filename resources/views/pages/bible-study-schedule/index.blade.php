<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('bible-study-schedule/index.title') }}</x-slot>

    <x-slot name="hero">
        <x-page-banner :title="__('bible-study-schedule/index.title')" desktop='storage/images/events/bible_study_schedule-desktop.jpg'
            mobile='storage/images/events/bible_study_schedule-mobile.jpg' />
    </x-slot>

    <x-study-schedule::filter-panel :view="$view" :start="$start" :previous="$previous" :next="$next" :range-label="$rangeLabel" />

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

</x-layout>
