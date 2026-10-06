@props([
    'management' => null, // Follow-up of the inquiry (ManageInquiry); null until the message is handled
])

@php
    // One status per message, checked in this order: answered (an answer means it was read),
    // then read (seen, still waiting for an answer), then new (not read yet)
    $status = match (true) {
        (bool) $management?->answered_at => 'answered',
        (bool) $management?->read_at => 'read',
        default => 'new',
    };
@endphp

<!-- Inquiry Notification (Manage Inquiries): "New" until the message is read, "Read" while it waits for an answer,
     "Answered" once it has one (a button: clicking it undoes the answer) -->
<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2 text-xs']) }}>
    @switch($status)
        @case('answered')
            <!-- Clicking "Answered" undoes the answer: the message goes back to "Read" (its note is kept).
                 .stop: the inquiry item around it is clickable too, and must not open -->
            <form action="{{ route('inquiries.unanswer', $management->inquiry_id) }}" method="POST"
                @click.stop @keydown.enter.stop @keydown.space.stop>
                @csrf
                @method('PUT')
                <button type="submit" title="{{ __('dashboard/manage-inquiries/index.undo_answered') }}"
                    class="px-2 py-0.5 rounded-full border bg-emerald-800 hover:bg-emerald-900 outline-white hover:outline-2 text-white cursor-pointer">
                    <i class="fa-solid fa-check mr-1" aria-hidden="true"></i>{{ __('dashboard/manage-inquiries/index.inquiry_answered') }}
                </button>
            </form>
        @break

        @case('read')
            <span class="px-2 py-0.5 rounded-full border bg-slate-600 outline-white text-white">
                <i class="fa-solid fa-eye mr-1" aria-hidden="true"></i>{{ __('dashboard/manage-inquiries/index.inquiry_read') }}
            </span>
        @break

        @case('new')
            <span class="px-2 py-0.5 border outline-white rounded-full bg-sky-500 text-white font-medium">
                <i class="fa-solid fa-bell mr-1" aria-hidden="true"></i>{{ __('dashboard/manage-inquiries/index.inquiry_new') }}</span>
        @break
    @endswitch
</div>
