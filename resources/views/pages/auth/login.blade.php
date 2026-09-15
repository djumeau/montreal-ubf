@php
    use Illuminate\Support\Str;

    $locale = app()->getLocale();
    $isFrench = ($locale === 'fr_CA');

@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('auth/index.login') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8 pt-18'>{{ __('auth/index.login') }}</h1>

    <div>

        <form class="flex flex-col rounded-sm shadow-md text-white border border-white p-4 w-90 justify-self-center gap-y-4 items-center" novalidate action="{{ $isFrench ? route('connexion.authentifier') : route('login.authenticate')}}" method="POST">
            @csrf

            <x-inputs.text id="email" name="email" type="email" placeholder="{{ __('auth/index.email') }}" value="{{ old('email') }}" :width=80 />

            <x-inputs.text id="password" name="password" type="password" placeholder="{{ __('auth/index.password') }}" value="{{ old('password') }}" :width=80 />

            <x-submit>Submit</x-submit>

            {{-- <p class="mt-4 text-gray-200 italic">
                {{ __('auth/index.dont_have_account') }} <a href="{{ __('nav.login.url') }}"
                    class="text-blue-200 hover:text-blue-500">{{ __('auth/index.register') }}</a>
            </p> --}}

        </form>

    </div>

</x-layout>
