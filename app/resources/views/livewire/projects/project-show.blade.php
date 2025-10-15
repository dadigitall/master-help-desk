<!-- Project Details Modal -->
<div x-data="{ show: @entangle('showModal') }" 
     x-show="show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
            @if ($project)
                <div class="bg-white">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-16 w-16 rounded-lg flex items-center justify-center" style="background-color: {{ $project->color }}20;">
                                    <span class="text-2xl font-bold" style="color: {{ $project->color }};">{{ strtoupper(substr($project->name, 0, 1)) }}</span>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-2xl font-bold text-gray-900">{{ $project->name }}</h2>
                                    <p class="text-sm text-gray-500">{{ $project->prefix }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <!-- Favorite Toggle -->
                                <button wire:click="toggleFavorite" 
                                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-star text-xl {{ $isFavorite ? 'text-yellow-500' : 'text-gray-400' }}"></i>
                                </button>
                                <!-- Status Badge -->
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $project->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $project->active ? 'Active' : 'Inactive' }}
                                </span>
                                <!-- Close Button -->
                                <button wire:click="closeModal" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                    <i class="fas fa-times text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="max-h-[80vh] overflow-y-auto">
                        <!-- Project Info -->
                        <div class="px-6 py-6 bg-gray-50 border-b border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Company -->
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Company</h3>
                                    <p class="mt-1 text-lg font-medium text-gray-900">{{ $project->company->name }}</p>
                                </div>
                                <!-- Created By -->
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Created By</h3>
                                    <p class="mt-1 text-lg font-medium text-gray-900">{{ $project->user->name }}</p>
                                </div>
                                <!-- Created Date -->
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500">Created</h3>
                                    <p class="mt-1 text-lg font-medium text-gray-900">{{ $project->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            @if ($project->description)
                                <div class="mt-6">
                                    <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                                    <p class="text-gray-900">{{ $project->description }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Stats Dashboard -->
                        <div class="px-6 py-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-6">Project Statistics</h3>
                            
                            <!-- Main Stats -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                                <!-- Total Tickets -->
                                <div class="bg-white rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-blue-100 rounded-lg">
                                            <i class="fas fa-ticket-alt text-blue-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-2xl font-bold text-gray-900">{{ $project->tickets_count ?? 0 }}</div>
                                            <div class="text-sm text-gray-500">Total Tickets</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Open Tickets -->
                                <div class="bg-white rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-orange-100 rounded-lg">
                                            <i class="fas fa-exclamation-circle text-orange-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-2xl font-bold text-orange-600">{{ $ticketsStats->get('open', 0) }}</div>
                                            <div class="text-sm text-gray-500">Open Tickets</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- In Progress -->
                                <div class="bg-white rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-yellow-100 rounded-lg">
                                            <i class="fas fa-spinner text-yellow-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-2xl font-bold text-yellow-600">{{ $ticketsStats->get('in_progress', 0) }}</div>
                                            <div class="text-sm text-gray-500">In Progress</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Closed Tickets -->
                                <div class="bg-white rounded-lg border border-gray-200 p-4">
                                    <div class="flex items-center">
                                        <div class="p-3 bg-green-100 rounded-lg">
                                            <i class="fas fa-check-circle text-green-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-2xl font-bold text-green-600">{{ $ticketsStats->get('closed', 0) }}</div>
                                            <div class="text-sm text-gray-500">Closed</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Section -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                                <!-- Completion Progress -->
                                <div class="bg-white rounded-lg border border-gray-200 p-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-4">Completion Progress</h4>
                                    <div class="mb-4">
                                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                                            <span>Overall Progress</span>
                                            <span>{{ $completionPercentage }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div class="bg-green-600 h-3 rounded-full transition-all duration-300" 
                                                 style="width: {{ $completionPercentage }}%"></div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4 text-center">
                                        <div>
                                            <div class="text-lg font-semibold text-gray-900">{{ $ticketsStats->get('open', 0) }}</div>
                                            <div class="text-xs text-gray-500">Open</div>
                                        </div>
                                        <div>
                                            <div class="text-lg font-semibold text-gray-900">{{ $ticketsStats->get('in_progress', 0) }}</div>
                                            <div class="text-xs text-gray-500">In Progress</div>
                                        </div>
                                        <div>
                                            <div class="text-lg font-semibold text-gray-900">{{ $ticketsStats->get('closed', 0) }}</div>
                                            <div class="text-xs text-gray-500">Closed</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Priority Distribution -->
                                <div class="bg-white rounded-lg border border-gray-200 p-6">
                                    <h4 class="text-lg font-medium text-gray-900 mb-4">Priority Distribution</h4>
                                    <div class="space-y-3">
                                        @foreach(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'] as $priority => $label)
                                            <div>
                                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                                    <span>{{ $label }}</span>
                                                    <span>{{ $ticketsByPriority->get($priority, 0) }}</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="h-2 rounded-full transition-all duration-300
                                                        @if($priority === 'urgent') bg-red-600
                                                        @elseif($priority === 'high') bg-orange-600
                                                        @elseif($priority === 'medium') bg-yellow-600
                                                        @else bg-green-600
                                                        @endif" 
                                                         style="width: {{ $project->tickets_count > 0 ? ($ticketsByPriority->get($priority, 0) / $project->tickets_count) * 100 : 0 }}%"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Tickets -->
                            @if ($recentTickets->count() > 0)
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 mb-4">Recent Tickets</h4>
                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Ticket
                                                        </th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Status
                                                        </th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Priority
                                                        </th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Created By
                                                        </th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                            Date
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                    @foreach ($recentTickets as $ticket)
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="text-sm font-medium text-gray-900">
                                                                    {{ $project->prefix }}-{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}
                                                                </div>
                                                                <div class="text-sm text-gray-500">{{ $ticket->title }}</div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                    @if($ticket->status === 'open') bg-red-100 text-red-800
                                                                    @elseif($ticket->status === 'in_progress') bg-yellow-100 text-yellow-800
                                                                    @else bg-green-100 text-green-800
                                                                    @endif">
                                                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                    @if($ticket->priority === 'urgent') bg-red-100 text-red-800
                                                                    @elseif($ticket->priority === 'high') bg-orange-100 text-orange-800
                                                                    @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800
                                                                    @else bg-green-100 text-green-800
                                                                    @endif">
                                                                    {{ ucfirst($ticket->priority) }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                {{ $ticket->user->name }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                {{ $ticket->created_at->format('M d, Y') }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8 bg-gray-50 rounded-lg">
                                    <i class="fas fa-ticket-alt text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No tickets created yet</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                Last updated: {{ $project->updated_at->format('M d, Y H:i') }}
                            </div>
                            <div class="flex space-x-3">
                                <button wire:click="$dispatch('openEditProjectModal', {{ $project->id }})" 
                                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-edit mr-2"></i>Edit Project
                                </button>
                                <button wire:click="closeModal" 
                                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
