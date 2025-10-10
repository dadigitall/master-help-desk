<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <livewire:dashboard.dashboard-index />
            </div>
        </div>
    </div>

    <!-- Auto-refresh script -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Auto-refresh dashboard every 60 seconds
            setInterval(() => {
                Livewire.dispatch('refreshDashboard');
            }, 60000);

            // Handle period changes
            Livewire.on('periodChanged', (period) => {
                Livewire.find('{{ $this->id }}').updatePeriod(period);
            });

            // Handle filter changes
            Livewire.on('filtersChanged', (filters) => {
                Livewire.find('{{ $this->id }}').updateFilters(filters);
            });
        });
    </script>
</x-app-layout>
