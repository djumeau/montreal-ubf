@props([
    'study', // BibleStudy with series, book and the attachments to list already loaded (see BibleStudyController::index)
])

@php
    $isFrench = app()->getLocale() === 'fr_CA';

    // Title in the current locale, else the other one, else "#id"
    $title = $study->current_title ?: ($study->title_en ?: ($study->title_fr ?: "#{$study->id}"));
    $seriesName = $study->series ? ($isFrench ? $study->series->name_fr : $study->series->name_en) : null;

    // Files in type order (question sheets first), then by name as loaded
    $files = $study->attachments->sortBy(fn ($attachment) => array_search($attachment->type, \App\Models\StudyAttachment::TYPES))->values();
@endphp

<!-- Bible Study Card (view only) -->
<article {{ $attributes->merge(['class' => 'flex flex-col h-full bg-slate-800 border border-slate-700 rounded-sm shadow-lg overflow-hidden']) }}>

    <img src="{{ $study->imageUrl('desktop') }}" alt="{{ $title }}" loading="lazy"
        class="w-full aspect-video object-cover border-b border-slate-700">

    <div class="flex flex-col flex-1 p-4">

        <h3 class="text-lg font-bold text-slate-100 leading-snug">{{ $title }}</h3>

        <!-- Passage opens in Bible Gateway (NIV / SG21) -->
        @if ($study->display_passage)
            <a href="{{ $study->bible_gateway_url }}" target="_blank" rel="noopener"
                class="w-fit text-slate-300 hover:text-white hover:underline">
                {{ $study->display_passage }}
            </a>
        @endif

        @if ($seriesName)
            <p class="flex items-center gap-2 mt-1 text-sm text-slate-400">
                <i class="fa-solid fa-layer-group" aria-hidden="true"></i>
                {{ __('bible-study/index.series_label', ['name' => $seriesName]) }}
            </p>
        @endif

        <!-- Downloads: PDF previews in a new tab, DOCX downloads; pushed to the bottom so cards line up -->
        @if ($files->isNotEmpty())
            <ul class="mt-auto pt-4 space-y-2">
                @foreach ($files as $file)
                    @php
                        $isPdf = $file->extension === 'pdf';
                        $label = __('dashboard/index.attachment_' . $file->type) . ' (' . strtoupper($file->extension) . ')';
                    @endphp
                    <li>
                        <a href="{{ route('attachments.show', $file) }}" @if ($isPdf) target="_blank" rel="noopener" @endif
                            title="{{ $file->name_with_extension }}"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-slate-100 border border-slate-600 rounded-sm bg-slate-900/50 hover:bg-sky-950 hover:border-slate-400 transition-colors">
                            <i class="fa-solid {{ $isPdf ? 'fa-file-pdf text-red-400' : 'fa-file-word text-sky-400' }}" aria-hidden="true"></i>
                            <span class="flex-1 min-w-0 truncate">{{ $label }}</span>
                            <i class="fa-solid fa-download text-sky-400" aria-hidden="true"></i>
                            <span class="sr-only">{{ $isPdf ? __('dashboard/index.preview_attachment') : __('dashboard/index.download_attachment') }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

    </div>

</article>
