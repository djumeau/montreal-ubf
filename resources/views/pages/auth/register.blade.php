<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('auth/index.register') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8'>{{ __('auth/index.register') }}</h1>

    <div class="max-w-md mx-auto bg-slate-800 p-6 mb-6 rounded-md shadow-md outline-2 text-white">

        <form novalidate action="{{ route('register.store') }}" method="POST">

            @csrf

            <div class="mb-4">
                <input type="text" id="name" name="name" placeholder="{{ __('auth/index.username') }}" value="{{ old('name') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline">
                @error('name')
                    <span style="color: rgb(255, 0, 0);">{{ __('auth/index.validation.name_required') }}</span>
                @enderror
            </div>

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
                    <span style="color: rgb(255, 0, 0);">{{ __('auth/index.validation.password_required') }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="{{ __('auth/index.confirm_password') }}" value="{{ old('password_confirmation') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3  leading-tight focus:outline-none focus:shadow-outline">
                @error('password_confirmation')
                    <span style="color: rgb(255, 0, 0);">{{ __('auth/index.validation.password_confirmed') }}</span>
                @enderror
            </div>

            <button type="submit"
                class="bg-blue-500 align-right hover:bg-blue-700 text-white font-bold py-2 px-4 rounded
                outline-1 outline-white hover:outline-2 focus:shadow-outline">{{ __('auth/index.register') }}
            </button>

            <p class="mt-4 text-gray-200 italic">
                {{ __('auth/index.already_have_account') }} <a href="{{ route('login') }}"
                    class="text-blue-200 hover:text-blue-500">{{ __('auth/index.login') }}</a>
            </p>

        </form>
    </div>

</x-layout>
