<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{__('events/index.title')}}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8'>{{__('events/index.title')}}</h1>

    <x-blurb title="{{__('events/index.upcoming')}}" :variant="['slate-900', '#1e3a8a']"></x-blurb>

    <br/>

    <div class="block md:hidden">
        <x-events href='https://montrealubf.org/franco2026' image='./images/2026_conf/2026-conf_franco_titre-mobile.jpg' title="" dates="{{__('events/index.dates')}}" location="{{__('events/index.location')}}">{{__('events/index.content')}}</x-events>
    </div>

    <div class="hidden md:block">
        <x-events href='https://montrealubf.org/franco2026' image='./images/2026_conf/2026-conf_franco_titre-desktop.jpg' title="" dates="{{__('events/index.dates')}}" location="{{__('events/index.location')}}">{{__('events/index.content')}}</x-events>
    </div>

    <br/>

</x-layout>
