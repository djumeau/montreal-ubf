<!-- Header & Toggle Chevron Button: reads and flips sidebarOpen of the page's x-data -->
<div class="p-2 flex items-center justify-between border-b-2">
    <span x-show="sidebarOpen" class="font-bold text-lg text-slate-100">
        {{ __('dashboard/index.dashboard') }}
    </span>
    <button @click="sidebarOpen = !sidebarOpen"
        class="grid place-items-center size-10 pl-2 text-slate-100 hover:text-slate-300 transition-colors focus:outline-none cursor-pointer">
        <i class="fas" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
    </button>
</div>
