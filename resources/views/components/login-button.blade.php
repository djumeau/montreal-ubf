@php

$loginIsNotActive = request()->routeIs('login') || request()->routeIs('connexion');
// dd($loginIsNotActive);

@endphp

@if ($loginIsNotActive)
    <div
        class="bg-gray-600 justify-right text-gray-300 font-bold py-2 px-4 rounded
        outline-1 outline-white">
        <i class="fa fa-user p-0 mr-1"></i>
        {{ __('nav.login.title') }}
    </div>
@else
    <a href="{{ route(__('nav.login.name')) }}"
        class="bg-sky-900/50 justify-right hover:bg-sky-950/50 text-white font-bold py-2 px-4 rounded
        outline-1 outline-white focus:shadow-outline">
        <i class="fa fa-user p-0 mr-1"></i>
        {{ __('nav.login.title') }}
    </a>
@endif
