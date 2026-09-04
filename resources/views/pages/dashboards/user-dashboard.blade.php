<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('dashboard.title') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pt-18 pb-8'>{{ __('dashboard/index.welcome', ['name' => $user->name]) }}
    </h1>

    <div class="flex flex-col md:flex-row md:items-start flex-wrap justify-center items-center gap-6 pb-6">

        <!-- Left Column: Profile Info Card -->
        <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-sm border border-slate-800 flex flex-col items-center text-center">

            <!-- Avatar Placeholder -->
            <div class="w-16 h-16 bg-blue-950 text-blue-200 rounded-full flex items-center justify-center text-4xl font-bold mb-4">
                {{ substr($user->name, 0, 1) }}
            </div>

            <!-- User Details - {{ $user->name }} -->
            <h2 class="text-xl font-bold text-black mb-2>{{ $user->name }}</h2>
            <p class="text-sm text-gray-900 mb-4">{{ $user->email }}</p>

            <!-- Profile Category -->
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-950 text-blue-200 border border-indigo-200">
                {{ $user->privileges }}
            </span>
        </div>

        <div class="border border-slate-800 bg-white p-6 mb-6 rounded-lg shadow-md text-black outline-2">

            <h2 class="text-center text-lg mb-4">{{ __('auth/index.update_password') }}</h2>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <x-inputs.text id="current_password" name="current_password" type="password" label="{{ __('auth/index.current_password') }}" required />

                <x-inputs.text id="new_password" name="new_password" type="password" label="{{ __('auth/index.new_password') }}" required />

                <x-inputs.text id="confirm_password" name="confirm_password" type="password" label="{{ __('auth/index.confirm_password') }}" required />

                <button type="submit"
                    class="w-full bg-sky-900 justify-right hover:bg-sky-950 text-white font-bold py-2 px-4 rounded
                    outline-1 outline-white hover:outline-2 focus:shadow-outline">{{ __('auth/index.update') }}
                </button>

                <!-- Success Message Banner -->
                @if (session('status') === 'password-updated')
                    <div style="color: green;">
                        {{ __('auth/index.updated') }}
                    </div>
                @endif

            </form>

        </div>
    </div>

</x-layout>
