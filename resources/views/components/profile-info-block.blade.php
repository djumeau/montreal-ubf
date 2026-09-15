<!-- Profile Info Block -->
<div class="p-4 flex flex-col items-center text-center overflow-hidden">
    <!-- {{ auth()->user()->avatar_url }} -->
    <img src="{{ auth()->user()->avatar_url }}?v={{ time() }}" alt="Avatar"
        :class="sidebarOpen ? 'size-20' : 'size-10'"
        class="rounded-full object-cover border-2 border-white shadow-xs transition-all duration-300">


    <div x-show="sidebarOpen" class="mt-3 transition-opacity duration-300">
        <h3 class="font-semibold text-base leading-tight truncate max-w-50">{{ auth()->user()->name }}
        </h3>
        <p class="text-xs text-slate-100 truncate max-w-50 mb-2">{{ auth()->user()->email }}</p>
        <span
            class="inline-block border-2 px-2 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-black text-slate-100">
            {{ auth()->user()->role->value }}
        </span>
    </div>
</div>
<!-- End of Profile Block -->
