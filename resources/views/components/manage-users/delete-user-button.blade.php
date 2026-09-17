@props(['user', 'adminCount' => 0])

@php
    $isLastAdmin = $user->role === \App\Enums\Role::ADMIN && $adminCount <= 1;
    $isSelf = $user->id === auth()->id();
@endphp

{{-- Self-deletion from this table is never allowed (use the Profile page instead), except
     the last-admin info modal, which is safe to show since it has no destructive action. --}}
@if (!$isSelf || $isLastAdmin)
    <div x-data="{ showDeleteModal: false }" class="inline-block">

        @if ($isLastAdmin)
            <button type="button" @click="showDeleteModal = true"
                class="px-2 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        @else
            <button type="button" @click="showDeleteModal = true"
                class="px-2 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        @endif

        <div x-show="showDeleteModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition>

            <div @click.away="showDeleteModal = false"
                class="bg-slate-800 rounded-sm max-w-md w-full p-6 shadow-xl border dark:border-slate-700 text-center whitespace-normal">

                @if ($isLastAdmin)
                    <h3 class="text-lg font-bold mb-4">{{ __('dashboard/index.cannot_delete_last_admin_role') }}</h3>

                    <div class="flex justify-center">
                        <button type="button" @click="showDeleteModal = false"
                            class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors cursor-pointer">
                            {{ __('dashboard/index.cancel') }}
                        </button>
                    </div>
                @else
                    <h3 class="text-lg font-bold mb-4">{{ __('dashboard/index.delete_user_confirm') }}</h3>

                    <form action="{{ route('users.destroy', $user) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <div class="flex justify-center space-x-3">
                            <button type="button" @click="showDeleteModal = false"
                                class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors cursor-pointer">
                                {{ __('dashboard/index.no') }}
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-medium rounded-sm transition-colors cursor-pointer">
                                {{ __('dashboard/index.yes') }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

    </div>
@endif
