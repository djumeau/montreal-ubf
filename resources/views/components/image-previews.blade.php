@props([
    'model', // Alpine expression of the current image URLs, e.g. "editSeries.previews": {desktop, mobile, square}
])

@php
    // Same order and shapes as the upload fields: Desktop (16:9), Mobile (3:2), Square / thumbnail (1:1)
    $slots = ['desktop' => 'aspect-video', 'mobile' => 'aspect-3/2', 'square' => 'aspect-square'];
@endphp

<!-- Current images, small and side by side, for reference while replacing them -->
<div {{ $attributes->merge(['class' => 'mb-4 flex items-end justify-center gap-3']) }} x-show="{{ $model }}">
    @foreach ($slots as $slot => $aspect)
        <figure class="flex flex-col items-center gap-1" x-show="{{ $model }}?.{{ $slot }}">
            <img :src="{{ $model }}?.{{ $slot }}" alt="" class="h-16 sm:h-20 {{ $aspect }} rounded-sm object-cover border-2 border-slate-600">
            <figcaption class="text-xs text-slate-400">{{ __('dashboard/index.preview_' . $slot) }}</figcaption>
        </figure>
    @endforeach
</div>
