@use('App\Enums\Role')

<x-layout class="bg-slate-900" textColor="text-white">

     <x-slot name="title">{{ __('header.name') }} – {{ __('about/index.title')}}</x-slot>

    <x-slot name="hero">
        <x-page-banner
        :title="__('about/index.title')"
        desktop='storage/images/events/2026-05_NAYAC_group-desktop.jpg'
        mobile='storage/images/events/2026-05_NAYAC_group-mobile.jpg' />
    </x-slot>

    @auth

        @if (auth()->user()->role !== Role::GUEST)

            <x-blurb title="{{__('about/index.history.title')}}" :variant="['slate-900', '#1e3a8a']"></x-blurb>

            <x-text-image :toggleLeft="true"
            img="./images/history/lee-barry.jpg"
            alt="{{__('about/index.alt_1')}}"
            imageSize="90">{{__('about/index.history.content_1')}}</x-text-image>

            <x-text-image :toggleLeft="false"
            img="./images/history/bible-reading-class-ubf-korea.jpg"
            alt="{{__('about/index.alt_2')}}"
            imageSize="90">{{__('about/index.history.content_2')}}</x-text-image>

            <x-text-image :toggleLeft="true"
            img="./images/history/first-cis-conference.jpg"
            alt="{{__('about/index.alt_3')}}"
            imageSize="90">{{__('about/index.history.content_3')}}</x-text-image>

            <x-text-image :toggleLeft="false"
            img="./images/history/cdn-campus-mission-1982.jpeg"
            alt="{{__('about/index.alt_4')}}"
            imageSize="90">{{__('about/index.history.content_4')}}</x-text-image>

            <x-text-image :toggleLeft="true"
            img="./images/history/20240825-presentation.jpg"
            alt="{{__('about/index.alt_5')}}"
            imageSize="90">{{__('about/index.history.content_5')}}</x-text-image>

            <br/>

        @endif

    @endauth

    <x-blurb title="{{__('about/index.mission_vision.title')}}" :variant="['#1e3a8a', 'slate-900']">
        <p>{{__('about/index.mission_vision.mv_blurb_1')}}</p>

        <p class="pb-8">{{__('about/index.mission_vision.mv_blurb_2')}} <a href='https://ubf.org/about/origin' class='underline' target='_blank'>ubf.org</a>{{__('about/index.mission_vision.mv_blurb_3')}}</p>
    </x-blurb>

    <br/><br/>

    <x-blurb title="{{__('about/index.statement_faith.title')}}" :variant="['slate-900', '#1e3a8a']"></x-blurb>

    <div class="container flex flex-col md:px-24 pb-8">

        <h3 class='justify-left text-left font-bold text-xl md:text-2xl py-4'>{{__('about/index.statement_faith.sf_blurb_1')}}</h3>

        <ul class='list-none space-y-4'>
            <li class="flex items-start gap-3"><i class="fa-solid fa-cross mt-1 shrink-0" aria-hidden="true"></i><span>{{__('about/index.statement_faith.sf_statement_1')}}</span></li>

            <li class="flex items-start gap-3"><i class="fa-solid fa-cross mt-1 shrink-0" aria-hidden="true"></i><span>{{__('about/index.statement_faith.sf_statement_2')}}</span></li>

            <li class="flex items-start gap-3"><i class="fa-solid fa-cross mt-1 shrink-0" aria-hidden="true"></i><span>{{__('about/index.statement_faith.sf_statement_3')}}</span></li>

            <li class="flex items-start gap-3"><i class="fa-solid fa-cross mt-1 shrink-0" aria-hidden="true"></i><span>{{__('about/index.statement_faith.sf_statement_4')}}</span></li>

            <li class="flex items-start gap-3"><i class="fa-solid fa-cross mt-1 shrink-0" aria-hidden="true"></i><span>{{__('about/index.statement_faith.sf_statement_5')}}</span></li>

            <li class="flex items-start gap-3"><i class="fa-solid fa-cross mt-1 shrink-0" aria-hidden="true"></i><span>{{__('about/index.statement_faith.sf_statement_6')}}</span></li>

            <li class="flex items-start gap-3"><i class="fa-solid fa-cross mt-1 shrink-0" aria-hidden="true"></i><span>{{__('about/index.statement_faith.sf_statement_7')}}</span></li>

            <li class="flex items-start gap-3"><i class="fa-solid fa-cross mt-1 shrink-0" aria-hidden="true"></i><span>{{__('about/index.statement_faith.sf_statement_8')}}</span></li>

            <li class="flex items-start gap-3"><i class="fa-solid fa-cross mt-1 shrink-0" aria-hidden="true"></i><span>{{__('about/index.statement_faith.sf_statement_9')}}</span></li>

            <li class="flex items-start gap-3"><i class="fa-solid fa-cross mt-1 shrink-0" aria-hidden="true"></i><span>{{__('about/index.statement_faith.sf_statement_10')}}</span></li>

            <li class="flex items-start gap-3"><i class="fa-solid fa-cross mt-1 shrink-0" aria-hidden="true"></i><span>{{__('about/index.statement_faith.sf_statement_11')}}</span></li>

            <li class="flex items-start gap-3"><i class="fa-solid fa-cross mt-1 shrink-0" aria-hidden="true"></i><span>{{__('about/index.statement_faith.sf_statement_12')}}</span></li>

        </ul>

    </div>

</x-layout>
