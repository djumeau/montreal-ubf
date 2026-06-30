<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{__('about/index.title')}}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8'>{{__('about/index.title')}}</h1>

    <x-blurb title="{{__('about/index.history.title')}}" :variant="['slate-900', '#1e3a8a']"></x-blurb>

    <x-text-image :toggleLeft="true" 
    img="./images/history/lee-barry.jpg"
    alt="{{__('about/index.alt_1')}}">{{__('about/index.history.content_1')}}</x-text-image>

    <x-text-image :toggleLeft="false" 
    img="./images/history/bible-reading-class-ubf-korea.jpg"
    alt="{{__('about/index.alt_2')}}">{{__('about/index.history.content_2')}}</x-text-image>

    <x-text-image :toggleLeft="true" 
    img="./images/history/first-cis-conference.jpg"
    alt="{{__('about/index.alt_3')}}">{{__('about/index.history.content_3')}}</x-text-image>


    <x-text-image :toggleLeft="false" 
    img="./images/history/cdn-campus-mission-1982.jpeg"
    alt="{{__('about/index.alt_4')}}">{{__('about/index.history.content_4')}}</x-text-image>

    <x-text-image :toggleLeft="true" 
    img="./images/history/20240825-presentation.jpg"
    alt="{{__('about/index.alt_5')}}">{{__('about/index.history.content_5')}}</x-text-image>

    <br/>

    <x-blurb title="{{__('about/index.mission_vision.title')}}" :variant="['#1e3a8a', 'slate-900']">
        <p>{{__('about/index.mission_vision.mv_blurb_1')}}</p>
        
        <p>{{__('about/index.mission_vision.mv_blurb_2')}} <a href='https://ubf.org/about/origin' class='underline' target='_blank'>ubf.org</a>{{__('about/index.mission_vision.mv_blurb_3')}}</p>
    </x-blurb>

    <br/><br/>

    <x-blurb title="{{__('about/index.statement_faith.title')}}" :variant="['slate-900', '#1e3a8a']"></x-blurb>

    <div class="container flex flex-col md:px-24">

        <h3 class='justify-left text-left font-bold text-xl md:text-2xl py-4'>{{__('about/index.statement_faith.sf_blurb_1')}}</h3>

        <ul class='list-disc list-outside space-y-4 pl-4'>
            <li>{{__('about/index.statement_faith.sf_statement_1')}}</li>

            <li>{{__('about/index.statement_faith.sf_statement_2')}}</li>

            <li>{{__('about/index.statement_faith.sf_statement_3')}}</li>

            <li>{{__('about/index.statement_faith.sf_statement_4')}}</li>

            <li>{{__('about/index.statement_faith.sf_statement_5')}}</li>

            <li>{{__('about/index.statement_faith.sf_statement_6')}}</li>

            <li>{{__('about/index.statement_faith.sf_statement_7')}}</li>

            <li>{{__('about/index.statement_faith.sf_statement_8')}}</li>

            <li>{{__('about/index.statement_faith.sf_statement_9')}}</li>

            <li>{{__('about/index.statement_faith.sf_statement_10')}}</li>

            <li>{{__('about/index.statement_faith.sf_statement_11')}}</li>

            <li>{{__('about/index.statement_faith.sf_statement_12')}}</li>

        </ul>

    </div>

</x-layout>
