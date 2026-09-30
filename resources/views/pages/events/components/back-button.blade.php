@props([
    'icon' => null, // Font Awesome classes, e.g. 'fa-solid fa-arrow-left'
])

@php
    // Back to the events list, keeping its filters when we came from it
    $eventsUrl = url(__('nav.events.url'));
    $backUrl = strtok(url()->previous(), '?') === $eventsUrl ? url()->previous() : $eventsUrl;
@endphp

<!-- Back to Events: same style as the header's login button (components/login-button.blade.php).
     Extra classes (e.g. margins) are added to these -->
<a href="{{ $backUrl }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 whitespace-nowrap bg-sky-900/50 hover:bg-sky-950/50 text-white font-bold mt-2 p-4 rounded
    outline-1 outline-white focus:shadow-outline']) }}>
    @if ($icon)
        <i class="{{ $icon }}"></i>
    @endif
    {{ $slot }}
</a>
