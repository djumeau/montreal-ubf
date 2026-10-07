@props(['prayerTopic'])

@php
    // Current locale's text first, the other locale's text underneath in parentheses
    $isFrench = app()->getLocale() === 'fr_CA';
@endphp

<td @class(['py-3 px-2 text-left max-w-md', 'pl-8' => $prayerTopic->parent_id])>
    <div class="flex gap-2">
        @if ($prayerTopic->parent_id)
            <i class="fa-solid fa-turn-up rotate-90 self-start shrink-0 text-white mt-1" title="{{ __('dashboard/manage-prayer-topics/index.subtopic') }}"></i>
            <span class="sr-only">{{ __('dashboard/manage-prayer-topics/index.subtopic') }}</span>
        @endif
        @if ($prayerTopic->image_url)
            <img src="{{ $prayerTopic->image_url }}" alt=""
                class="size-14 shrink-0 rounded-sm object-cover border border-slate-700">
        @endif
        <div class="min-w-0">
            <div class="text-slate-100 line-clamp-3 whitespace-pre-line">{{ $prayerTopic->current_topic }}</div>
            <div class="text-slate-400 text-xs line-clamp-2 whitespace-pre-line">({{ $isFrench ? $prayerTopic->topic_en : $prayerTopic->topic_fr }})</div>
            @if ($prayerTopic->url)
                <a href="{{ $prayerTopic->url }}" target="_blank" rel="noopener noreferrer"
                    title="{{ __('dashboard/manage-prayer-topics/index.open_link') }}"
                    class="inline-block max-w-full truncate text-xs text-sky-400 hover:text-sky-300 underline">
                    <i class="fa-solid fa-link mr-1" aria-hidden="true"></i>{{ $prayerTopic->url }}
                </a>
            @endif
        </div>
    </div>
</td>
