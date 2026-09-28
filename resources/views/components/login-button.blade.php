@props(['isMobile' => false])

@php

$loginIsNotActive = request()->routeIs('login') || request()->routeIs('connexion') || request()->routeIs('register') || request()->routeIs('enregistrer');
// dd($loginIsNotActive);

@endphp

@if ($loginIsNotActive)
    <div
        class=" {{ $isMobile ? 'flex justify-center' : 'inline-flex' }} items-center gap-2 whitespace-nowrap bg-gray-600 text-gray-300 font-bold py-2 px-2 rounded
        outline-1 outline-white">
        <i class="fa fa-user p-0 shrink-0"></i>
        {{ __('nav.login.title') }}
    </div>
@else
    <a href="{{ route(__('nav.login.name')) }}"
        class=" {{ $isMobile ? 'flex justify-center' : 'inline-flex' }} items-center gap-2 whitespace-nowrap bg-sky-900/50 hover:bg-sky-950/50 text-white font-bold py-2 px-2 rounded cursor-pointer
        outline-1 outline-white focus:shadow-outline">
        <i class="fa fa-user p-0 shrink-0"></i>
        {{ __('nav.login.title') }}
    </a>
@endif
