<!-- Topbar NEXOPS -->
<header class="sticky top-0 z-40 bg-white border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-6">

        <!-- Search Bar -->
        <div class="flex-1 max-w-xl">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text"
                       placeholder="Cari ticket, customer, perangkat, atau serial number..."
                       class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg
                              focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary
                              placeholder-gray-400">
            </div>
        </div>

        <!-- Right Side -->
        <div class="flex items-center gap-2 ml-4">

            <!-- Notification -->
            <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-danger rounded-full"></span>
            </button>

            <!-- Message -->
            <button class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="message-square" class="w-5 h-5"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-primary rounded-full"></span>
            </button>

            <!-- Divider -->
            <div class="w-px h-8 bg-gray-200 mx-2"></div>

            <!-- Profile Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                        class="flex items-center gap-3 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                        <span class="text-primary text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="text-left hidden sm:block">
                        <p class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400">Administrator</p>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 hidden sm:block"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">

                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        Profile
                    </a>

                    <hr class="my-1 border-gray-100">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-danger hover:bg-red-50">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>
