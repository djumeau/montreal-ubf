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
        showAvatarModal: false
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
                    <h2 class="p-4 text-lg font-bold mb-2 text-slate-100">{{ __('dashboard/index.manage_users') }}</h2>

                    <!-- Display Success Notifications -->
                    @if (session('status'))
                        <div class="flex items-center justify-left ml-4 mb-4">
                            <div class="w-100 p-2 border rounded-sm border-emerald-600 text-emerald-400 text-sm">
                                {{ __(session('status')) }}
                            </div>
                        </div>
                    @endif



                </div>

            @endif
            <!-- END UI Segment: Role Management -->

        </main>

    </div>

</x-layout>
