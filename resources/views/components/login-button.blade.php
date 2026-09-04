@props([
    'icon' => null,
    'isMobile' => false,
])

<a href="{{ route(__('nav.login.name')) }}"
    class="bg-sky-900 justify-right hover:bg-sky-950 text-white font-bold py-2 px-4 rounded
    outline-1 outline-white hover:outline-2 focus:shadow-outline">
    <i class="fa fa-user p-0 mr-1"></i>
    {{ __('nav.login.title') }}
</a>
