@php
    $locale = session('locale', app()->getLocale());
    $locale = substr($locale, 0, 2);
@endphp

<x-layout class="bg-slate-900" textColor="text-white" x-data="currentTab: 'en' }">

    <x-slot name="title">{{ __('header.name') }} - {{ __('bible-study/index.create') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8'>{{ __('bible-study/index.create') }}</h1>

    <div class="max-w-4xl mx-auto p-8 bg-white rounded-2xl border border-slate-100 shadow-xs">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-5 mb-6">

            <!-- Language Selection Toggle Component Tabs -->
            <div class="flex bg-slate-100 p-1 rounded-xl mt-4 sm:mt-0 self-start">
                <button type="button" @click="currentTab = 'en'" :class="currentTab === 'en' ? 'bg-white shadow-xs text-indigo-600 font-semibold' : 'text-slate-600 hover:text-slate-900'" class="px-4 py-1.5 rounded-lg text-sm transition cursor-pointer">
                    EN
                </button>
                <button type="button" @click="currentTab = 'fr'" :class="currentTab === 'fr' ? 'bg-white shadow-xs text-indigo-600 font-semibold' : 'text-slate-600 hover:text-slate-900'" class="px-4 py-1.5 rounded-lg text-sm transition cursor-pointer">
                    FR
                </button>
            </div>
        </div>

        {{-- @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif --}}

        <form action="{{ route('bible-studies.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- GLOBAL FIELD REGION (Non-Translatable Meta IDs) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50 p-5 rounded-xl border border-slate-100 mb-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Study Series ID</label>
                    <input type="string" name="study_series_id" value="{{ old('study_series_id') }}"
                           class="w-full px-3 py-2 bg-white border @error('study_series_id') border-red-500 @else border-slate-300 @enderror rounded-lg focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                    @error('study_series_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Book ID</label>
                    <input type="number" name="book_id" value="{{ old('book_id') }}"
                           class="w-full px-3 py-2 bg-white border @error('book_id') border-red-500 @else border-slate-300 @enderror rounded-lg focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                    @error('book_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror°
                </div>
            </div>

            <!-- DYNAMIC LOCALIZED CONTENT REGIONS -->
            @foreach(['en' => 'English Content Panel', 'fr' => 'Panneau de contenu Français'] as $lang => $label)
            <div x-show="currentTab === '{{ $lang }}'" class="space-y-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-98" x-transition:enter-end="opacity-100 transform scale-100">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Lesson Title ({{ strtoupper($lang) }})</label>
                        <input type="text" name="title[{{ $lang }}]" value="{{ old("title.$lang") }}" placeholder="{{ $lang === 'en' ? 'e.g., Jesus is God' : 'ex., Jésus est Dieu' }}"
                               class="w-full px-3 py-2 bg-white border @error("title.$lang") border-red-500 @else border-slate-300 @enderror rounded-lg focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                        @error("title.$lang") <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Bible Passage Reference -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Bible Passage ({{ strtoupper($lang) }})</label>
                        <input type="text" name="bible_passage[{{ $lang }}]" value="{{ old("bible_passage.$lang") }}" placeholder="{{ $lang === 'en' ? 'e.g., 1:1-4' : 'ex., 12.1-4' }}"
                               class="w-full px-3 py-2 bg-white border @error("bible_passage.$lang") border-red-500 @else border-slate-300 @enderror rounded-lg focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                        @error("bible_passage.$lang") <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Image Links -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Image Links ({{ strtoupper($lang) }}) <span class="text-xs text-slate-400 font-normal">(One URL per line)</span></label>
                        <textarea name="image_links[{{ $lang }}]" rows="3" placeholder="https://biblegateway.com"
                                  class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">{{ old("image_links.$lang") }}</textarea>
                    </div>

                    <!-- Passage Links -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Passage Links ({{ strtoupper($lang) }}) <span class="text-xs text-slate-400 font-normal">(One URL per line)</span></label>
                        <textarea name="passage_links[{{ $lang }}]" rows="3" placeholder="https://bible.com"
                                  class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">{{ old("passage_links.$lang") }}</textarea>
                    </div>

                    <!-- Question Sheet Text -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Question Sheet Text / URL ({{ strtoupper($lang) }})</label>
                        <input type="text" name="question_sheet[{{ $lang }}]" value="{{ old("question_sheet.$lang") }}"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                    </div>

                    <!-- Lecture Content Textarea -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5"Lecture ({{ strtoupper($lang) }})</label>
                        <textarea name="lecture[{{ $lang }}]" rows="4" placeholder="..."
                                  class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">{{ old("lecture.$lang") }}</textarea>
                    </div>

                </div>
            </div>
            @endforeach

            <!-- Global Error Banner helper across invisible language forms -->
            @if ($errors->any())
                <div class="p-3 bg-red-50 border border-red-100 rounded-xl">
                    <p class="text-xs text-red-600 font-medium">Please review both English and French fields. Missing translations detected.</p>
                </div>
            @endif

            <!-- Submit Button Trigger -->
            <div class="pt-4 border-t border-slate-100 flex justify-end">
                <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-medium rounded-lg shadow-xs transition text-sm cursor-pointer">
                    Save Localized Record
                </button>
            </div>
        </form>

    </div>


</x-layout>
