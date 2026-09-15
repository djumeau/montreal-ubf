@props([
    'url' => 'https://www.montrealubf.org',
    'isActive' => false, // If true, disable link
    'icon' => 'fa-user-cog',
])

<div>
    <a @if ($isActive) role="link" aria-disabled="true" @else href="{{ $url }}" @endif
        @class([
            'btn w-full flex items-center p-3 transition-colors focus:outline-none',
            'bg-slate-100 font-medium text-slate-900 border-b border-t border-slate-100 cursor-default' => $isActive,
            'hover:bg-white text-slate-100 hover:text-slate-900 cursor-pointer' => !$isActive,
        ]) @disabled(!$isActive)>

        <i class="fas {{ $icon }} w-6 text-center"></i>
        <span x-show="sidebarOpen" class="ml-3 text-sm">{{ $slot }}</span>

    </a>
</div>
