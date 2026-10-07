@php

    $isFrench = app()->getLocale() === 'fr_CA';

@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} – {{ __('nav.confidentiality.title') }}</x-slot>

    <x-slot name="hero">
        <x-page-banner
        :title="__('nav.confidentiality.title')"
        desktop='storage/images/home/confidentiality_policy-desktop.jpg'
        mobile='storage/images/home/confidentiality_policy-mobile.jpg' />
    </x-slot>

    {{-- Dynamic localized date rendering --}}
    <p class="text-xs italic text-slate-100 mb-2">
        @if ($isFrench)
            {{ __('home/index.privacy.last_updated', ['date' => \Carbon\Carbon::parse('2026-10-07')->isoFormat('D MMMM YYYY')]) }}
            {{-- Output: Dernière mise à jour : le 7 octobre 2026 --}}
        @else
            {{ __('home/index.privacy.last_updated', ['date' => \Carbon\Carbon::parse('2026-10-07')->isoFormat('MMMM D, YYYY')]) }}
            {{-- Output: Last updated: October 7, 2026 --}}
        @endif
    </p>

    {{-- Intro Statement --}}
    <p class="text-xl leading-relaxed px-8 pt-4 mb-8 text-white">
        {{ __('home/index.privacy.intro') }}
    </p>

    {{-- Dynamic Sections Loop --}}
    <div class="space-y-8 px-8">
        @foreach (__('home/index.privacy.sections') as $section)
            <section class="space-y-2">
                <h2 class="text-lg font-bold text-white">
                    {{ $section['title'] }}
                </h2>
                <p class="text-sm leading-relaxed text-slate-200">
                    {{ $section['text'] }}
                </p>
                {{-- Optional bulleted list under the paragraph --}}
                @if (!empty($section['items']))
                    <ul class="list-disc pl-5 space-y-1 text-sm leading-relaxed text-slate-200">
                        @foreach ($section['items'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    </div>

    {{-- Officer --}}
    <p class="text-base leading-relaxed pt-8 mb-2 text-white">
        <span class="font-medium">{{ __('home/index.privacy.contact') }}
            @if ($isFrench)
            : @else:
            @endif
        </span>
        <span class="italic">{{ __('home/index.privacy.name') }}</span>
    </p>

    {{-- Email --}}
    <p class="text-base leading-relaxed mb-2 text-white">
        <i class="fa-solid fa-envelope"></i> <a href="mailto:{{ __('home/index.privacy.email') }}"
            class="hover:underline">{{ __('home/index.privacy.email') }}</a>
    </p>

</x-layout>
