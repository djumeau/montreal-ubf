@props(['title', 'variant' => 'white', 'isDark' => null])

@php
    $style = '';
    $bgClass = '';
    $determineDark = false;

    if (is_array($variant)) {
        // Safe explicit array index access
        $color1 = $variant[0] ?? '#4f46e5';
        $color2 = $variant[1] ?? '#7c3aed';

        // Convert plain Tailwind names to modern v4 CSS variables
        $c1 = str_starts_with($color1, '#') ? $color1 : "var(--color-{$color1})";
        $c2 = str_starts_with($color2, '#') ? $color2 : "var(--color-{$color2})";

        // Tailwind v4 engine safely handles mixed dynamic declarations via pure inline styles
        $style = "background: linear-gradient(to right, {$c1}, {$c2});";
        $determineDark = true;
    } else {
        if (str_starts_with($variant, '#')) {
            $style = "background-color: {$variant};";
        } else {
            $bgClass = "bg-{$variant}";
        }

        $determineDark = preg_match(
            '/(black|slate|zinc|neutral|stone|gray|indigo|blue|purple|violet|emerald|rose)-(500|600|700|800|900|950)/',
            $variant,
        );
    }

    $isDarkBg = $isDark ?? $determineDark;
    $titleColor = $isDarkBg ? 'text-white' : 'text-slate-900';
    $textColor = $isDarkBg ? 'text-white/90' : 'text-slate-600';
@endphp

<div {{ $attributes->merge(['class' => "w-full py-12 px-4 $bgClass"]) }} style="{{ $style }}">
    <div class="max-w-3xl mx-auto">
        <h3 class="text-2xl font-bold tracking-tight text-center sm:text-3xl {{ $titleColor }}">
            {{ $title }}
        </h3>

        @if ($slot->isNotEmpty())
            <!-- [&_p]:text-inherit overrides Tailwind v4 Preflight resets for slotted p tags -->
            <div class="text-lg leading-relaxed text-left {{ $textColor }} mt-4 space-y-4 [&_p]:text-inherit">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
