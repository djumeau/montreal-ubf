@props(['user'])

<form method="POST" action="{{ route('users.update-role', $user) }}">
    @csrf
    @method('PUT')
    <select name="role" onchange="this.form.submit()"
        class="bg-slate-900 border border-slate-100 rounded-sm text-sm py-1 px-2 text-white focus:outline-none focus:shadow-outline">
        @foreach (\App\Enums\Role::options() as $option)
            <option value="{{ $option['value'] }}" {{ $user->role->value === $option['value'] ? 'selected' : '' }}>
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>
</form>
