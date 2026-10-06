@props([
    'inquiry', // Contact form message (Inquiry) to delete; its follow-up (manage_inquiries) goes with it
])

<!-- Delete Inquiry: red trash button, then a Yes / No modal.
     .stop on its clicks and keys: the inquiry item around it is clickable too, and must not open -->
<div x-data="{ showDeleteModal: false }" class="inline-block" @click.stop @keydown.enter.stop @keydown.space.stop>

    <button type="button" @click="showDeleteModal = true" aria-label="{{ __('dashboard/manage-inquiries/index.delete_inquiry') }}"
        title="{{ __('dashboard/manage-inquiries/index.delete_inquiry') }}"
        class="px-2 py-1 bg-red-700 hover:bg-red-800 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
        <i class="fa-solid fa-trash" aria-hidden="true"></i>
    </button>

    <!-- Teleported to <body>: the item's outline and stacking don't apply to the modal -->
    <template x-teleport="body">
        <div x-show="showDeleteModal" x-cloak @keydown.escape.window="showDeleteModal = false"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity text-white"
            x-transition>

            <div @click.away="showDeleteModal = false" role="dialog" aria-modal="true"
                class="bg-slate-800 rounded-sm max-w-md w-full p-6 shadow-xl border border-white text-center whitespace-normal">

                <h3 class="text-lg font-bold mb-2">{{ __('dashboard/manage-inquiries/index.delete_inquiry_confirm') }}</h3>
                <p class="mb-4 text-sm text-slate-300">{{ $inquiry->inquiry->label() }} · {{ $inquiry->name }}</p>

                <form action="{{ route('inquiries.destroy', $inquiry) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="flex justify-center space-x-3">
                        <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors cursor-pointer">
                            {{ __('dashboard/index.no') }}
                        </button>
                        <button type="submit"
                            class="px-4 py-2 border outline-white bg-red-700 hover:bg-red-800 text-white font-medium rounded-sm transition-colors cursor-pointer">
                            {{ __('dashboard/index.yes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>
