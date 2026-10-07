@props([
    'heading' => null,
    'studyId' => null, // BibleStudy id; its series, title, book, passage, images and question sheets are shown in the current locale
    'date' => null, // Date the study is held, e.g. "2026-10-04" (any date Carbon can parse, or a Carbon instance)
])

@php
    $study = $studyId ? \App\Models\BibleStudy::with(['series', 'book'])->find($studyId) : null;

    $formattedDate = $date
        ? \Illuminate\Support\Carbon::parse($date)->locale(app()->getLocale())->translatedFormat(__('home/study.dateFormat'))
        : '';

    $questionSheets = $study?->localizedAttachments('question_sheet')->sortBy('filename') ?? collect();
@endphp

@if($study)

<!-- Bible Study Section (mobile image below md:, desktop image from md: up) -->

<section {{ $attributes->merge(['class' => 'relative bg-cover bg-center bg-no-repeat min-h-75 md:min-h-95 bg-(image:--study-bg-mobile) md:bg-(image:--study-bg-desktop)']) }}
    style="--study-bg-mobile: url('{{ $study->imageUrl('mobile') }}'); --study-bg-desktop: url('{{ $study->imageUrl('desktop') }}');">

    <div class="overlay bg-slate-900/60"></div>

    <div class="flex flex-col mx-auto text-center z-10">

        <!-- Top Left Badge: Study Series -->

        <div class="absolute top-6 left-0 bg-black/60 backdrop-blur-xs text-amber-50 px-8 py-2.5 rounded-r-full font-sans text-sm md:text-base tracking-wide shadow-md z-5">
            @if ($heading) {{ $heading }} @else {{ __('home/study.heading') }} @endif
        </div>
`
        <!-- Main Content Cluster -->
        <div class="flex-col text-center translate-y-12 md:translate-y-18 z-10">

            <!-- Series Title -->
            <h2 class="text-4xl md:text-6xl font-serif italic font-normal tracking-wide drop-shadow-[0_1px_4px_rgba(0,0,0,0.9)] text-white">
                {{ $study->series->current_name }}
            </h2>

            <!-- Date Stamp -->
            <p class="font-sans text-xs md:text-base tracking-wider text-white mt-4">
                {{ $formattedDate }}
            </p>

            <!-- Study Title -->
            <h3 class="font-serif text-xl sm:text-2xl md:text-2xl lg:text-3xl font-bold italic drop-shadow-[0_2px_4px_rgba(0,0,0,0.3)] text-white mb-1">
                {{ $study->current_title }}
            </h3>

            <!-- Scripture Reference -->
            <p class="font-serif text-xs sm:text-base md:text-base italic text-amber-100/90 mb-3">
                <a href="{{ $study->bible_gateway_url }}" target="_blank"><span class="hover:text-white hover:underline transition-all duration-200 drop-shadow-xs">{{ $study->display_passage }}</span></a>
            </p>

            <!-- Question Sheets: stacked on mobile, one line from md: up -->
            <div id="download-links" class="flex flex-col md:flex-row md:justify-center gap-2 md:gap-6 font-sans text-sm md:text-base mb-5 text-white/90">

                @foreach($questionSheets as $sheet)
                <a href="{{ route('attachments.show', $sheet) }}" target="_blank">

                    <i class="fa-solid {{ $sheet->extension === 'pdf' ? 'fa-file-pdf text-red-300' : 'fa-file-word text-blue-300' }}"></i>

                    <span class="hover:text-white hover:underline transition-all duration-200 drop-shadow-xs">{{ $sheet->name_with_extension }}</span>
                </a>
                @endforeach

            </div>

        </div>

    </div>

</section>

@endif
