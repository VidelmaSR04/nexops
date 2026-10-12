<!-- Sidebar NEXOPS (versi putih) -->
<aside class="fixed top-0 left-0 h-screen w-60 bg-white border-r border-gray-100 flex flex-col z-50">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
        <div class="w-9 h-9 bg-primary rounded-lg flex items-center justify-center">
            <i data-lucide="radio-tower" class="w-5 h-5 text-white"></i>
        </div>
        <div>
            <span class="text-dark font-bold text-lg tracking-wide">NEXOPS</span>
            <p class="text-gray-400 text-[10px] -mt-1">Operations Management</p>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-dark' }}">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            Dashboard
        </a>

        <a href="{{ route('tickets.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('tickets.*') ? 'bg-primary text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-dark' }}">
            <i data-lucide="ticket" class="w-5 h-5"></i>
            Tickets
        </a>

                <a href="{{ route('inventory.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('inventory.*') ? 'bg-primary text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-dark' }}">
            <i data-lucide="package" class="w-5 h-5"></i>
            Inventory
        </a>

        <a href="{{ route('assets.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('assets.*') ? 'bg-primary text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-dark' }}">
            <i data-lucide="monitor" class="w-5 h-5"></i>
            Assets
        </a>

        <a href="{{ route('warehouse.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('warehouse.*') ? 'bg-primary text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-dark' }}">
            <i data-lucide="warehouse" class="w-5 h-5"></i>
            Warehouse
        </a>

        <a href="{{ route('technicians.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('technicians.*') ? 'bg-primary text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-dark' }}">
            <i data-lucide="hard-hat" class="w-5 h-5"></i>
            Technicians
        </a>

        <a href="{{ route('customers.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('customers.*') ? 'bg-primary text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-dark' }}">
            <i data-lucide="users" class="w-5 h-5"></i>
            Customers
        </a>

        <a href="{{ route('reports.index') }}"
   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-colors {{ request()->routeIs('reports.index') ? 'bg-primary text-white shadow-sm' : 'text-secondary hover:bg-gray-100 hover:text-dark' }}">
    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
    <span>Reports</span>
</a>

        <a href="{{ route('settings.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('settings.*') ? 'bg-primary text-white' : 'text-gray-500 hover:bg-gray-50 hover:text-dark' }}">
            <i data-lucide="settings" class="w-5 h-5"></i>
            Settings
        </a>

    </nav>

    <!-- User Info di bawah sidebar -->
    <div class="px-4 py-4 border-t border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center">
                <span class="text-primary text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-dark text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                <p class="text-gray-400 text-xs">Administrator</p>
            </div>
        </div>
    </div>

</aside>
