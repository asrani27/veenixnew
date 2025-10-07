<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-56 bg-white/10 backdrop-blur-lg shadow-lg border-r border-white/20 transform -translate-x-full lg:relative lg:translate-x-0 transition-all duration-300 ease-in-out">
    <div class="flex flex-col h-full">
        <!-- Logo/Brand -->
        <div class="flex items-center justify-center h-12 px-3 bg-purple-600/80 backdrop-blur-sm">
            <h1 class="text-white text-lg font-semibold">Admin Panel</h1>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-1">
            <a href="/admin/dashboard"
                class="flex items-center px-3 py-2 {{ request()->is('admin/dashboard') ? 'text-white bg-white/20 backdrop-blur-sm rounded-md border border-white/30' : 'text-white/80 hover:bg-white/10 hover:text-white' }} rounded-md transition-all duration-300 border {{ request()->is('admin/dashboard') ? 'border-white/30' : 'border-transparent hover:border-white/20' }}">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span class="text-sm">Dashboard</span>
            </a>

            <a href="/admin/movie"
                class="flex items-center px-3 py-2 {{ request()->is('admin/movie*') ? 'text-white bg-white/20 backdrop-blur-sm rounded-md border border-white/30' : 'text-white/80 hover:bg-white/10 hover:text-white' }} rounded-md transition-all duration-300 border {{ request()->is('admin/movie*') ? 'border-white/30' : 'border-transparent hover:border-white/20' }}">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a2 2 0 00-1-1H4a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                <span class="text-sm">Movie</span>
            </a>

            <a href="/admin/tv"
                class="flex items-center px-3 py-2 {{ request()->is('admin/tv*') ? 'text-white bg-white/20 backdrop-blur-sm rounded-md border border-white/30' : 'text-white/80 hover:bg-white/10 hover:text-white' }} rounded-md transition-all duration-300 border {{ request()->is('admin/tv*') ? 'border-white/30' : 'border-transparent hover:border-white/20' }}">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                <span class="text-sm">TV</span>
            </a>

            <a href="/admin/reports"
                class="flex items-center px-3 py-2 {{ request()->is('admin/reports*') ? 'text-white bg-white/20 backdrop-blur-sm rounded-md border border-white/30' : 'text-white/80 hover:bg-white/10 hover:text-white' }} rounded-md transition-all duration-300 border {{ request()->is('admin/reports*') ? 'border-white/30' : 'border-transparent hover:border-white/20' }}">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z">
                    </path>
                </svg>
                <span class="text-sm">Reports</span>
            </a>

            <a href="/admin/api-settings"
                class="flex items-center px-3 py-2 {{ request()->is('admin/api-settings*') ? 'text-white bg-white/20 backdrop-blur-sm rounded-md border border-white/30' : 'text-white/80 hover:bg-white/10 hover:text-white' }} rounded-md transition-all duration-300 border {{ request()->is('admin/api-settings*') ? 'border-white/30' : 'border-transparent hover:border-white/20' }}">
                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1 1 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                    </path>
                </svg>
                <span class="text-sm">API Settings</span>
            </a>
        </nav>

        <!-- Profile Menu -->
        <div class="px-3 py-3 border-t border-white/20">
            <div class="relative">
                <button onclick="toggleProfileMenu()"
                    class="flex items-center w-full px-3 py-2 text-white/80 hover:bg-white/10 hover:text-white rounded-md transition-all duration-300 border border-transparent hover:border-white/20">
                    <img src="https://ui-avatars.com/api/?name=User&background=8b5cf6&color=fff" alt="Profile"
                        class="w-6 h-6 rounded-full mr-3">
                    <div class="flex-1 text-left">
                        <p class="text-xs font-medium text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-white/60">Administrator</p>
                    </div>
                    <svg class="w-3 h-3 transform transition-transform text-white/60" id="profileMenuIcon" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="profileMenu"
                    class="absolute bottom-full left-0 right-0 mb-2 bg-white/20 backdrop-blur-lg rounded-md shadow-lg border border-white/30 hidden">
                    <a href="{{ route('settings.profile') }}"
                        class="flex items-center px-3 py-2 text-white hover:bg-white/10 rounded-t-md transition-colors">
                        <svg class="w-3 h-3 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1 1 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-xs">Settings</span>
                    </a>
                    <form action="{{ route('logout') }}" method="GET" class="block" onsubmit="return confirm('Are you sure you want to logout?');">
                        @csrf
                        <button type="submit"
                            class="flex items-center w-full px-3 py-2 text-white hover:bg-white/10 rounded-b-md transition-colors">
                            <svg class="w-3 h-3 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span class="text-xs">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
