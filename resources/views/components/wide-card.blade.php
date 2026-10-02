{{-- Wide Card: the card component at the full width of its container (as wide as a blurb).
     Image above the text on phones, beside it from md up. Usage:
     <x-wide-card image="./images/other/photo.jpg" alt="..." title="Optional title">Text</x-wide-card> --}}
@props([
    'image' => './images/ministry/montreal_ministry_20190616-original.jpg',
    'alt' => '',
    'title' => null,
    'bgcolor' => 'bg-slate-900', // Background colour of the card
    'textcolor' => 'text-white', // Text colour of the title and text
])

<div
    {{ $attributes->merge(['class' => "w-full flex flex-col md:flex-row rounded-xl shadow-md overflow-hidden border border-slate-200 $bgcolor $textcolor"]) }}>

    <!-- Image Wrapper -->
    <div class="w-full h-52 md:h-auto md:w-2/5 md:min-h-64 shrink-0 overflow-hidden">
        <img src="{{ asset($image) }}" alt="{{ $alt }}"
            class="w-full h-full object-cover pointer-events-none select-none" loading="lazy" />
    </div>

    <!-- Content Wrapper -->
    <div class="flex flex-col justify-center p-5 md:p-8">
        @if ($title)
            <h3 class="text-xl text-center font-bold mb-2 tracking-tight">
                {{ $title }}
            </h3>
        @endif
        <p class="leading-relaxed">
            {{ $slot }}
        </p>
    </div>
</div>
