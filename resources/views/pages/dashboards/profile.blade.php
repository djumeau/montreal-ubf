<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('dashboard/index.title') }}</x-slot>

    <h2 class='text-right text-2xl font-bold pt-18 pb-6'>{{ __('dashboard/index.welcome', ['name' => $user->name]) }}
    </h2>

    <!-- UI - Left sidebar with main area -->
    <div x-data="{
        sidebarOpen: true,
        showAvatarModal: false
    }" class="flex min-h-screen text-white">

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
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="grid place-items-center size-10 pl-2 text-slate-100 hover:text-slate-300 transition-colors focus:outline-none cursor-pointer">
                        <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                    </button>
                </div>

                <x-profile-info-block/>

                <x-dashboard-features></x-dashboard-features>

            </div>

        </aside>

        <!-- Right Column: Interactive Work Space Context -->
        <main :class="sidebarOpen ? 'hidden md:block' : 'block'" class="flex-1 pl-6 overflow-y-auto">

            <!-- UI Segment: Update Profile Form -->
            <div class="w-full border rounded-sm border-slate-100">

                <div
                    x-data="{ showDeleteModal: false }"
                    class="flex flex-row justify-between items-center">

                    <h2 class="p-4 text-lg font-bold mb-2 text-slate-100">{{ __('dashboard/index.update_profile') }}</h2>

                    <button type="button" @click="showDeleteModal = true"
                        class="px-2 py-2 mr-4 bg-red-700 hover:bg-red-800 text-white font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                        {{ __('dashboard/index.delete_user') }}
                    </button>

                    <!-- AlpineJS Modal for Delete User Confirmation -->
                    <div
                        x-show="showDeleteModal" x-cloak
                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
                        x-transition>

                        <div @click.away="showDeleteModal = false"
                            class="bg-slate-800 rounded-sm max-w-md w-full p-6 shadow-xl border text-center whitespace-normal">

                            <h3 class="text-lg font-bold mb-4">{{ __('dashboard/index.delete_user_confirm') }}</h3>

                            <form action="{{ route('profile.destroy') }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <div class="flex justify-center space-x-3">
                                    <button type="button" @click="showDeleteModal = false"
                                        class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors cursor-pointer">
                                        {{ __('dashboard/index.no') }}
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-red-700 hover:bg-red-800 text-slate-100 border font-medium rounded-sm transition-colors cursor-pointer">
                                        {{ __('dashboard/index.yes') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

                <!-- Display Notification - If applicable -->
                @if (session('status'))
                    <div class="flex items-center justify-left ml-4 mb-4">
                        <div class="w-100 p-2 border rounded-sm border-emerald-600 text-emerald-400 text-sm">
                            {{ __(session('status')) }}
                        </div>
                    </div>
                @endif

                <form class="ml-4" action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Trigger for Avatar Upload Modal -->
                    <div class="mb-6 flex items-center justify-center space-x-2">

                        <img src="{{ auth()->user()->avatar_url }}?v={{ time() }}"
                            class="size-16 border-2 rounded-full object-cover">

                        <button type="button" @click="showAvatarModal = true"
                            class="px-2 py-2 bg-sky-900 hover:bg-sky-950 text-white font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
                            <i class="fas fa-camera mr-1"></i>{{ __('dashboard/index.change_avatar') }}
                        </button>

                    </div>

                    <div class="flex flex-col mr-4 items-stretch md:items-center justify-center gap-y-2 p-2 mb-8">

                        <x-inputs.text class="w-full sm:w-50 md:w-100" id="name" name="name"
                            value="{{ auth()->user()->name }}" />

                        <x-inputs.text class="w-full sm:w-50 md:w-100" id="email" name="email" type="email"
                            value="{{ auth()->user()->email }}" />

                        <x-inputs.text class="w-full sm:w-50 md:w-100" id="password" name="password" type="password"
                            placeholder="{{ __('auth/index.password') }}" value="{{ old('password') }}" />

                        <x-inputs.text class="w-full sm:w-50 md:w-100 mb-4" id="password_confirmation" name="password_confirmation"
                            type="password" placeholder="{{ __('auth/index.confirm_password') }}"
                            value="{{ old('password_confirmation') }}" />

                        <x-submit>
                            {{ __('dashboard/index.update') }}
                        </x-submit>

                    </div>

                </form>

            </div>
            <!-- END UI Segment - User Profile -->

        </main>

        <!-- AlpineJS Modal for Image Interception -->
        <!-- 1. Add "avatarPreview: null" to the component state scope -->
        <div x-show="showAvatarModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs transition-opacity"
            x-transition x-data="{ avatarPreview: null }">

            <div @click.away="showAvatarModal = false; avatarPreview = null"
                class="bg-slate-800 rounded-sm max-w-md w-full p-6 shadow-xl border dark:border-slate-700">

                <h3 class="text-lg font-bold mb-4">{{ __('dashboard/index.upload_new_avatar') }}</h3>

                <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- 2. Interactive Drag & Drop / Selection Area -->
                    <div
                        class="border-2 border-dashed border-slate-300 rounded-sm p-6 text-center hover:border-emerald-500 transition-colors relative">

                        <!-- Hidden file input using Alpine change interception -->
                        <input type="file" name="avatar" id="avatar" class="hidden" required accept="image/*"
                            @change="
                                const file = $event.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => { avatarPreview = e.target.result; };
                                    reader.readAsDataURL(file);
                                }
                            ">

                        <label for="avatar"
                            class="cursor-pointer flex flex-col items-center justify-center min-h-35">
                            <!-- State A: No file selected yet (Show upload icon) -->
                            <template x-if="!avatarPreview">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-slate-100 mb-2"></i>
                                    <span class="text-sm font-medium text-slate-100">
                                        {{ __('dashboard/index.select_image') }}
                                    </span>
                                </div>
                            </template>

                            <!-- State B: Image chosen (Show live circular cropping snapshot) -->
                            <template x-if="avatarPreview">
                                <div class="flex flex-col items-center space-y-3">
                                    <img :src="avatarPreview"
                                        class="w-28 h-28 rounded-full object-cover border-4 border-white shadow-md">
                                    <span
                                        class="text-xs text-emerald-600  font-semibold bg-white px-2.5 py-1 rounded-full">
                                        <i class="fas fa-sync-alt mr-1"></i> {{ __('dashboard/index.select_image') }}
                                    </span>
                                </div>
                            </template>
                        </label>
                    </div>

                    <!-- Modal Action Controls -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showAvatarModal = false; avatarPreview = null"
                            class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors cursor-pointer">
                            {{ __('dashboard/index.cancel') }}
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-sky-900/50 text-slate-100 border rounded-sm hover:bg-sky-950/50 transition-colors cursor-pointer">
                            {{ __('dashboard/index.upload') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-layout>
