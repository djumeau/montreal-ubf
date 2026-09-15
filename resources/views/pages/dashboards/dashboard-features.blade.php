@php

    $isProfileActive = request()->routeIs('dashboard') || request()->routeIs('tableau');
    $profileName = __('nav.dashboard.name');

    $isManageUsersActive = request()->routeIs('manage-users') || request()->routeIs('gerer-utilisateurs');
    $manageUsersName = __('nav.manage-users.name');

@endphp

<!-- Context Dynamic Links -->
<nav id="features">

    <x-feature-button :url="__('nav.update-profile.url')" :isActive="$isProfileActive"
        icon="fa-user-cog">{{ __('nav.update-profile.title') }}</x-feature-button>

    @if (auth()->user()->canManageRoles())
        <x-feature-button :url="__('nav.manage-users.url')" :isActive="$isManageUsersActive"
            icon="fa-users-cog">{{ __('nav.manage-users.title') }}</x-feature-button>
    @endif

</nav>
