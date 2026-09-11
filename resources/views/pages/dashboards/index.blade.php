@props(['user' => null])

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('dashboard/index.title') }}</x-slot>

    <h2 class='text-right text-2xl font-bold pt-18 pb-6'>{{ __('dashboard/index.welcome', ['name' => $user->name]) }}
    </h2>

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
                        {{ __('Dashboard') }}
                    </span>
                    <button @click="sidebarOpen = !sidebarOpen" class="grid place-items-center size-10 pl-2 text-slate-100 hover:text-slate-300 transition-colors focus:outline-none cursor-pointer">
                        <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                    </button>
                </div>

                <!-- Profile Info Block -->
                <div class="p-4 flex flex-col items-center text-center overflow-hidden">
                    <img src="{{ auth()->user()->avatar_url }}"
                        alt="Avatar"
                        :class="sidebarOpen ? 'w-20 h-20' : 'w-10 h-10'"
                        class="rounded-full object-cover border-4 border-slate-100 shadow-sm transition-all duration-300">

                    <div x-show="sidebarOpen" class="mt-3 transition-opacity duration-300">
                        <h3 class="font-semibold text-base leading-tight truncate max-w-[200px]">{{ auth()->user()->name }}</h3>
                        <p class="text-xs text-slate-100 truncate max-w-[200px] mb-2">{{ auth()->user()->email }}</p>
                        <span class="inline-block border-2 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full bg-black text-slate-100">
                            {{ auth()->user()->role->value }}
                        </span>
                    </div>
                </div>

                <!-- Context Dynamic Links -->
                <nav class="space-y-1">
                    <button @click="activeTab = 'profile'"
                            :class="activeTab === 'profile' ? 'bg-slate-100 text-slate-900 font-medium border-b border-t border-slate-100' : 'text-slate-100'"
                            class="w-full flex items-center p-3 transition-colors focus:outline-none cursor-pointer">
                        <i class="fas fa-user-cog w-6 text-center"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm">{{ __('dashboard/index.update_profile') }}</span>
                    </button>

                    <!-- Placeholder for future modules (e.g. Schedule, Ministries) -->
                    <button @click="activeTab = 'ministries'"
                            :class="activeTab === 'ministries' ? 'bg-slate-100 text-slate-900 font-medium border-b border-t border-slate-100' : 'text-slate-100'"
                            class="w-full flex items-center p-3 transition-colors focus:outline-none cursor-pointer">
                        <i class="fas fa-church w-6 text-center"></i>
                        <span x-show="sidebarOpen" class="ml-3 text-sm">{{ __('Ministries') }}</span>
                    </button>
                </nav>
            </div>
        </aside>

        <!-- Right Column: Interactive Work Space Context -->
        <main
            :class="sidebarOpen ? 'hidden md:block' : 'block'"
            class="flex-1 pl-6 overflow-y-auto">

            <!-- UI Segment: Update Profile Form -->
            <div x-show="activeTab === 'profile'" x-cloak class="w-full border rounded-sm border-slate-100">
                <h2 class="p-4 text-lg font-bold mb-2 text-slate-100">{{ __('dashboard/index.update_profile') }}</h2>

                <!-- Display Success Notifications -->
                @if (session('status'))
                    <div class="w-full mb-4 p-4 border rounded-sm border-slate-100 text-emerald-400 text-sm">
                        {{ __(session('status')) }}
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Trigger for Avatar Upload Modal -->
                    <div class="ml-4 mb-6 flex items-center space-x-2">

                        <img src="{{ auth()->user()->avatar_url }}" class="size-16 rounded-full object-cover border-4">

                        <button type="button" @click="showAvatarModal = true" class="px-2 py-2 text-sm  bg-sky-900/50 text-slate-100  border rounded-sm hover:bg-sky-950/50 transition-colors cursor-pointer">
                            <i class="fas fa-camera mr-2"></i>{{ __('dashboard/index.change_avatar') }}
                        </button>

                    </div>

                    <!-- Input Fields Grid -->
                    <div class="space-y-4">

                        <x-inputs.text id="name" name="name" value="{{ auth()->user()->name }}" />

                        <x-inputs.text id="email" name="email" type="email" value="{{ auth()->user()->email }}" />

                        <div class="grid grid-cols-1 gap-y-4 md:grid-cols-2 md:gap-y-0">

                            <x-inputs.text id="password" name="password" type="password" placeholder="{{ __('auth/index.password') }}" value="{{ old('password') }}" />

                            <x-inputs.text id="password_confirmation" name="password_confirmation" type="password" placeholder="{{ __('auth/index.confirm_password') }}" value="{{ old('password_confirmation') }}" />

                        </div>
                    </div>

                    <div class="m-4 flex justify-end">

                        <button type="submit"
                            class="p-2 bg-sky-900/50 text-slate-100  border rounded-sm hover:bg-sky-950/50 transition-colors cursor-pointer">{{ __('dashboard/index.update') }}
                        </button>

                    </div>

                </form>

            </div>

        </main>

        <!-- AlpineJS Modal for Image Interception -->
        <div x-show="showAvatarModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity" x-transition>

            <div @click.away="showAvatarModal = false" class="bg-white rounded-md w-full p-6 shadow-xl border border-slate-100">

                <h3 class="text-lg font-bold mb-4 text-black">{{ __('dashboard/index.upload_new_avatar') }}</h3>

                <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center hover:border-blue-900 transition-colors">
                        <input type="file" name="avatar" id="avatar" class="hidden" required accept="image/*" @change="/* optional image preview logic */">
                        <label for="avatar" class="cursor-pointer flex flex-col items-center">
                            <i class="fas fa-cloud-upload-alt text-3xl text-slate-400 mb-2"></i>
                            <span class="text-sm font-medium text-slate-400 hover:text-slate-700">{{ __('dashboard/index.browse_files') }}</span>
                        </label>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showAvatarModal = false" class="px-4 py-2 border rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                            class="p-2 bg-sky-900/50 text-slate-100  border rounded-sm hover:bg-sky-950/50 transition-colors cursor-pointer">{{ __('dashboard/index.upload') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-layout>
