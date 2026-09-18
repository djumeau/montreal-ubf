<div>
    <button type="submit"
        {{ $attributes-> merge([ 'class' => 'w-full px-4 py-2 bg-sky-900/50 hover:bg-sky-950/50 text-slate-100 font-base rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer']) }}>

        {{ $slot }}

    </button>
</div>
