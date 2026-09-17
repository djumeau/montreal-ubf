<div>
    <button type="submit"
        {{ $attributes-> merge([ 'class' => 'w-full px-4 py-2 bg-sky-900 hover:bg-sky-950 text-white font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer']) }}>

        {{ $slot }}

    </button>
</div>
