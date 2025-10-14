<!-- Sidebar Navigation -->
<aside id="sidebar" class="fixed left-0 top-0 z-40 h-screen transition-all duration-300 {{ $sidebarOpen ? 'translate-x-0' : '-translate-x-full' }} {{ $sidebarCollapsed ? 'w-20' : 'w-64' }}" 
     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="h-full px-3 py-4 overflow-y-auto">
        <!-- Logo Section -->
        <div class="flex items-center justify-between mb-8 px-4">
            <div class="flex items-center space-x-3 {{ $sidebarCollapsed ? 'justify-center' : '' }}">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                    <i class="fas fa-headset text-purple-600 text-xl"></i>
                </div>
                @if(!$sidebarCollapsed)
                    <div class="text-white">
                        <h1 class="text-xl font-bold">HelpDesk</h1>
                        <p class="text-xs text-purple-200">Admin Panel</p>
                    </div>
                @endif
            </div>
            <div class="flex items-center space-x-2">
                <!-- Bouton pour réduire/étendre -->
                <button wire:click="toggleSidebarCollapse" 
                        class="text-white hover:bg-white/20 p-2 rounded-lg transition-colors hidden lg:flex"
                        title="{{ $sidebarCollapsed ? 'Étendre le menu' : 'Réduire le menu' }}">
                    <i class="fas {{ $sidebarCollapsed ? 'fa-angle-right' : 'fa-angle-left' }}"></i>
                </button>
                <!-- Bouton pour fermer (mobile) -->
                <button wire:click="toggleSidebar" 
                        class="text-white hover:bg-white/20 p-2 rounded-lg transition-colors lg:hidden">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- User Profile Section -->
        <div class="mb-6 px-4">
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                <div class="flex items-center {{ $sidebarCollapsed ? 'justify-center' : 'space-x-3' }}">
                    <div class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-bold shadow-lg flex-shrink-0">
                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                    </div>
                    @if(!$sidebarCollapsed)
                        <div class="flex-1">
                            <p class="text-white font-semibold text-sm">{{ $user->name ?? 'Admin User' }}</p>
                            <p class="text-purple-200 text-xs">{{ $user->email ?? 'admin@example.com' }}</p>
                        </div>
                    @endif
                </div>
                @if(!$sidebarCollapsed)
                    <div class="mt-3 pt-3 border-t border-white/20">
                        <div class="flex justify-between text-xs text-purple-200">
                            <span>Role: Administrator</span>
                            <span>Online</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="space-y-2 px-4">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="flex items-center {{ $sidebarCollapsed ? 'justify-center' : 'space-x-3' }} text-white hover:bg-white/20 p-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-white/20 shadow-lg' : '' }}"
               title="{{ $sidebarCollapsed ? 'Dashboard' : '' }}">
                <i class="fas fa-home w-5 text-center group-hover:scale-110 transition-transform"></i>
                @if(!$sidebarCollapsed)
                    <span class="font-medium">Dashboard</span>
                    @if(request()->routeIs('dashboard'))
                        <div class="ml-auto w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></div>
                    @endif
                @endif
            </a>

            <!-- Tickets -->
            <div class="space-y-1">
                <a href="{{ route('tickets') }}" 
                   class="flex items-center {{ $sidebarCollapsed ? 'justify-center' : 'space-x-3' }} text-white hover:bg-white/20 p-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('tickets*') ? 'bg-white/20 shadow-lg' : '' }}"
                   title="{{ $sidebarCollapsed ? 'Tickets' : '' }}">
                    <i class="fas fa-ticket-alt w-5 text-center group-hover:scale-110 transition-transform"></i>
                    @if(!$sidebarCollapsed)
                        <span class="font-medium">Tickets</span>
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">12</span>
                    @endif
                </a>
                @if(!$sidebarCollapsed)
                    <div class="ml-8 space-y-1">
                        <a href="{{ route('tickets.create') }}" 
                           class="flex items-center space-x-2 text-purple-200 hover:text-white hover:bg-white/10 p-2 rounded-lg text-sm transition-all duration-200">
                            <i class="fas fa-plus w-4 text-center"></i>
                            <span>New Ticket</span>
                        </a>
                        <a href="#" class="flex items-center space-x-2 text-purple-200 hover:text-white hover:bg-white/10 p-2 rounded-lg text-sm transition-all duration-200">
                            <i class="fas fa-list w-4 text-center"></i>
                            <span>All Tickets</span>
                        </a>
                        <a href="#" class="flex items-center space-x-2 text-purple-200 hover:text-white hover:bg-white/10 p-2 rounded-lg text-sm transition-all duration-200">
                            <i class="fas fa-clock w-4 text-center"></i>
                            <span>Recent</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Projects -->
            <div class="space-y-1">
                <a href="{{ route('projects') }}" 
                   class="flex items-center {{ $sidebarCollapsed ? 'justify-center' : 'space-x-3' }} text-white hover:bg-white/20 p-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('projects*') ? 'bg-white/20 shadow-lg' : '' }}"
                   title="{{ $sidebarCollapsed ? 'Projects' : '' }}">
                    <i class="fas fa-project-diagram w-5 text-center group-hover:scale-110 transition-transform"></i>
                    @if(!$sidebarCollapsed)
                        <span class="font-medium">Projects</span>
                    @endif
                </a>
                @if(!$sidebarCollapsed)
                    <div class="ml-8 space-y-1">
                        <a href="{{ route('projects.create') }}" 
                           class="flex items-center space-x-2 text-purple-200 hover:text-white hover:bg-white/10 p-2 rounded-lg text-sm transition-all duration-200">
                            <i class="fas fa-plus w-4 text-center"></i>
                            <span>New Project</span>
                        </a>
                        <a href="#" class="flex items-center space-x-2 text-purple-200 hover:text-white hover:bg-white/10 p-2 rounded-lg text-sm transition-all duration-200">
                            <i class="fas fa-star w-4 text-center"></i>
                            <span>Favorites</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Companies -->
            <a href="{{ route('companies') }}" 
               class="flex items-center {{ $sidebarCollapsed ? 'justify-center' : 'space-x-3' }} text-white hover:bg-white/20 p-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('companies*') ? 'bg-white/20 shadow-lg' : '' }}"
               title="{{ $sidebarCollapsed ? 'Companies' : '' }}">
                <i class="fas fa-building w-5 text-center group-hover:scale-110 transition-transform"></i>
                @if(!$sidebarCollapsed)
                    <span class="font-medium">Companies</span>
                @endif
            </a>

            <!-- Users -->
            <a href="#" 
               class="flex items-center {{ $sidebarCollapsed ? 'justify-center' : 'space-x-3' }} text-white hover:bg-white/20 p-3 rounded-xl transition-all duration-200 group"
               title="{{ $sidebarCollapsed ? 'Users' : '' }}">
                <i class="fas fa-users w-5 text-center group-hover:scale-110 transition-transform"></i>
                @if(!$sidebarCollapsed)
                    <span class="font-medium">Users</span>
                @endif
            </a>

            <!-- Reports -->
            <a href="#" 
               class="flex items-center {{ $sidebarCollapsed ? 'justify-center' : 'space-x-3' }} text-white hover:bg-white/20 p-3 rounded-xl transition-all duration-200 group"
               title="{{ $sidebarCollapsed ? 'Reports' : '' }}">
                <i class="fas fa-chart-bar w-5 text-center group-hover:scale-110 transition-transform"></i>
                @if(!$sidebarCollapsed)
                    <span class="font-medium">Reports</span>
                @endif
            </a>

            <!-- Settings -->
            <div class="space-y-1">
                <a href="#" 
                   class="flex items-center {{ $sidebarCollapsed ? 'justify-center' : 'space-x-3' }} text-white hover:bg-white/20 p-3 rounded-xl transition-all duration-200 group"
                   title="{{ $sidebarCollapsed ? 'Settings' : '' }}">
                    <i class="fas fa-cog w-5 text-center group-hover:scale-110 transition-transform"></i>
                    @if(!$sidebarCollapsed)
                        <span class="font-medium">Settings</span>
                    @endif
                </a>
                @if(!$sidebarCollapsed)
                    <div class="ml-8 space-y-1">
                        <a href="#" class="flex items-center space-x-2 text-purple-200 hover:text-white hover:bg-white/10 p-2 rounded-lg text-sm transition-all duration-200">
                            <i class="fas fa-user-cog w-4 text-center"></i>
                            <span>Profile</span>
                        </a>
                        <a href="#" class="flex items-center space-x-2 text-purple-200 hover:text-white hover:bg-white/10 p-2 rounded-lg text-sm transition-all duration-200">
                            <i class="fas fa-bell w-4 text-center"></i>
                            <span>Notifications</span>
                        </a>
                        <a href="#" class="flex items-center space-x-2 text-purple-200 hover:text-white hover:bg-white/10 p-2 rounded-lg text-sm transition-all duration-200">
                            <i class="fas fa-shield-alt w-4 text-center"></i>
                            <span>Security</span>
                        </a>
                    </div>
                @endif
            </div>
        </nav>

        <!-- Bottom Actions -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/20">
            <div class="space-y-2">
                <a href="#" 
                   class="flex items-center {{ $sidebarCollapsed ? 'justify-center' : 'space-x-3' }} text-purple-200 hover:text-white hover:bg-white/10 p-3 rounded-xl transition-all duration-200"
                   title="{{ $sidebarCollapsed ? 'Help & Support' : '' }}">
                    <i class="fas fa-question-circle w-5 text-center"></i>
                    @if(!$sidebarCollapsed)
                        <span class="font-medium text-sm">Help & Support</span>
                    @endif
                </a>
                <button wire:click="logout" 
                        class="w-full flex items-center {{ $sidebarCollapsed ? 'justify-center' : 'space-x-3' }} text-red-300 hover:text-white hover:bg-red-500/20 p-3 rounded-xl transition-all duration-200"
                        title="{{ $sidebarCollapsed ? 'Logout' : '' }}">
                    <i class="fas fa-sign-out-alt w-5 text-center"></i>
                    @if(!$sidebarCollapsed)
                        <span class="font-medium text-sm">Logout</span>
                    @endif
                </button>
            </div>
        </div>
    </div>
</aside>

<!-- Top Navigation Bar -->
<nav class="fixed top-0 left-0 right-0 z-30 bg-white border-b border-gray-200 shadow-sm">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Left Section -->
            <div class="flex items-center space-x-4">
                <!-- Sidebar Toggle -->
                <button wire:click="toggleSidebar" 
                        class="text-gray-600 hover:text-gray-900 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-4">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-500 hover:text-gray-700' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('tickets') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('tickets*') ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-500 hover:text-gray-700' }}">
                        Tickets
                    </a>
                    <a href="{{ route('projects') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('projects*') ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-500 hover:text-gray-700' }}">
                        Projects
                    </a>
                    <a href="{{ route('companies') }}" class="px-3 py-2 text-sm font-medium {{ request()->routeIs('companies*') ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-500 hover:text-gray-700' }}">
                        Companies
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="hidden md:block">
                    <div class="relative">
                        /* Lines 226-230 omitted */
                    </div>
                </div>
            </div>            <!-- Right Section -->
            <div class="flex items-center space-x-4">
                <!-- Quick Actions -->
                <button class="relative text-gray-600 hover:text-gray-900 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                    <i class="fas fa-plus text-lg"></i>
                </button>

                <!-- Notifications -->
                <div class="relative">
                    <button wire:click="toggleNotifications" 
                            class="relative text-gray-600 hover:text-gray-900 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                        <i class="fas fa-bell text-lg"></i>
                        @if(collect($notifications)->where('read', false)->count() > 0)
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                                {{ collect($notifications)->where('read', false)->count() }}
                            </span>
                        @endif
                    </button>

                    <!-- Notifications Dropdown -->
                    @if($showNotifications)
                        <div class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden z-50">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="font-semibold text-gray-900">Notifications</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @foreach($notifications as $notification)
                                    <div class="p-4 hover:bg-gray-50 border-b border-gray-100 {{ !$notification['read'] ? 'bg-blue-50' : '' }}">
                                        <div class="flex items-start space-x-3">
                                            <div class="w-2 h-2 {{ !$notification['read'] ? 'bg-blue-500' : 'bg-gray-300' }} rounded-full mt-2"></div>
                                            <div class="flex-1">
                                                <p class="text-sm text-gray-900 {{ !$notification['read'] ? 'font-semibold' : '' }}">{{ $notification['message'] }}</p>
                                                <p class="text-xs text-gray-500 mt-1">{{ $notification['time'] }}</p>
                                            </div>
                                            @if(!$notification['read'])
                                                <button wire:click="markAsRead({{ $notification['id'] }})" 
                                                        class="text-gray-400 hover:text-gray-600">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="p-3 border-t border-gray-200">
                                <a href="#" class="text-sm text-purple-600 hover:text-purple-700 font-medium">View all notifications</a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- User Menu -->
                <div class="relative">
                    <button wire:click="toggleUserMenu" 
                            class="flex items-center space-x-3 text-gray-600 hover:text-gray-900 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                        <div class="w-8 h-8 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-md">
                            {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                        </div>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>

                    <!-- User Dropdown -->
                    @if($showUserMenu)
                        <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden z-50">
                            <div class="p-4 border-b border-gray-200">
                                <p class="font-semibold text-gray-900">{{ $user->name ?? 'Admin User' }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email ?? 'admin@example.com' }}</p>
                            </div>
                            <div class="py-2">
                                <a href="#" class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user w-4"></i>
                                    <span>Profile</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog w-4"></i>
                                    <span>Settings</span>
                                </a>
                                <a href="#" class="flex items-center space-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-moon w-4"></i>
                                    <span>Dark Mode</span>
                                </a>
                                <div class="border-t border-gray-200 my-2"></div>
                                <button wire:click="logout" 
                                        class="w-full flex items-center space-x-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt w-4"></i>
                                    <span>Logout</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Overlay for mobile -->
@if($sidebarOpen)
    <div wire:click="toggleSidebar" 
         class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>
@endif

<!-- Main Content Wrapper -->
<div class="transition-all duration-300 {{ $sidebarOpen ? ($sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64') : 'lg:ml-0' }}">
    <!-- Spacer for fixed navbar -->
    <div class="h-16"></div>
</div>

<!-- Script pour la persistance locale et améliorations -->
<script>
    document.addEventListener('livewire:init', () => {
        // Sauvegarder l'état de la sidebar dans localStorage
        function saveSidebarState(open, collapsed) {
            localStorage.setItem('sidebar_open', open);
            localStorage.setItem('sidebar_collapsed', collapsed);
        }

        // Charger l'état de la sidebar depuis localStorage
        function loadSidebarState() {
            const open = localStorage.getItem('sidebar_open') !== 'false';
            const collapsed = localStorage.getItem('sidebar_collapsed') === 'true';
            return { open, collapsed };
        }

        // Synchroniser l'état avec le backend
        Livewire.on('sidebarStateChanged', (data) => {
            saveSidebarState(data.open, data.collapsed);
        });

        // Raccourcis clavier
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + B pour toggle la sidebar
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                // Déclencher le clic sur le bouton toggle
                const toggleBtn = document.querySelector('[wire\\:click="toggleSidebar"]');
                if (toggleBtn) {
                    toggleBtn.click();
                }
            }
            
            // Ctrl/Cmd + Shift + B pour toggle la réduction
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'B') {
                e.preventDefault();
                // Déclencher le clic sur le bouton de réduction
                const collapseBtn = document.querySelector('[wire\\:click="toggleSidebarCollapse"]');
                if (collapseBtn) {
                    collapseBtn.click();
                }
            }
        });

        // Animation au survol pour les tooltips en mode réduit
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        // Réinitialiser les tooltips quand la sidebar change d'état
                        if (window.bootstrap && window.bootstrap.Tooltip) {
                            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
                            tooltipTriggerList.map(function (tooltipTriggerEl) {
                                const tooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                                if (tooltip) {
                                    tooltip.dispose();
                                }
                                return new bootstrap.Tooltip(tooltipTriggerEl, {
                                    placement: 'right',
                                    trigger: 'hover'
                                });
                            });
                        }
                    }
                });
            });
            
            observer.observe(sidebar, {
                attributes: true,
                attributeFilter: ['class']
            });
        }

        // Amélioration de l'accessibilité
        const sidebarToggle = document.querySelector('[wire\\:click="toggleSidebar"]');
        if (sidebarToggle) {
            sidebarToggle.setAttribute('aria-label', 'Basculer la barre latérale');
            sidebarToggle.setAttribute('aria-expanded', 'true');
        }

        const sidebarCollapse = document.querySelector('[wire\\:click="toggleSidebarCollapse"]');
        if (sidebarCollapse) {
            sidebarCollapse.setAttribute('aria-label', 'Réduire/Étendre la barre latérale');
            sidebarCollapse.setAttribute('aria-expanded', 'true');
        }
    });
</script>
