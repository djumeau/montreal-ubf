<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('giving.title') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8'>{{ __('giving.title') }}</h1>

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

            <div class="flex align-center justify-center">
                <a class="hover:none active:none" href="https://www.zeffy.com/en-CA/donation-form/world-mission-bible-canada" target="_blank"><img  src="./images/offering_qr_code.png" /></a>
            </div>

            <div class="flex flex-start gap-2 bg-slate-900 rounded-lg p-4 text-white">
                <i class="fa-solid fa-circle-info fa-2x"></i> <p>{{ __('giving.online.note') }}</p>
            </div>
        </x-card>

    </div>

    <div class="mx-auto flex flex-start gap-2 bg-red-900 rounded-lg p-4 pb-6 text-white md:max-w-[85%]">
                    <i class="fa-solid fa-file fa-2x"></i> <p>{{ __('giving.charity') }}</p>
            </div>

</x-layout>
