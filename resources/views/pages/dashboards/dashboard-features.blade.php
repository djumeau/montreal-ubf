@php

    $isProfileActive = request()->routeIs('dashboard') || request()->routeIs('tableau');
    $profileName = __('nav.dashboard.name');

    $isManageUsersActive = request()->routeIs('manage-users') || request()->routeIs('gerer-utilisateurs');
    $manageUsersName = __('nav.manage-users.name');

    $isManageSeriesActive = request()->routeIs('manage-series') || request()->routeIs('gerer-serie');
    $manageSeriesName = __('nav.manage-series.name');

    $isManageStudiesActive = request()->routeIs('manage-studies') || request()->routeIs('gerer-etudes');
    $manageSeriesName = __('nav.manage-studies.name');

@endphp

<!-- Context Dynamic Links -->
<nav id="features">

    <x-feature-button :url="__('nav.update-profile.url')" :isActive="$isProfileActive"
        icon="fa-user-cog">{{ __('nav.update-profile.title') }}</x-feature-button>

    @if (auth()->user()->canManageRoles())
        <x-feature-button :url="__('nav.manage-users.url')" :isActive="$isManageUsersActive"
            icon="fa-users-cog">{{ __('nav.manage-users.title') }}</x-feature-button>

        <x-feature-button :url="__('nav.manage-series.url')" :isActive="$isManageSeriesActive"
            icon="fa-list">{{ __('nav.manage-series.title') }}</x-feature-button>

        <x-feature-button :url="__('nav.manage-studies.url')" :isActive="$isManageStudiesActive"
            icon="fa-bible">{{ __('nav.manage-studies.title') }}</x-feature-button>
    @endif

</nav>
