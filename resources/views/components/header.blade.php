<!-- Header -->
<header class="bg-blue-900 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-3xl font-semibold">
            <a href="{{ route('home') }}">Conférence d'été 2026 - Montréal</a>
        </h1>
        <nav class="hidden md:flex items-center space-x-4">
            <a href="{{ route('about') }}" class="text-white hover:underline py-2">About </a> |
            <a href="{{ route('events') }}" class="text-white hover:underline py-2">Events </a> |
            <a href="dashboard.html" class="text-white hover:underline py-2">
                <i class="fa fa-gauge mr-1"></i>Admin
            </a>
        </nav>
        <button id="hamburger" class="text-white md:hidden flex items-center">
            <i class="fa fa-bars text-2xl"></i>
        </button>
    </div>
    <!-- Mobile Menu -->
    <nav id="mobile-menu" class="hidden md:hidden bg-blue-900 text-white mt-5 pb-4 space-y-2">
        <a href="saved-jobs.html" class="block px-4 py-2 hover:bg-blue-700">About</a>
        <a href="login.html" class="block px-4 py-2 hover:bg-blue-700">Events</a>
        <a href="register.html" class="block px-4 py-2 hover:bg-blue-700">Admin</a>
    </nav>
</header>
