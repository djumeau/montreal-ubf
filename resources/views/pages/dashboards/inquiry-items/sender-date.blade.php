@props([
    'inquiry', // Contact form message (Inquiry): who sent it and when
])

@php
    // "Thursday, October 1st, 2026, 4:40 PM" / "Jeudi 1 octobre 2026, 16 h 40"
    $sentAt =
        ucfirst($inquiry->created_at->isoFormat(__('bible-study-schedule/index.list_day_format'))) .
        ', ' .
        $inquiry->created_at->isoFormat(__('bible-study-schedule/index.time_format'));
@endphp

<!-- Inquiry Sender and Date (Manage Inquiries): name, email (opens the mail program to reply), signed in or not, and when it was sent -->
<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-x-4 gap-y-1 text-slate-300']) }}>

    <span><i class="fa-solid fa-user mr-1" aria-hidden="true"></i>{{ $inquiry->name }}</span>

    <!-- @click.stop: opens the mail program without also opening the inquiry item around it -->
    <a href="mailto:{{ $inquiry->email }}" @click.stop @keydown.enter.stop
        class="text-sky-100 hover:text-sky-300 hover:underline">
        <i class="fa-solid fa-envelope mr-1" aria-hidden="true"></i>{{ $inquiry->email }}
    </a>

    @if ($inquiry->user)
        <span class="text-xs italic">{{ __('dashboard/manage-inquiries/index.inquiry_signed_in') }}</span>
    @endif

    <span><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>{{ $sentAt }}</span>

</div>
