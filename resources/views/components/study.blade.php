@props([
    'image' => './images/montreal_skyline-mobile.jpg',
    'heading' => '',
    'book' => '',
    'dateStamp' => '',
    'biblePassage' => '',
    'bibleLink' => '',
    'pdfURL' => '',
    'docURL' => '',
    'href' => null,
])

<!-- Bible Study Section -->
<section {{ $attributes->merge(['class' => 'relative bg-cover bg-center bg-no-repeat h-80']) }}
    style="background-image: url('{{ asset($image) }}')">

    <div class="overlay bg-slate-900/60"></div>

    <div class="flex flex-col mx-auto text-center z-10">

        <!-- Top Left Badge -->
        <div class="absolute top-6 left-0 bg-black/60 backdrop-blur-xs text-amber-50 px-8 py-2.5 rounded-r-full font-sans text-sm md:text-base tracking-wide shadow-md z-5">
            {{$heading}}
        </div>

        <!-- Main Content Cluster -->
        <div class="flex-col text-center translate-y-18 z-10">
            
            <!-- Main Title -->
            <h2 class="text-xl md:text-6xl font-serif italic font-normal tracking-wide drop-shadow-[0_1px_4px_rgba(0,0,0,0.9)] text-white">
                {{$book}}
            </h2>

            <!-- Date Stamp - {{$dateStamp}} -->
            <p class="font-sans text-xs md:text-sm tracking-wider text-white mt-4">
                {{$dateStamp}}
            </p>

            <!-- Sermon Title -->
            <h3 class="font-serif text-base sm:text-xl md:text-2xl lg:text-3xl font-bold italic drop-shadow-[0_2px_4px_rgba(0,0,0,0.3)] text-white mb-1">
            {{$slot}}
            </h3>

            <!-- Scripture Reference -->
            <p class="font-serif text-xs sm:text-sm md:text-base italic text-amber-100/90 mb-3">
                <a href="{{ $bibleLink ?? '#' }}" target="_blank"><span class="hover:text-white hover:underline transition-all duration-200 drop-shadow-xs">{{$biblePassage}}</span></a>
            </p>

            <!-- Download Links -->
            <div class="flex flex-col gap-2 font-sans text-sm md:text-base text-white/90">

                <a href="{{$pdfURL}}" target="_blank">
                    <i class="fa-solid fa-file-pdf text-red-300"></i>
                    <span class="hover:text-white hover:underline transition-all duration-200 drop-shadow-xs">{{__('home/study.questionSheet')}} (.pdf)</span>
                </a>

                <a href="{{ Storage::disk('public')->url($docURL) }}" target="_blank">
                    <i class="fa-solid fa-file-word text-blue-300"></i>
                    <span class="hover:text-white hover:underline transition-all duration-200 drop-shadow-xs">{{__('home/study.questionSheet')}} (.docx)</span>
                </a>

            </div>
            
        </div>
    
    </div>

</section>
