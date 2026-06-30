<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{__('about/index.title')}}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8'>{{__('about/index.title')}}</h1>

    <x-blurb title="{{__('about/index.history')}}" :variant="['slate-900', '#1e3a8a']"></x-blurb>

    <x-text-image :toggleLeft="true" 
    img="./images/history/lee-barry.jpg"
    alt="{{__('about/index.alt_1')}}">{{__('about/index.content_1')}}</x-text-image>

    <x-text-image :toggleLeft="false" 
    img="./images/history/bible-reading-class-ubf-korea.jpg"
    alt="{{__('about/index.alt_2')}}">{{__('about/index.content_2')}}</x-text-image>

    <x-text-image :toggleLeft="true" 
    img="./images/history/first-cis-conference.jpg"
    alt="{{__('about/index.alt_3')}}">{{__('about/index.content_3')}}</x-text-image>


    <x-text-image :toggleLeft="false" 
    img="./images/history/cdn-campus-mission-1982.jpeg"
    alt="Cercle de prière">Le ministère canadien a vu le jour en 1982 à Winnipeg, au Manitoba, et s&apos;est répandu à plusieurs villes à travers le pays.</x-text-image>

    <x-text-image :toggleLeft="true" 
    img="./images/history/20240825-presentation.jpg"
    alt="Présentation à Montreal">Le ministère de Montréal était établit en 1991. L&apos;église est composée de professionnels, de familles et de jeunes adultes.</x-text-image>

    <br/>

    <x-blurb title='Mission et vision' :variant="['slate-900', '#1e3a8a']">
        <p>Notre mission principale est d&apos;apporter l&apos;Évangile aux étudiants des campus car l&apos;Évanigile offre le salut à tous le monde pour ceux qui croit (Ro 1.16). Également, nous croyons que nos étudiants sont nos futurs leaders. Une fois qu&apos;ils auront mis leur foi en Jésus-Christ comme Seigneur et Sauveur, ils pourront exercer une influence dans tous les aspects de la vie et transmettre la bonne nouvelle de l&apos;Évangile à leurs pairs, à la nation et au monde entier.</p>
        
        <p>Pour plus d&apos;information sur le ministère, visitez <a href='https://ubf.org/about/origin' class='underline' target='_blank'>ubf.org</a>. (En anglais.)</p>
    </x-blurb>

    <br/><br/>

    <x-blurb title='Déclaration de foi' :variant="['slate-900', '#1e3a8a']"></x-blurb>

    <br/>

    <h3 class='flex justify-left text-left font-bold text-xl'>Nous croyons :</h3>

    <ul class='list-disc list-outside p-5 space-y-4'>
        <li>Qu&apos;il existe un seul Dieu en trois Personnes : Dieu le Père, Dieu le Fils et Dieu le Saint-Esprit.</li>

        <li>Dieu a créé les cieux, la terre et toutes les autres choses de l&apos;univers; qu&apos;Il est le Souverain de toutes choses; que ce Dieu souverain se révèle; nous croyons en son œuvre rédemptrice et en son dernier jugement.</li>

        <li>La Bible est inspirée par Dieu; elle est la vérité; elle est l&apos;autorité suprême en matière de foi et de pratique.</li>

        <li>Depuis la chute d&apos;Adam, toute l'humanité est sous l’esclavage et la puissance du péché et méritent le jugement et la colère de Dieu.</li>

        <li>Jésus-Christ, qui est à la fois Dieu et homme, par sa mort expiatoire et sacrificielle sur la croix pour nos péchés et par sa résurrection, est le seul chemin du salut; (Jn 14.6) lui seul nous sauve du péché et du jugement et nous purifie de la souillure du monde causée par le péché.</li>

        <li>Jésus-Christ est ressuscité des morts, est monté au ciel et se siège à la droite de Dieu le Père.</li>

        <li>La régénération est l&apos;œuvre du Saint-Esprit, et elle est nécessaire pour entrer dans le royaume de Dieu. Nous croyons que Dieu a envoyé son Saint-Esprit pour donner à son Église la puissance de témoigner de Jésus jusqu&apos;aux extrémités de la terre.</li>

        <li>Nous sommes justifiés par la grâce seule, par la foi seule.</li>

        <li>Le Saint-Esprit agit dans le cœur de chaque croyant pour le guider.</li>

        <li>L&apos;Église universelle est le corps du Christ et tous les chrétiens en sont membres.</li>

        <li>Jésus reviendra dans la gloire pour juger les vivants et les morts.</li>

    </ul>

</x-layout>
