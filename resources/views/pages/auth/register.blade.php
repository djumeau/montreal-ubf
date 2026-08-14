<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.title') }} - {{ __('auth/index.register') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pb-8'>{{ __('auth/index.register') }}</h1>

    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">

        <form action="{{ route('register.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">{{ __('auth/index.username') }}</label>
                <input type="text" id="name" name="name"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">{{ __('auth/index.email') }}</label>
                <input type="email" id="email" name="email"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">{{ __('auth/index.password') }}</label>
                <input type="password" id="password" name="password"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">{{ __('auth/index.confirm_password') }}</label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <button type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded
                focus:outline-none focus:shadow-outline">{{ __('auth/index.register') }}</button>

        </form>
    </div>

</x-layout>
