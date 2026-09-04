@php
    use Illuminate\Support\Str;

    $locale = app()->getLocale();
    $isFrench = ($locale === 'fr_CA');

@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('auth/index.login') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8 pt-18'>{{ __('auth/index.login') }}</h1>

    <div class="max-w-md mx-auto bg-white p-6 mb-6 rounded-lg shadow-md text-black outline-2">

        <form novalidate action="{{ $isFrench ? route('connexion.authentifier') : route('login.authenticate')}}" method="POST">
            @csrf

            <x-inputs.text id="email" name="email" type="email" label="{{ __('auth/index.email') }}" value="{{ old('email') }}" />

            <x-inputs.text id="password" name="password" type="password" label="{{ __('auth/index.password') }}" value="{{ old('password') }}" />

            <button type="submit"
                class="w-full bg-sky-900 justify-right hover:bg-sky-950 text-white font-bold py-2 px-4 rounded
                outline-1 outline-white hover:outline-2 focus:shadow-outline">{{ __('auth/index.submit') }}
            </button>

            {{-- <p class="mt-4 text-gray-200 italic">
                {{ __('auth/index.dont_have_account') }} <a href="{{ __('nav.login.url') }}"
                    class="text-blue-200 hover:text-blue-500">{{ __('auth/index.register') }}</a>
            </p> --}}

        </form>

    </div>

</x-layout>
