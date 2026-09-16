@props(['user' => null])

<x-layout class="bg-slate-900" textColor="text-white">

    <!-- dashboard/index.blade.php -->

    <x-slot name="title">{{ __('dashboard/index.title') }}</x-slot>

    <h2 class='text-right text-2xl font-bold pt-18 pb-6'>{{ __('dashboard/index.welcome', ['name' => $user->name]) }}
    </h2>

    <!-- UI - Left sidebar with main area -->
    <div x-data="{
        sidebarOpen: true,
        activeTab: 'profile',
        showAvatarModal: false,
        showAddUserModal: {{ $errors->has('name') || $errors->has('email') ? 'true' : 'false' }}
        }"
        class="flex min-h-screen text-white">

        <!-- Left Column: Collapsible Sidebar -->
        <aside
            :class="{
                'w-full block': sidebarOpen,
                'md:block md:w-16': !sidebarOpen,
                'md:w-64': sidebarOpen && window.innerWidth >= 768
            }"

            class="transition-all duration-300 ease-in-out border rounded-sm border-slate-100 flex flex-col justify-between">
            <div>
                <!-- Header & Toggle Chevron Button -->
                <div class="p-2 flex items-center justify-between border-b-2">
                    <span x-show="sidebarOpen" class="font-bold text-lg text-slate-100">
                        {{ __('dashboard/index.dashboard') }}
                    </span>
                    <button @click="sidebarOpen = !sidebarOpen" class="grid place-items-center size-10 pl-2 text-slate-100 hover:text-slate-300 transition-colors focus:outline-none cursor-pointer">
                        <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                    </button>
                </div>

                <x-profile-info-block/>

                <x-dashboard-features></x-dashboard-features>

            </div>

        </aside>

        <!-- Right Column: Interactive Work Space Context -->
        <main
            :class="sidebarOpen ? 'hidden md:block' : 'block'"
            class="flex-1 pl-6 overflow-y-auto">

            <!-- UI Segment: Role Management -->
            @if(auth()->user()->canManageRoles())

                <div class="w-full border rounded-sm border-slate-100">

                    <div class="p-4 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-100">{{ __('dashboard/index.manage_users') }}</h2>

                        <button type="button" @click="showAddUserModal = true"
                            class="px-4 py-2 bg-sky-900 hover:bg-sky-950 text-white font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                            <i class="fas fa-user-cog mr-1"></i>{{ __('dashboard/index.add_user') }}
                        </button>
                    </div>

                    <!-- Display Success Notifications -->
                    @if (session('status'))
                        <div class="flex items-center justify-left ml-4 mb-4">
                            <div class="w-fit p-2 border rounded-sm border-emerald-600 text-emerald-400 text-sm">
                                {{ __(session('status')) }}
                            </div>
                        </div>
                    @endif

                    <!-- List paginated users (10) -->

                    <!-- Desktop: Table Layout -->
                    <div class="hidden md:block overflow-x-auto px-4 pb-4">

                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-300 uppercase text-xs tracking-wider">
                                    <th class="py-2 pr-4">{{ __('dashboard/index.name') }}</th>
                                    <th class="py-2 pr-4">{{ __('dashboard/index.email') }}</th>
                                    <th class="py-2 pr-4">{{ __('dashboard/index.role') }}</th>
                                    <th class="py-2 pr-4 text-right">{{ __('dashboard/index.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $listedUser)
                                    <tr class="border-b border-slate-800">
                                        <td class="py-3 pr-4">{{ $listedUser->name }}</td>
                                        <td class="py-3 pr-4 text-slate-300">{{ $listedUser->email }}</td>
                                        <td class="py-3 pr-4">
                                            <x-manage-users.role-select :user="$listedUser" />
                                        </td>
                                        <td class="py-3 pr-4 text-right">
                                            <x-manage-users.reset-password-button :user="$listedUser" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile: Stacked Card Layout -->
                    <div class="md:hidden px-4 pb-4 space-y-3">
                        @foreach ($users as $listedUser)
                            <div class="border border-slate-800 rounded-sm p-3">
                                <div class="font-bold text-slate-100">{{ $listedUser->name }}</div>
                                <div class="text-slate-300 text-sm mb-3">{{ $listedUser->email }}</div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <x-manage-users.role-select :user="$listedUser" />
                                    <x-manage-users.reset-password-button :user="$listedUser" />
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($users->hasPages())
                        <div class="px-4 pb-4 mx-4 mb-4 bg-white text-slate-900 rounded-sm">
                            {{ $users->links() }}
                        </div>
                    @endif

                </div>

            @endif
            <!-- END UI Segment: Role Management -->

        </main>

        <!-- AlpineJS Modal for Adding a New User -->
        <div x-show="showAddUserModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition>

            <div @click.away="showAddUserModal = false"
                class="bg-slate-800 rounded-sm max-w-md w-full p-6 shadow-xl border dark:border-slate-700">

                <h3 class="text-lg font-bold mb-4">{{ __('dashboard/index.add_new_user') }}</h3>

                <form class="w-full" action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <!-- New users start with the default avatar; they can change it once they log in -->
                    <div class="mb-6 flex items-center justify-center">
                        <img src="{{ \App\Models\User::defaultAvatarUrl() }}" class="size-16 border-2 rounded-full object-cover">
                    </div>

                    <div class="flex flex-col items-stretch justify-center gap-y-2 mb-4">
                        <x-inputs.text class="w-full" id="name" name="name" placeholder="{{ __('dashboard/index.name') }}"
                            value="{{ old('name') }}" />

                        <x-inputs.text class="w-full" id="email" name="email" type="email" placeholder="{{ __('dashboard/index.email') }}"
                            value="{{ old('email') }}" />
                    </div>

                    <!-- Modal Action Controls -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showAddUserModal = false"
                            class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors cursor-pointer">
                            {{ __('dashboard/index.cancel') }}
                        </button>

                        <x-submit>
                            {{ __('dashboard/index.create') }}
                        </x-submit>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-layout>
