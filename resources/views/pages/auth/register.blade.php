@php
    use Illuminate\Support\Str;

    $locale = app()->getLocale();
    $isFrench = ($locale === 'fr_CA');

@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('auth/index.register') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8 pt-18'>{{ __('auth/index.register') }}</h1>

    <div class="max-w-md mx-auto bg-slate-800 p-6 mb-6 rounded-md shadow-md outline-2 text-white">

        <form novalidate action="{{ route('register.store') }}" method="POST">

            @csrf

            <x-inputs.text id="name" name="name" placeholder="{{ __('auth/index.name') }}" value="{{ old('name') }}" />

            <x-inputs.text id="email" name="email" type="email" placeholder="{{ __('auth/index.email') }}" value="{{ old('email') }}" />

            <x-inputs.text id="password" name="password" type="password" placeholder="{{ __('auth/index.password') }}" value="{{ old('password') }}" />

            <x-inputs.text id="password_confirmation" name="password_confirmation" type="password" placeholder="{{ __('auth/index.confirm_password') }}" value="{{ old('password_confirmation') }}" />

            <button type="submit"
                class="bg-blue-500 align-right hover:bg-blue-700 text-white font-bold py-2 px-4 rounded
                outline-1 outline-white hover:outline-2 focus:shadow-outline">{{ __('auth/index.register') }}
            </button>

            {{-- <p class="mt-4 text-gray-200 italic">
                {{ __('auth/index.already_have_account') }} <a href="{{ $isFrench ? route('connexion') : route('login') }}"
                    class="text-blue-200 hover:text-blue-500">{{ __('auth/index.login') }}</a> --}}

        </form>
    </div>

</x-layout>
