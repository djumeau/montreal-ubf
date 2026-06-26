@props([
    'image' => './images/ministry/montreal_ministry_20190616-original.jpg',
    'title' => 'Welcome!',
    'description' => 'For God so loved the world!',
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-md overflow-hidden border border-slate-200']) }}>

    <!-- Image Wrapper -->
    <div class="w-full h-52 overflow-hidden bg-slate-100">
        <img src="{{ asset($image) }}" class="relative w-full h-full object-cover pointer-events-none select-none"
            loading="lazy" />
    </div>

    <!-- Content Wrapper -->
    <div class="p-5">
        <h3 class="text-xl text-center font-bold text-slate-900 mb-2 tracking-tight">
            {{ $title }}
        </h3>
        <p class="text-slate-700 leading-relaxed">
            {{ $slot }}
        </p>
    </div>
</div>
