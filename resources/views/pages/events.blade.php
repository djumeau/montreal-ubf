<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('events/index.title') }}</x-slot>

    <!-- Hero Banner: full width from the top of the page, behind the fixed header (pt-16 keeps the text below it).
         Mobile image below md, desktop image from md up -->
    <x-slot name="hero">
        <section class="relative h-72 md:h-96 pt-24 flex items-center justify-center overflow-hidden">

            <div class="absolute inset-0 bg-cover bg-center md:hidden"
                style="background-image: url('{{ asset('storage/images/events/events-mobile.jpg') }}')"></div>
            <div class="absolute inset-0 bg-cover bg-center hidden md:block"
                style="background-image: url('{{ asset('storage/images/events/events-desktop.jpg') }}')"></div>

            <div class="absolute inset-0 bg-slate-900/60"></div>

            <div class="relative z-10 text-center px-4">
                <h1 class="text-3xl md:text-5xl font-bold drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                    {{ __('events/index.title') }}
                </h1>
                <p class="mt-2 text-lg md:text-xl text-slate-100 drop-shadow-[0_1px_3px_rgba(0,0,0,0.8)]">
                    {{ __('events/index.subtitle') }}
                </p>
                <div class="mx-auto mt-4 h-0.5 w-14 bg-white/80"></div>
            </div>
        </section>
    </x-slot>

    <x-blurb title="{{__('events/index.upcoming')}}" :variant="['slate-900', '#1e3a8a']"></x-blurb>

    <br/>

    <x-blurb title="{{__('events/index.past')}}" :variant="['slate-900', '#1e3a8a']"></x-blurb>

    <br/>

    <div class="block md:hidden">
        <x-events href='https://franco2026.university-bible-fellowship.ca' image='./images/2026_conf/2026-conf_franco_titre-mobile.jpg' title="" dates="{{__('events/index.dates')}}" location="{{__('events/index.location')}}">{{__('events/index.content')}}</x-events>
    </div>

    <div class="hidden md:block">
        <x-events href='https://franco2026.university-bible-fellowship.ca' image='./images/2026_conf/2026-conf_franco_titre-desktop.jpg' title="" dates="{{__('events/index.dates')}}" location="{{__('events/index.location')}}">{{__('events/index.content')}}</x-events>
    </div>

    <br/>

</x-layout>
