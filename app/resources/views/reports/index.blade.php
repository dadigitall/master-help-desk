<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rapports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            Génération de Rapports
                        </h3>
                        <button 
                            wire:click="$toggle('showForm')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                        >
                            {{ __('Nouveau Rapport') }}
                        </button>
                    </div>

                    <!-- Formulaire de génération -->
                    <div wire:loading.flex class="hidden">
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            <span class="ml-3 text-gray-600">Génération du rapport en cours...</span>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form wire:submit="generate" class="space-y-6" wire:model.defer="showForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Type de rapport -->
                            <div>
                                <x-label for="report_type" :value="__('Type de rapport')" />
                                <select 
                                    wire:model="report_type" 
                                    id="report_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">{{ __('Sélectionner un type') }}</option>
                                    @foreach($reportTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('report_type')" class="mt-2" />
                            </div>

                            <!-- Format -->
                            <div>
                                <x-label for="format" :value="__('Format')" />
                                <select 
                                    wire:model="format" 
                                    id="format"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                    @foreach($reportFormats as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('format')" class="mt-2" />
                            </div>

                            <!-- Date de début -->
                            <div>
                                <x-label for="date_from" :value="__('Date de début')" />
                                <input 
                                    wire:model="date_from" 
                                    type="date" 
                                    id="date_from"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                <x-input-error :messages="$errors->get('date_from')" class="mt-2" />
                            </div>

                            <!-- Date de fin -->
                            <div>
                                <x-label for="date_to" :value="__('Date de fin')" />
                                <input 
                                    wire:model="date_to" 
                                    type="date" 
                                    id="date_to"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                <x-input-error :messages="$errors->get('date_to')" class="mt-2" />
                            </div>

                            <!-- Entreprise -->
                            <div>
                                <x-label for="company_id" :value="__('Entreprise')" />
                                <select 
                                    wire:model="company_id" 
                                    id="company_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">{{ __('Toutes les entreprises') }}</option>
                                    @foreach(\App\Models\Company::orderBy('name')->get() as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                            </div>

                            <!-- Statut -->
                            <div>
                                <x-label for="status_id" :value="__('Statut')" />
                                <select 
                                    wire:model="status_id" 
                                    id="status_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">{{ __('Tous les statuts') }}</option>
                                    @foreach(\App\Models\TicketStatus::orderBy('name')->get() as $status)
                                        <option value="{{ $status->id }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <button 
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove>{{ __('Générer le rapport') }}</span>
                                <span wire:loading class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Génération...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Rapports récents -->
                <div class="p-6 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Rapports Récents
                    </h3>

                    @if(count($recentReports) > 0)
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nom du fichier
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Taille
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date de création
                                        </th>
                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentReports as $report)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $report['filename'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $report['size'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $report['created_at']->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ $report['download_url'] }}" 
                                                   class="text-blue-600 hover:text-blue-900 mr-3"
                                                   target="_blank">
                                                    {{ __('Télécharger') }}
                                                </a>
                                                <form wire:submit="destroy('{{ $report['filename'] }}')" 
                                                      wire:confirm="{{ __('Êtes-vous sûr de vouloir supprimer ce rapport ?') }}" 
                                                      class="inline">
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900"
                                                            wire:loading.attr="disabled">
                                                        {{ __('Supprimer') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">
                                Aucun rapport
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Commencez par générer votre premier rapport.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
