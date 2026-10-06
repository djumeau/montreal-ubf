@props(['user'])

<!-- "processing" is set when the form is sent (the new password is emailed, which takes a few seconds),
     and cleared if the page is shown again from the browser's Back button -->
<div x-data="{ showResetModal: false, processing: false }" @pageshow.window="processing = false" class="inline-block">

    <button type="button" @click="showResetModal = true"
        class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer whitespace-nowrap">
        {{ __('dashboard/manage-users/index.reset_password') }}
    </button>

    <div x-show="showResetModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
        x-transition>

        <div @click.away="if (!processing) showResetModal = false"
            class="bg-slate-800 rounded-sm max-w-md w-full p-6 shadow-xl border dark:border-slate-700 text-center whitespace-normal">

            <h3 class="text-lg font-bold mb-4">{{ __('dashboard/manage-users/index.reset_password_confirm') }}</h3>

            <form action="{{ route('users.reset-password', $user) }}" method="POST" @submit="processing = true">
                @csrf

                <!-- No / Yes, replaced by "Processing..." once sent, so it cannot be sent twice -->
                <div x-show="!processing" class="flex justify-center space-x-3">
                    <button type="button" @click="showResetModal = false"
                        class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors cursor-pointer">
                        {{ __('dashboard/index.no') }}
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-sky-900/50 hover:bg-sky-950/50 border text-slate-100 rounded-sm transition-colors cursor-pointer">
                        {{ __('dashboard/index.yes') }}
                    </button>
                </div>

                <x-processing />
            </form>
        </div>
    </div>

</div>
