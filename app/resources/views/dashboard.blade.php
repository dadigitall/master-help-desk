<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            Dashboard Analytics
        </h2>
    </x-slot>

    <x-slot name="subtitle">
        Real-time insights and performance metrics for your help desk system
    </x-slot>

    <x-slot name="actions">
        <button onclick="window.location.reload()" 
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors">
            <i class="fas fa-sync-alt mr-2"></i>
            Refresh
        </button>
        <button class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg text-sm font-medium hover:from-purple-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all shadow-lg hover:shadow-xl">
            <i class="fas fa-download mr-2"></i>
            Export Report
        </button>
    </x-slot>

    <!-- Quick Stats Bar -->
    <div class="mb-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Active Users</p>
                    <p class="text-2xl font-bold mt-1">24</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Response Rate</p>
                    <p class="text-2xl font-bold mt-1">98%</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Avg Response</p>
                    <p class="text-2xl font-bold mt-1">2.3h</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Satisfaction</p>
                    <p class="text-2xl font-bold mt-1">4.8</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="space-y-8">
        <livewire:dashboard.dashboard-index />
    </div>

    <!-- Floating Action Button for Quick Actions -->
    <div class="fixed bottom-8 right-8 z-50">
        <div class="relative group">
            <button class="w-14 h-14 bg-gradient-to-r from-purple-600 to-purple-700 rounded-full text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110 flex items-center justify-center">
                <i class="fas fa-plus text-xl"></i>
            </button>
            
            <!-- Quick Actions Menu -->
            <div class="absolute bottom-16 right-0 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform scale-95 group-hover:scale-100">
                <div class="bg-white rounded-lg shadow-xl border border-gray-200 py-2 min-w-48">
                    <a href="{{ route('tickets.create') }}" 
                       class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">
                        <i class="fas fa-ticket-alt w-4 mr-3"></i>
                        New Ticket
                    </a>
                    <a href="{{ route('projects.create') }}" 
                       class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">
                        <i class="fas fa-project-diagram w-4 mr-3"></i>
                        New Project
                    </a>
                    <a href="{{ route('companies.create') }}" 
                       class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors">
                        <i class="fas fa-building w-4 mr-3"></i>
                        New Company
                    </a>
                    <hr class="my-2">
                    <button class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors w-full text-left">
                        <i class="fas fa-file-export w-4 mr-3"></i>
                        Export Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Auto-refresh and Real-time Updates -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Auto-refresh dashboard every 30 seconds
            const refreshInterval = setInterval(() => {
                if (!document.hidden) {
                    Livewire.dispatch('refreshDashboard');
                }
            }, 30000);

            // Clear interval when page is hidden to save resources
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    clearInterval(refreshInterval);
                } else {
                    location.reload(); // Reload when page becomes visible again
                }
            });

            // Handle period changes
            Livewire.on('periodChanged', (period) => {
                const dashboardComponent = document.querySelector('[wire\\:id]');
                if (dashboardComponent) {
                    Livewire.find(dashboardComponent.getAttribute('wire:id')).updatePeriod(period);
                }
            });

            // Handle filter changes
            Livewire.on('filtersChanged', (filters) => {
                const dashboardComponent = document.querySelector('[wire\\:id]');
                if (dashboardComponent) {
                    Livewire.find(dashboardComponent.getAttribute('wire:id')).updateFilters(filters);
                }
            });

            // Show loading state during updates
            Livewire.hook('request', ({ uri, options, payload, cancel, succeed }) => {
                // Show loading spinner
                const loadingSpinners = document.querySelectorAll('.fa-sync-alt');
                loadingSpinners.forEach(spinner => {
                    spinner.classList.add('animate-spin');
                });
            });

            Livewire.hook('response', ({ uri, options, payload, response, succeed }) => {
                // Hide loading spinner
                const loadingSpinners = document.querySelectorAll('.fa-sync-alt');
                loadingSpinners.forEach(spinner => {
                    spinner.classList.remove('animate-spin');
                });
            });

            // Animate numbers on update
            Livewire.on('statsUpdated', () => {
                animateNumbers();
            });

            function animateNumbers() {
                const numbers = document.querySelectorAll('[data-animate-number]');
                numbers.forEach(number => {
                    const target = parseInt(number.getAttribute('data-animate-number'));
                    const current = parseInt(number.textContent);
                    const increment = (target - current) / 20;
                    let step = 0;
                    
                    const timer = setInterval(() => {
                        step++;
                        const value = Math.round(current + (increment * step));
                        number.textContent = value;
                        
                        if (step >= 20) {
                            number.textContent = target;
                            clearInterval(timer);
                        }
                    }, 50);
                });
            }

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                // Ctrl/Cmd + K for quick search
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    document.querySelector('input[placeholder*="Search"]').focus();
                }
                
                // Ctrl/Cmd + N for new ticket
                if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                    e.preventDefault();
                    window.location.href = '{{ route("tickets.create") }}';
                }
                
                // Escape to close dropdowns
                if (e.key === 'Escape') {
                    document.querySelectorAll('[data-dropdown-menu]').forEach(menu => {
                        if (!menu.classList.contains('hidden')) {
                            menu.classList.add('hidden');
                        }
                    });
                }
            });
        });
    </script>
</x-app-layout>
