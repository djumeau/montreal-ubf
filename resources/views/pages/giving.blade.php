<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('giving.title') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8'>{{ __('giving.title') }}</h1>

    <x-blurb title="{{ __('giving.scripture') }}" :variant="['slate-900', '#1e3a8a']"></x-blurb>

    <x-text-image :toggleLeft="true" img="./images/offering_qr_code.png" alt="{{ __('about/index.alt_5') }}"
        imageSize="66">{{ __('giving.instructions') }}</x-text-image>

</x-layout>
