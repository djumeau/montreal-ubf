<x-layout class="bg-slate-900">

    <div class='container mx-auto px-4'>

        <!-- First Row: Heading -->
        <div class="flex flex-col md:flex-row justify-center items-center md:items-start pb-6">
            <h2 class="text-xl md:text-2xl font-bold text-white">{{__('home/index.background')}}</h2>
        </div>

        <!-- Responsive Grid Wrapper -->

        <div class='flex flex-col md:flex-row md:items-start flex-wrap justify-center items-center gap-6 pb-6'>

            <x-card image='./images/ministry/montreal_ministry_20190616-original.jpg' title="{{__('home/card_1.title')}}"
                alt='Montréal 2019' class="w-full max-w-sml md:w-[30%] md:max-w-none">{{__('home/card_1.content')}}
            </x-card>

            <x-card image='./images/other/classroom-circle-card.jpg' title="{{__('home/card_2.title')}}" alt="{{__('home/card_2.alt')}}"
                class="w-full max-w-sml md:w-[30%] md:max-w-none">
                <p>{{__('home/card_2.blurb_1')}}
                <ul class='space-y-2'>
                    <li class='flex items-start gap-x-1'>
                        <span>&bull;</span>
                        <span>{{__('home/card_2.point_1')}}</span>
                    </li>
                    <li class='flex items-start gap-x-1.5'>
                        <span>&bull;</span>
                        <span>{{__('home/card_2.point_2')}}</span>
                    </li>
                    <li class='flex items-start gap-x-1.5'>
                        <span>&bull;</span>
                        <span>{{__('home/card_2.point_3')}}</span>
                    </li>
                </ul>
                </p>
                <br />
                <p>{{__('home/card_2.blurb_2')}}</p>
            </x-card>

            <x-card image='./images/other/earth-from-space-card.jpg' title="{{__('home/card_3.title')}}" alt="{{__('home/card_3.blurb_1')}}"
                class="w-full max-w-sml md:w-[30%] md:max-w-none">{{__('home/card_3.blurb_1')}}
            </x-card>

        </div>

    </div>

</x-layout>
