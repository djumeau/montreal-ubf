<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('dashboard/index.title') }}</x-slot>

    <h2 class='text-right text-2xl font-bold pt-20 pb-6'>{{ __('dashboard/index.welcome', ['name' => $user->name]) }}
    </h2>

    <!-- UI - Left sidebar with main area -->
    <div x-data="{ sidebarOpen: true }" class="flex min-h-screen text-white">

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

                <x-profile-info-block />

                <x-dashboard-features></x-dashboard-features>
            </div>

        </aside>

        <!-- Right Column: Interactive Work Space Context -->
        <main :class="sidebarOpen ? 'hidden md:block' : 'block'" class="flex-1 pl-6 overflow-y-auto">

            <!-- UI Segment: Manage Study Schedule -->
            @if (auth()->user()->canManageRoles())
                <div class="w-full border rounded-sm border-slate-100">

                    <div class="p-4 flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-100">{{ __('dashboard/index.manage_schedule') }}</h2>
                    </div>

                    <!-- Display Success Notifications -->
                    @if (session('status'))
                        <div class="flex items-center justify-left ml-4 mb-4">
                            <div class="w-fit p-2 border rounded-sm border-emerald-600 text-emerald-400 text-sm">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif

                    <!-- Period and view toolbar (links stay on this page) -->
                    <x-study-schedule::filter-panel :view="$view" :start="$start" :previous="$previous"
                        :next="$next" :range-label="$rangeLabel" :overlap="false" class="mx-4 mb-4" />

                    @if ($view === 'week')
                        <!-- Week Grid (desktop): Sunday to Saturday header, then the hour rows -->
                        <div class="hidden md:block m-4 border border-slate-700 rounded-sm overflow-hidden">
                            <table class="w-full table-fixed text-sm">
                                <x-study-schedule::week-head :start="$start" />
                                <x-study-schedule::hour-rows :start="$start" :studies="$studies" />
                            </table>
                        </div>
                    @endif

                    <!-- Month and List views, mobile day list and the Bible study modal: content to come -->

                </div>
            @endif

            <!-- END UI Segment: Manage Study Schedule -->

        </main>

    </div>

</x-layout>
