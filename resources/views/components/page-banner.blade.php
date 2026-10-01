@props([
    'title',
    'subtitle' => null,
    'desktop' => 'storage/images/events/events-desktop.jpg', // Paths from public/
    'mobile' => 'storage/images/events/events-mobile.jpg',
])

<!-- Page Banner (e.g. Events, Bible Study Schedule): full width from the top of the page, behind the fixed header (pt-24 keeps the text below it).
     Mobile image below md, desktop image from md up. Goes in the layout's "hero" slot -->
<section class="relative h-72 md:h-128 pt-24 flex items-center justify-center overflow-hidden">

    <div class="absolute inset-0 bg-cover bg-center md:hidden"
        style="background-image: url('{{ asset($mobile) }}')"></div>
    <div class="absolute inset-0 bg-cover bg-center hidden md:block"
        style="background-image: url('{{ asset($desktop) }}')"></div>

    <div class="absolute inset-0 bg-slate-900/25"></div>

    <div class="relative z-10 text-center px-4">
        <h1 class="text-3xl md:text-5xl font-bold drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
            {{ $title }}
        </h1>
        @if ($subtitle)
            <p class="mt-2 text-lg md:text-xl text-slate-100 drop-shadow-[0_1px_3px_rgba(0,0,0,0.8)]">
                {{ $subtitle }}
            </p>
        @endif
        <div class="mx-auto mt-4 h-0.5 w-14 bg-white/80"></div>
    </div>
</section>
