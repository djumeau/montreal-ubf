@props(['prayerTopic'])

<td class="py-3 px-2">
    @if ($prayerTopic->answered)
        <i class="fa-solid fa-circle-check text-emerald-400"
            title="{{ __('dashboard/manage-prayer-topics/index.answered') }}"></i>
        <span class="sr-only">{{ __('dashboard/manage-prayer-topics/index.answered') }}</span>
    @else
        <span class="text-slate-500" title="{{ __('dashboard/manage-prayer-topics/index.not_answered') }}">—</span>
        <span class="sr-only">{{ __('dashboard/manage-prayer-topics/index.not_answered') }}</span>
    @endif
</td>
