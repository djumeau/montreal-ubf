<x-layout class="bg-slate-900" textColor="text-white">

    <!-- dashboards/manage-home-page.blade.php -->

    <x-slot name="title">{{ __('header.name') }} - {{ __('dashboard/index.title') }}</x-slot>

    <h2 class='text-right text-2xl font-bold pt-18 pb-6'>{{ __('dashboard/index.welcome', ['name' => $user->name]) }}
    </h2>

    <!-- UI - Left sidebar with main area -->
    <div x-data="{ sidebarOpen: true }" class="flex min-h-screen text-white">

        <x-dashboards::left-column />

        <!-- Right Column: Interactive Work Space Context -->
        <main :class="sidebarOpen ? 'hidden md:block' : 'block'" class="flex-1 pl-6 overflow-y-auto">

            <!-- UI Segment: Manage Home Page -->
            @if (auth()->user()->canManageRoles())
                <div class="w-full border rounded-sm border-slate-100">

                    <div class="p-4">
                        <h2 class="text-lg font-bold text-slate-100">{{ __('dashboard/manage-home-page/index.manage_home_page') }}</h2>
                    </div>

                    <!-- Display Success Notifications -->
                    @if (session('status'))
                        <div class="flex items-center justify-left ml-4 mb-4">
                            <div class="w-fit p-2 border rounded-sm border-emerald-600 text-emerald-400 text-sm">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif

                </div>
            @endif
            <!-- END UI Segment: Manage Home Page -->

        </main>

    </div>

</x-layout>
