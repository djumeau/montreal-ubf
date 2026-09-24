
@php
    // Current locale's name first, the other locale's name underneath in parentheses
    $isFrench = app()->getLocale() === 'fr_CA';
    $book = $series->book;
@endphp

<tr @class([
    'border-b border-slate-800 align-middle',
    'bg-black/50' => $striped,
])>
    <td class="py-3 px-2 text-slate-300">{{ $series->id }}</td>
    <td class="py-3 px-2">
        <img src="{{ $series->imageUrl('thumbnail') }}"
            alt="{{ $series->name_en }}"
            class="size-14 mx-auto rounded-sm object-cover border border-slate-700">
    </td>
    <td class="py-3 px-2">
        <div class="text-slate-100">
            {{ $isFrench ? $series->name_fr : $series->name_en }}</div>
        <div class="text-slate-400 text-xs">
            ({{ $isFrench ? $series->name_en : $series->name_fr }})
        </div>
    </td>
    <td class="py-3 px-2">
        @if ($book)
            <div class="text-slate-100">
                {{ $isFrench ? $book->name_fr : $book->name_en }}</div>
            <div class="text-slate-400 text-xs">
                ({{ $isFrench ? $book->name_en : $book->name_fr }})</div>
        @else
            <span
                class="text-slate-100">{{ __('dashboard/index.multiple') }}</span>
        @endif
    </td>
    <td class="py-3 px-2">
        <a href="{{ route(__('nav.manage-studies.name'), ['series' => $series->id]) }}" title="{{ __('dashboard/index.view_studies') }}"
            aria-label="{{ __('dashboard/index.view_studies') }}: {{ $series->bible_studies_count }}"
            class="inline-flex items-center justify-center gap-1.5 min-w-9 px-2.5 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>{{ $series->bible_studies_count }}
        </a>
    </td>
    <td class="py-3 px-2 text-slate-300">{{ $series->localized_dates ?? '—' }}</td>
    <td class="py-3 px-2 whitespace-nowrap">
        <div class="flex items-center justify-center gap-2">
            <button type="button" @click="openEdit(@js($rowData))"
                class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                <i class="fas fa-pen mr-1"></i>{{ __('dashboard/index.edit') }}
            </button>
            <button type="button" @click="openDelete(@js($rowData))" aria-label="{{ __('dashboard/index.delete_series') }}"
                class="px-2 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </div>
    </td>
</tr>
