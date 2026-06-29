<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">Événements</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8'>Événements</h1>

    <x-blurb title='À venir' :variant="['slate-900', '#1e3a8a']"></x-blurb>

    <br/>

    <div class="block md:hidden">
        <x-events href='https://montrealubf.org/franco2026' image='./images/2026_conf/2026-conf_franco_titre-mobile.jpg'>2026 Conférence biblique francophone d&apos;été</x-events>
    </div>

    <div class="hidden md:block">
        <x-events href='https://montrealubf.org/franco2026' image='./images/2026_conf/2026-conf_franco_titre-desktop.jpg'>2026 Conférence biblique francophone d&apos;été</x-events>
    </div>

</x-layout>
