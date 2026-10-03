@props([
    'management' => null, // Follow-up of the inquiry (ManageInquiry), with answeredBy loaded; null until the message is handled
])

@php
    // "Thursday, October 1st, 2026, 4:40 PM" / "Jeudi 1 octobre 2026, 16 h 40"
    $dateTime = fn ($date) => ucfirst($date->isoFormat(__('bible-study-schedule/index.list_day_format')))
        . ', ' . $date->isoFormat(__('bible-study-schedule/index.time_format'));
@endphp

<!-- Inquiry Follow-up (Manage Inquiries): who answered and when, and the note; nothing while there is neither -->
@if ($management?->answered_at || $management?->note)
    <div {{ $attributes->merge(['class' => 'pt-2 border-t border-slate-700 text-slate-300 space-y-1']) }}>
        @if ($management->answered_at)
            <p>
                <i class="fa-solid fa-reply mr-1" aria-hidden="true"></i>
                {{ __('dashboard/index.inquiry_answered_on', ['date' => $dateTime($management->answered_at)]) }}
                @if ($management->answeredBy)
                    {{ __('dashboard/index.inquiry_answered_by', ['name' => $management->answeredBy->name]) }}
                @endif
            </p>
        @endif
        @if ($management->note)
            <p class="whitespace-pre-line">
                <i class="fa-regular fa-note-sticky mr-1" aria-hidden="true"></i>{{ $management->note }}
            </p>
        @endif
    </div>
@endif
