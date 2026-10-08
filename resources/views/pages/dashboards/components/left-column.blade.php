<!-- Left Column: Collapsible Sidebar (follows sidebarOpen of the page's x-data) -->
<aside
    :class="{
        'w-full block': sidebarOpen,
        'md:block md:w-16': !sidebarOpen,
        'md:w-64': sidebarOpen && window.innerWidth >= 768
    }"
    class="transition-all duration-300 ease-in-out border rounded-sm border-slate-100 flex flex-col justify-between">

    <div>
        <x-dashboards::toggle-chevron />

        <x-profile-info-block />

        <x-dashboard-features></x-dashboard-features>
    </div>

</aside>
