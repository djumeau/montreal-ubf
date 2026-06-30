<x-layout class="bg-slate-900">

    <div class='container mx-auto px-4'>

        <!-- First Row: Heading -->
        <div class="flex flex-col md:flex-row justify-center items-center md:items-start pb-4">
            <h2 class="text-xl md:text-3xl font-bold text-white">{{__('home/index.background')}}</h2>
        </div>

        <!-- Responsive Grid Wrapper -->

        <div class='flex flex-col md:flex-row md:items-start flex-wrap justify-center items-center gap-6'>

            <x-card image='./images/ministry/montreal_ministry_20190616-original.jpg' title="{{__('home/card_1.title')}}"
                alt='Montréal 2019' class="w-full max-w-sml md:w-[30%] md:max-w-none">{{__('home/card_1.content')}}
            </x-card>

            <x-card image='./images/other/classroom-circle-card.jpg' title='Ministères' alt="Groupe d'étude"
                class="w-full max-w-sml md:w-[30%] md:max-w-none">
                <p>On offre l'étude biblique pour:
                <ul class='space-y-2'>
                    <li class='flex items-start gap-x-1'>
                        <span>&bull;</span>
                        <span>Les étudiants (CEGEP, écoles professionelles et l'université)</span>
                    </li>
                    <li class='flex items-start gap-x-1.5'>
                        <span>&bull;</span>
                        <span>Les jeunes adultes et professionels</span>
                    </li>
                    <li class='flex items-start gap-x-1.5'>
                        <span>&bull;</span>
                        <span>Les enfants (école primaire)</span>
                    </li>
                    <li class='flex items-start gap-x-1.5'>
                        <span>&bull;</span>
                        <span>Les adolescents (école secondaire)</span>
                    </li>
                </ul>
                </p>
                <br />
                <p>Nos études bibliques est parvenu en groupe et en personne. On préfère de se réunir à notre église
                    mais on offre aussi les études en ligne.</p>
            </x-card>

            <x-card image='./images/other/earth-from-space-card.jpg' title='Pour le monde' alt='Monde'
                class="w-full max-w-sml md:w-[30%] md:max-w-none">
                On participe aussi dans les conférences locales, nationales et internationales. On collabore avec nos
                ministères dans plus de 90 pays.
            </x-card>

        </div>

    </div>

</x-layout>
