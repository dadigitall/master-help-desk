<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'HelpDesk') }} - @yield('title', 'Dashboard')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        <!-- Custom Admin Styles -->
        <style>
            /* Custom scrollbar for sidebar */
            #sidebar::-webkit-scrollbar {
                width: 6px;
            }
            #sidebar::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.1);
            }
            #sidebar::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.3);
                border-radius: 3px;
            }
            #sidebar::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.5);
            }

            /* Smooth transitions */
            * {
                transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 150ms;
            }

            /* Custom animations */
            @keyframes pulse-glow {
                0%, 100% { box-shadow: 0 0 20px rgba(147, 51, 234, 0.5); }
                50% { box-shadow: 0 0 30px rgba(147, 51, 234, 0.8); }
            }

            .animate-pulse-glow {
                animation: pulse-glow 2s infinite;
            }

            /* Glass morphism effects */
            .glass {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            /* Professional gradient backgrounds */
            .gradient-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .gradient-secondary {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            }

            .gradient-success {
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            }

            /* Hover effects */
            .hover-lift:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }

            /* Loading spinner */
            .spinner {
                border: 2px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                border-top: 2px solid white;
                width: 20px;
                height: 20px;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <x-banner />

        <!-- Navigation Menu Component -->
        @livewire('navigation-menu')

        <!-- Main Content Area -->
        <main class="relative">
            <!-- Page Heading (if exists) -->
            @if (isset($header))
                <header class="bg-white border-b border-gray-200 shadow-sm">
                    <div class="px-4 sm:px-6 lg:px-8 py-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $header }}</h1>
                                @hasSection('subtitle')
                                    <p class="mt-1 text-sm text-gray-600">@yield('subtitle')</p>
                                @endif
                            </div>
                            @hasSection('actions')
                                <div class="flex items-center space-x-3">
                                    @yield('actions')
                                </div>
                            @endif
                        </div>
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <div class="px-4 sm:px-6 lg:px-8 py-8">
                <!-- Breadcrumb Navigation -->
                <x-breadcrumb />

                <!-- Flash Messages -->
                @if(session('flash_notification'))
                    @foreach(session('flash_notification') as $message)
                        <div class="mb-6 rounded-lg border p-4 {{ $message['level'] === 'success' ? 'bg-green-50 border-green-200 text-green-800' : ($message['level'] === 'danger' || $message['level'] === 'error' ? 'bg-red-50 border-red-200 text-red-800' : ($message['level'] === 'warning' ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : 'bg-blue-50 border-blue-200 text-blue-800')) }}">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas {{ $message['level'] === 'success' ? 'fa-check-circle' : (($message['level'] === 'danger' || $message['level'] === 'error') ? 'fa-exclamation-circle' : ($message['level'] === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle')) }}"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium">{!! $message['message'] !!}</p>
                                </div>
                                @if($message['important'])
                                    <div class="ml-auto pl-3">
                                        <div class="-mx-1.5 -my-1.5">
                                            <button type="button" class="inline-flex rounded-md p-1.5 {{ $message['level'] === 'success' ? 'bg-green-50 text-green-500 hover:bg-green-100' : (($message['level'] === 'danger' || $message['level'] === 'error') ? 'bg-red-50 text-red-500 hover:bg-red-100' : ($message['level'] === 'warning' ? 'bg-yellow-50 text-yellow-500 hover:bg-yellow-100' : 'bg-blue-50 text-blue-500 hover:bg-blue-100')) }}">
                                                <span class="sr-only">Dismiss</span>
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    {{ session()->forget('flash_notification') }}
                @endif

                <!-- Main Content Slot -->
                {{ $slot }}
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-sm text-gray-500">
                        © {{ date('Y') }} {{ config('app.name', 'HelpDesk') }}. All rights reserved.
                    </div>
                    <div class="flex items-center space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Documentation</a>
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Support</a>
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Privacy Policy</a>
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Terms of Service</a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Modals Stack -->
        @stack('modals')

        <!-- Livewire Scripts -->
        @livewireScripts

        <!-- Custom JavaScript -->
        <script>
            // Auto-hide notifications after 5 seconds
            document.addEventListener('livewire:init', () => {
                setTimeout(() => {
                    const alerts = document.querySelectorAll('[role="alert"]');
                    alerts.forEach(alert => {
                        setTimeout(() => {
                            alert.style.transition = 'opacity 0.5s';
                            alert.style.opacity = '0';
                            setTimeout(() => alert.remove(), 500);
                        }, 5000);
                    });
                }, 100);
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth' });
                    }
                });
            });

            // Enhanced dropdown handling
            document.addEventListener('click', (e) => {
                // Close dropdowns when clicking outside
                const dropdowns = document.querySelectorAll('[data-dropdown]');
                dropdowns.forEach(dropdown => {
                    if (!dropdown.contains(e.target)) {
                        const menu = dropdown.querySelector('[data-dropdown-menu]');
                        if (menu && !menu.classList.contains('hidden')) {
                            menu.classList.add('hidden');
                        }
                    }
                });
            });

            // Loading states for buttons
            document.addEventListener('click', (e) => {
                const button = e.target.closest('button[wire:click]');
                if (button && !button.disabled) {
                    const originalContent = button.innerHTML;
                    button.disabled = true;
                    button.innerHTML = '<div class="spinner inline-block mr-2"></div> Loading...';
                    
                    // Reset after 3 seconds (fallback)
                    setTimeout(() => {
                        button.disabled = false;
                        button.innerHTML = originalContent;
                    }, 3000);
                }
            });
        </script>
    </body>
</html>
