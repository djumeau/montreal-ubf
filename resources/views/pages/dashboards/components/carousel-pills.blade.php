@props([
    'count' => 0, // Number of slides
    'small' => false, // Smaller pills, e.g. over a phone preview
])

<!-- Pill indicators of a carousel: one per slide, the current one longer and brighter; a click shows that slide
     (reads and sets "slide" of the page's x-data). Nothing shows for a single slide -->
@if ($count > 1)
    <div class="absolute inset-x-0 {{ $small ? 'bottom-1.5 gap-1' : 'bottom-2 gap-2' }} flex justify-center">
        @for ($index = 0; $index < $count; $index++)
            <button type="button" @click.stop="slide = {{ $index }}"
                aria-label="{{ __('dashboard/manage-home-page/index.show_slide', ['number' => $index + 1]) }}"
                :class="slide === {{ $index }} ? 'bg-white {{ $small ? 'w-3' : 'w-6' }}' : 'bg-white/40 {{ $small ? 'w-2' : 'w-4' }}'"
                class="{{ $small ? 'h-1' : 'h-1.5' }} rounded-full transition-all cursor-pointer"></button>
        @endfor
    </div>
@endif
