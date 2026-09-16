@props(['user'])

<form method="POST" action="{{ route('users.reset-password', $user) }}"
    onsubmit="return confirm({{ \Illuminate\Support\Js::from(__('dashboard/index.reset_password_confirm')) }})">
    @csrf
    <button type="submit"
        class="px-3 py-1.5 bg-sky-900 hover:bg-sky-950 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer whitespace-nowrap">
        {{ __('dashboard/index.reset_password') }}
    </button>
</form>
