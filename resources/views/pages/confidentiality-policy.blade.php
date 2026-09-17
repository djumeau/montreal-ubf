@php

    $isFrench = app()->getLocale() === 'fr_CA';

@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} – {{ __('nav.confidentiality.title') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pt-18 pb-8'>{{ __('nav.confidentiality.title') }}</h1>

    {{-- Dynamic localized date rendering --}}
    <p class="text-xs italic text-slate-100 mb-2">
        @if ($isFrench)
            {{ __('home/index.privacy.last_updated', ['date' => \Carbon\Carbon::parse('2026-09-17')->isoFormat('D MMMM YYYY')]) }}
            {{-- Output: Dernière mise à jour : 17 septembre 2026 --}}
        @else
            {{ __('home/index.privacy.last_updated', ['date' => \Carbon\Carbon::parse('2026-09-17')->isoFormat('MMMM D, YYYY')]) }}
            {{-- Output: Last Updated: September 17, 2026 --}}
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
        <span class="italic">{{ __('home/index.privacy.name') }}</italic>
    </p>

    {{-- Email --}}
    <p class="text-base leading-relaxed mb-2 text-white">
        <i class="fa-solid fa-envelope"></i> <a href="mailto:{{ __('home/index.privacy.email') }}"
            class="hover:underline">{{ __('home/index.privacy.email') }}</a>
    </p>

</x-layout>
