<form method="POST" action="{{ route(__('nav.logout.name')) }}">
    @csrf
    <button type="submit"
        class="bg-sky-900/50 justify-right hover:bg-sky-950/50 text-white font-bold py-2 px-4 rounded
        outline-1 outline-white hover:outline-2 focus:shadow-outline">
        <i class="fa fa-sign-out p-0 mr-1"></i>
        {{ __('nav.logout.title') }}
    </button>
</form>
