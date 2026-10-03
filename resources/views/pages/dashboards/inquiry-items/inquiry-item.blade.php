@props([
    'inquiry', // Contact form message (Inquiry), with its follow-up loaded: management.answeredBy
    'striped' => false, // Alternate item of the list (every second one): 50% black background
])

@php
    $management = $inquiry->management; // null until the message is handled
@endphp

<!-- Inquiry Item (Manage Inquiries): subject and status, sender, message, then its follow-up; every second one is striped (50% black).
     The whole item is clickable (mouse, Enter or Space): it sends an "open-inquiry" event with the inquiry's id for the page to handle.
     Not a <button>, as it holds the email link -->
<article role="button" tabindex="0" @click="$dispatch('open-inquiry', {{ $inquiry->id }})"
    @keydown.enter.prevent="$dispatch('open-inquiry', {{ $inquiry->id }})"
    @keydown.space.prevent="$dispatch('open-inquiry', {{ $inquiry->id }})"
    {{ $attributes->merge([
        'class' =>
            'border border-white rounded-sm p-3 text-sm cursor-pointer outline-white hover:outline-2 focus-visible:outline-2 ' .
            ($striped ? 'bg-black/50' : ''),
    ]) }}>

    <!-- Subject and status: "New" until read; answered ones show when -->
    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
        <x-inquiry-items::subject :inquiry="$inquiry" />
        <div class="flex items-center gap-2">
            <x-inquiry-items::notification :management="$management" />
            <x-inquiry-items::delete-button :inquiry="$inquiry" />
        </div>
    </div>

    <x-inquiry-items::sender-date :inquiry="$inquiry" class="mb-2" />

    <!-- The message, line breaks kept -->
    <p class="whitespace-pre-line text-slate-100">{{ $inquiry->message }}</p>

    <x-inquiry-items::follow-up :management="$management" class="mt-3" />

</article>
