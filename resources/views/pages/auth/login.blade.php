<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('auth/index.login') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8'>{{ __('auth/index.login') }}</h1>

    <div class="max-w-md mx-auto bg-slate-800 p-6 m-6 rounded-lg shadow-md text-white outline-2">

        <form action="{{ route('login.authenticate') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-bold mb-2">{{ __('auth/index.email') }}</label>
                <input type="email" id="email" name="email"
                    class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-bold mb-2">{{ __('auth/index.password') }}</label>
                <input type="password" id="password" name="password"
                    class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <button type="submit"
                class="bg-blue-500 justify-right hover:bg-blue-700 text-white font-bold py-2 px-4 rounded
                outline-1 outline-white hover:outline-2 focus:shadow-outline">{{ __('auth/index.submit') }}
            </button>

        </form>
    </div>

</x-layout>
