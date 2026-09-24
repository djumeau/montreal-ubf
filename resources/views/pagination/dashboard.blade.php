{{-- Dashboard pagination: transparent bar with the standard sky-blue buttons. Usage: $paginator->links('pagination.dashboard') --}}
@php
    $button = 'inline-flex items-center justify-center min-w-9 px-3 py-1.5 text-xs font-medium rounded outline-1 outline-white';
    $link = $button . ' bg-sky-900 hover:bg-sky-950 text-white hover:outline-2 focus:shadow-outline cursor-pointer';
    $current = $button . ' bg-sky-950 text-white outline-2';
    $disabled = $button . ' bg-sky-900/40 text-slate-400 outline-slate-500 cursor-not-allowed';

    // "&laquo; Précédent" -> "Précédent", for aria-labels on the arrow buttons
    $previousLabel = trim(html_entity_decode(strip_tags(__('pagination.previous'))), " «»");
    $nextLabel = trim(html_entity_decode(strip_tags(__('pagination.next'))), " «»");
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}"
        class="flex flex-col sm:flex-row items-center justify-between gap-3">

        <p class="text-sm text-slate-300">
            {{ __('Showing') }} {{ $paginator->firstItem() }} {{ __('to') }} {{ $paginator->lastItem() }}
            {{ __('of') }} {{ $paginator->total() }} {{ __('results') }}
        </p>

        <div class="flex flex-wrap items-center gap-2">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="{{ $disabled }}" aria-disabled="true" aria-label="{{ $previousLabel }}">
                    <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $link }}" aria-label="{{ $previousLabel }}">
                    <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                </a>
            @endif

            {{-- Page numbers ("..." separators come through as strings) --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-1 text-slate-400" aria-disabled="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="{{ $current }}" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="{{ $link }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $link }}" aria-label="{{ $nextLabel }}">
                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                </a>
            @else
                <span class="{{ $disabled }}" aria-disabled="true" aria-label="{{ $nextLabel }}">
                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
