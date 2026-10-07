@props([
    'category', // PrayerCategory of the card
    'headerImage' => null, // URL of the image fading in from the right; null without one
])

@php
    use App\Enums\PrayerCategory;
@endphp

<!-- Card heading: icon, name and description; the image fades in from the right -->
<header class="relative flex items-start gap-4 p-4 bg-black/50 overflow-hidden">
    @if ($headerImage)
        <img src="{{ $headerImage }}" alt="" loading="lazy"
            class="absolute inset-y-0 right-0 h-full w-1/2 object-cover mask-l-from-40%">
    @endif

    <!-- The General card shows the white logo of the current language, as in the header (CBU in French, UBF otherwise); the others their icon -->
    @if ($category === PrayerCategory::GENERAL)
        <img src="{{ asset(app()->getLocale() === 'fr_CA' ? 'images/icons/logo_cbu_white.svg' : 'images/icons/logo_ubf_white.svg') }}"
            alt="" aria-hidden="true" class="relative shrink-0 size-12 object-contain">
    @else
        <i class="relative fa-solid {{ $category->icon() }} size-12 text-center text-3xl text-white"
            aria-hidden="true"></i>
    @endif

    <div class="relative self-center">
        <!-- The General card goes without its name: only its description shows under "Prayer Topics" -->
        @if ($category === PrayerCategory::GENERAL)
            <h3 class="text-base font-medium">{{ $category->description() }}</h3>
        @else
            <h3 class="text-lg font-bold">{{ $category->label() }}</h3>
            <p class="text-sm text-slate-300">{{ $category->description() }}</p>
        @endif
    </div>
</header>
