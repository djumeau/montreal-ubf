@props(['user', 'adminCount' => 0])

@php
    $isLastAdmin = $user->role === \App\Enums\Role::ADMIN && $adminCount <= 1;
@endphp

<form method="POST" action="{{ route('users.update-role', $user) }}">
    @csrf
    @method('PUT')
    <select name="role" onchange="this.form.submit()" @disabled($isLastAdmin)
        @if ($isLastAdmin) title="{{ __('dashboard/index.cannot_demote_last_admin') }}" @endif
        class="bg-slate-900 border border-slate-100 rounded-sm text-sm py-1 px-2 text-white focus:outline-none focus:shadow-outline disabled:opacity-50 disabled:cursor-not-allowed">
        @foreach (\App\Enums\Role::options() as $option)
            <option value="{{ $option['value'] }}" {{ $user->role->value === $option['value'] ? 'selected' : '' }}>
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>
</form>
