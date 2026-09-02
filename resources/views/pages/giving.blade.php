@php

    $filename = __('giving.filename');
    // $videoDirPath = asset('storage/videos/' . {{ __('giving.filename') }});

    $filePath = Storage::url('videos/' . __('giving.filename'));

    // dd($filePath);

    // dd(Storage::disk('public')->exists('videos\donation_via_zeffy_en.mp4'));

    //dd(Storage::disk('public')->exists($videoDirPath));

    // dd($videoDirPath);

@endphp

<x-layout x-data="{ isOpen: false,
                    videoSrc: '{{ $filePath }}' }"
        class="giving bg-slate-900"
        textColor="text-white">

    <!-- Video source: {{ $filePath }} -->

    <x-slot name="title">{{ __('giving.title') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pt-18 pb-8'>{{ __('giving.title') }}</h1>

    <x-blurb title="{{ __('giving.scripture') }}" :variant="['slate-900', '#1e3a8a']"></x-blurb>

    <x-text-image img="./images/other/hands_glowing_cross.jpg" alt="{{ __('giving.title') }}"
       imageSize="90">{{ __('giving.introduction') }}</x-text-image>

    <!-- Responsive Grid Wrapper -->
    <div class='flex flex-col md:flex-row md:items-start flex-wrap justify-center items-center gap-4 pb-6'>

        <x-card image='./images/ministry/sws_20260315.jpg' title="{{__('giving.inperson.title')}}"
            alt='Montréal 2019' class="w-full max-w-sml md:w-[40%] md:max-w-none">{{__('giving.inperson.instructions')}}
        </x-card>

        <x-card image='./images/giving_debit_card.jpg' title="{{__('giving.online.title')}}" alt="{{__('giving.online.title')}}"
            class="w-full max-w-sml md:w-[40%] md:max-w-none">{{__('giving.online.instructions')}}

            <div class="flex align-center justify-center mb-4">
                <a class="hover:none active:none" href="https://www.zeffy.com/en-CA/donation-form/world-mission-bible-canada" target="_blank"><img  src="./images/offering_qr_code.png" /></a>
            </div>

            <div class="flex flex-col bg-slate-900 rounded-lg mb-2 p-4 text-white">

                <div class="w-[95%] flex flex-start gap-2 mb-2">
                    <i class="fa-solid fa-circle-info fa-2x"></i> <p>{{ __('giving.online.note') }}</p>
                </div>

                <!-- Button to open modal box. -->
                <div class="w-full">

                    <!-- Trigger Button -->
                    <button @click="isOpen = true" class="w-full px-4 py-2 bg-indigo-500 hover:bg-indigo-900 text-white rounded-md align-center" style="cursor: pointer;">
                        <i class="fa-solid fa-video"></i> {{ __('giving.video') }}
                    </button>

                </div>

            </div>

        </x-card>

    </div>

    <!-- Attention -->
    <div class="mx-auto flex flex-start gap-2 bg-red-900 rounded-lg p-4 pb-6 text-white md:max-w-[85%]">
                    <i class="fa-solid fa-file fa-2x"></i> <p>{{ __('giving.charity') }}</p>
    </div>

    <!-- Modal -->
    <div
        x-cloak
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="isOpen = false"
        class="overlay p-4 bg-black/70 backdrop-blur-sm">

        <div @click.away="isOpen = false"
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="scale-95 translate-y-4"
            x-transition:enter-end="scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="scale-100 translate-y-0"
            x-transition:leave-end="scale-95 translate-y-4"
            class="modal flex flex-col"
            x-cloak>
            <span class="text-sm font-semibold text-slate-900 mb-2">{{ __('giving.instructions') }}</span>

            <!-- Video Wrapper -->
            <div class="w-full bg-black aspect-350/775 max-h-[80vh] mx-auto overflow-hidden">
                <!--
                Binding the 'src' attribute ensures the iframe unloads/stops playing
                instantly when 'isOpen' becomes false.
                -->
                <template x-if="isOpen">
                   <video
                    class="w-full h-full object-cover hide-video-audio outline-2 outline-black"
                    controls
                    autoplay
                    muted
                    playsinline
                    >
                    <source src="{{ asset('storage/videos/donation_via_zeffy_en.mp4') }}" type="video/mp4">
                    Your browser does not support the video tag.
                    </video>
                </template>
            </div>

            <button type="button" @click="isOpen = false" class="bg-indigo-500 hover:bg-indigo-900 px-4 py-2 outline-1 outline-slate-900">{{ __('giving.close') }}</button>
        </div>

    </div> <!-- End Modal -->

</x-layout>
