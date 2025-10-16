<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Créer un projet
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <livewire:projects.project-create />
            </div>
        </div>
    </div>

    <!-- Notification Component -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (event) => {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg transform transition-all duration-300 ${
                    event.type === 'success' ? 'bg-green-500 text-white' :
                    event.type === 'error' ? 'bg-red-500 text-white' :
                    event.type === 'warning' ? 'bg-yellow-500 text-white' :
                    'bg-blue-500 text-white'
                }`;
                notification.innerHTML = `
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas ${
                                event.type === 'success' ? 'fa-check-circle' :
                                event.type === 'error' ? 'fa-exclamation-circle' :
                                event.type === 'warning' ? 'fa-exclamation-triangle' :
                                'fa-info-circle'
                            }"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">${event.message}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(notification);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    notification.remove();
                }, 5000);
            });
        });
    </script>
</x-app-layout>
