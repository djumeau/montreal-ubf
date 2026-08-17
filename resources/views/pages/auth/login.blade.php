<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('auth/index.login') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8'>{{ __('auth/index.login') }}</h1>

    <div class="max-w-md mx-auto bg-slate-800 p-6 m-6 rounded-lg shadow-md text-white outline-2">

        <form novalidate action="{{ route('login.authenticate') }}" method="POST">
            @csrf
            <div class="mb-4">
                <input  type="email" id="email" name="email"
                        placeholder="{{ __('auth/index.email') }}" value="{{ old('email') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline">
                @error('email')
                    <span id='errorText' style="color: rgb(255, 0, 0);">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <input type="password" id="password" name="password" placeholder="{{ __('auth/index.password') }}" value="{{ old('password') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline">
                @error('password')
                    <span style="color: rgb(255, 0, 0);">{{ $message }} </span>
                @enderror
            </div>

            <button type="submit"
                class="bg-blue-500 justify-right hover:bg-blue-700 text-white font-bold py-2 px-4 rounded
                outline-1 outline-white hover:outline-2 focus:shadow-outline">{{ __('auth/index.submit') }}
            </button>

            <p class="mt-4 text-gray-200 italic">
                {{ __('auth/index.dont_have_account') }} <a href="{{ route('register') }}"
                    class="text-blue-200 hover:text-blue-500">{{ __('auth/index.register') }}</a>
            </p>

        </form>
    </div>

</x-layout>
