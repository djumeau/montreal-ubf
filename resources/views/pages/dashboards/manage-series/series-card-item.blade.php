@php
    // Current locale's name first, the other locale's name underneath in parentheses
    $isFrench = app()->getLocale() === 'fr_CA';
    $book = $series->book;
@endphp

<div class="flex gap-3 border border-slate-800 rounded-sm p-3">
    <img src="{{ $series->imageUrl('thumbnail') }}" alt="{{ $series->name_en }}"
        class="size-16 shrink-0 rounded-sm object-cover border border-slate-700">

    <div class="flex-1 min-w-0">
        <div class="font-bold text-slate-100">
            <span class="text-slate-400 font-normal">#{{ $series->id }}</span>
            {{ $isFrench ? $series->name_fr : $series->name_en }}
        </div>
        <div class="text-slate-400 text-xs mb-2">
            ({{ $isFrench ? $series->name_en : $series->name_fr }})
        </div>

        <div class="text-slate-300 text-sm">
            {{ __('dashboard/index.related_book') }}:
            @if ($book)
                {{ $isFrench ? $book->name_fr : $book->name_en }}
                ({{ $isFrench ? $book->name_en : $book->name_fr }})
            @else
                {{ __('dashboard/index.multiple') }}
            @endif
        </div>
        <div class="text-slate-300 text-sm flex items-center gap-2 my-1">
            {{ __('dashboard/index.studies') }}:
            <a href="{{ route(__('nav.manage-studies.name'), ['series' => $series->id]) }}" title="{{ __('dashboard/index.view_studies') }}"
                aria-label="{{ __('dashboard/index.view_studies') }}: {{ $series->bible_studies_count }}"
                class="inline-flex items-center justify-center gap-1.5 min-w-9 px-2.5 py-1 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>{{ $series->bible_studies_count }}
            </a>
        </div>
        <div class="text-slate-300 text-sm mb-3">
            {{ __('dashboard/index.dates') }}: {{ $series->localized_dates ?? '—' }}
        </div>

        <div class="flex items-center gap-2">
            <button type="button" @click="openEdit(@js($rowData))"
                class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                <i class="fas fa-pen mr-1"></i>{{ __('dashboard/index.edit') }}
            </button>
            <button type="button" @click="openDelete(@js($rowData))" aria-label="{{ __('dashboard/index.delete_series') }}"
                class="px-2 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </div>
    </div>
</div>
