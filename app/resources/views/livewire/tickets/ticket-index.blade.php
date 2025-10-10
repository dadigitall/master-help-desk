<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tickets</h1>
                <p class="mt-1 text-sm text-gray-500">Manage and track support tickets</p>
            </div>
            @if ($canCreate)
                <button wire:click="$dispatch('openCreateTicketModal')" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    New Ticket
                </button>
            @endif
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <div class="relative">
                    <input type="text" 
                           wire:model.debounce.300ms="search" 
                           placeholder="Search tickets..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Project Filter -->
            <div>
                <select wire:model="selectedProject" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Projects</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select wire:model="selectedStatus" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Advanced Filters -->
        <div class="flex flex-wrap items-center gap-4">
            <!-- Priority Filter -->
            <select wire:model="selectedPriority" 
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Priorities</option>
                @foreach ($priorityOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            <!-- Assigned Filter -->
            <select wire:model="selectedAssigned" 
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Assignees</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>

            <!-- Per Page -->
            <select wire:model="perPage" 
                    class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="15">15 per page</option>
                <option value="30">30 per page</option>
                <option value="50">50 per page</option>
                <option value="100">100 per page</option>
            </select>

            <!-- View Mode Toggle -->
            <div class="flex items-center space-x-2">
                <button wire:click="setViewMode('table')" 
                        class="p-2 rounded {{ $viewMode === 'table' ? 'bg-blue-100 text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                    <i class="fas fa-list"></i>
                </button>
                <button wire:click="setViewMode('card')" 
                        class="p-2 rounded {{ $viewMode === 'card' ? 'bg-blue-100 text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                    <i class="fas fa-th-large"></i>
                </button>
            </div>

            <!-- My Tickets Toggle -->
            <label class="flex items-center">
                <input type="checkbox" wire:model="showMyTickets" class="rounded border-gray-300 text-blue-600">
                <span class="ml-2 text-sm text-gray-700">My tickets only</span>
            </label>

            <!-- Sort -->
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-700">Sort by:</span>
                <select wire:model="sortBy" class="text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <option value="created_at">Created</option>
                    <option value="updated_at">Updated</option>
                    <option value="title">Title</option>
                    <option value="priority">Priority</option>
                    <option value="status">Status</option>
                </select>
                <button wire:click="sortBy('{{ $sortBy }}')" class="p-1 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-sort-amount-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                </button>
            </div>

            <!-- Clear Filters -->
            <button wire:click="clearFilters" 
                    class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md hover:bg-gray-50">
                <i class="fas fa-times mr-1"></i>Clear
            </button>
        </div>
    </div>

    <!-- Tickets Table/Card View -->
    @if ($tickets->count() > 0)
        @if ($viewMode === 'table')
            <!-- Table View -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ticket
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Project
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Priority
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assigned To
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($tickets as $ticket)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $ticket->ticket_number }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($ticket->title, 50) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-6 w-6 rounded flex items-center justify-center" style="background-color: {{ $ticket->project->color }}20;">
                                            <span class="text-xs font-bold" style="color: {{ $ticket->project->color }};">{{ strtoupper(substr($ticket->project->name, 0, 1)) }}</span>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-900">{{ $ticket->project->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($canEdit)
                                        <select wire:change="updateTicketStatus({{ $ticket->id }}, $event.target.value)" 
                                                class="text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                            @foreach ($statusOptions as $value => $label)
                                                <option value="{{ $value }}" {{ $ticket->status === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($ticket->status === 'open') bg-red-100 text-red-800
                                            @elseif($ticket->status === 'in_progress') bg-yellow-100 text-yellow-800
                                            @elseif($ticket->status === 'pending') bg-gray-100 text-gray-800
                                            @elseif($ticket->status === 'resolved') bg-blue-100 text-blue-800
                                            @elseif($ticket->status === 'closed') bg-green-100 text-green-800
                                            @else bg-orange-100 text-orange-800
                                            @endif">
                                            {{ $statusOptions[$ticket->status] ?? ucfirst($ticket->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($canEdit)
                                        <select wire:change="updateTicketPriority({{ $ticket->id }}, $event.target.value)" 
                                                class="text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                            @foreach ($priorityOptions as $value => $label)
                                                <option value="{{ $value }}" {{ $ticket->priority === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($ticket->priority === 'urgent') bg-red-100 text-red-800
                                            @elseif($ticket->priority === 'high') bg-orange-100 text-orange-800
                                            @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800
                                            @endif">
                                            {{ $priorityOptions[$ticket->priority] ?? ucfirst($ticket->priority) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($canEdit)
                                        <select wire:change="assignTicket({{ $ticket->id }}, $event.target.value)" 
                                                class="text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Unassigned</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" {{ $ticket->assigned_to === $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <span class="text-sm text-gray-900">
                                            {{ $ticket->assignedUser?->name ?? 'Unassigned' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $ticket->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="$dispatch('openViewTicketModal', {{ $ticket->id }})" 
                                                class="text-blue-600 hover:text-blue-900">
                                            View
                                        </button>
                                        @if ($canEdit)
                                            <button wire:click="$dispatch('openEditTicketModal', {{ $ticket->id }})" 
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                Edit
                                            </button>
                                        @endif
                                        @if ($canDelete)
                                            <button wire:click="deleteTicket({{ $ticket->id }})" 
                                                    wire:confirm="Are you sure you want to delete this ticket?"
                                                    class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Card View -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($tickets as $ticket)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-lg flex items-center justify-center" style="background-color: {{ $ticket->project->color }}20;">
                                        <span class="text-sm font-bold" style="color: {{ $ticket->project->color }};">{{ strtoupper(substr($ticket->project->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $ticket->ticket_number }}</div>
                                        <div class="text-xs text-gray-500">{{ $ticket->project->name }}</div>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($ticket->priority === 'urgent') bg-red-100 text-red-800
                                    @elseif($ticket->priority === 'high') bg-orange-100 text-orange-800
                                    @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ $priorityOptions[$ticket->priority] ?? ucfirst($ticket->priority) }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ Str::limit($ticket->title, 60) }}</h3>

                            <!-- Description -->
                            @if ($ticket->description)
                                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($ticket->description, 80) }}</p>
                            @endif

                            <!-- Status and Assignment -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($ticket->status === 'open') bg-red-100 text-red-800
                                    @elseif($ticket->status === 'in_progress') bg-yellow-100 text-yellow-800
                                    @elseif($ticket->status === 'pending') bg-gray-100 text-gray-800
                                    @elseif($ticket->status === 'resolved') bg-blue-100 text-blue-800
                                    @elseif($ticket->status === 'closed') bg-green-100 text-green-800
                                    @else bg-orange-100 text-orange-800
                                    @endif">
                                    {{ $statusOptions[$ticket->status] ?? ucfirst($ticket->status) }}
                                </span>
                                <div class="text-xs text-gray-500">
                                    @if ($ticket->assignedUser)
                                        <i class="fas fa-user mr-1"></i>{{ $ticket->assignedUser->name }}
                                    @else
                                        <i class="fas fa-user-slash mr-1"></i>Unassigned
                                    @endif
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <div class="text-xs text-gray-500">
                                    {{ $ticket->created_at->format('M d, Y') }}
                                </div>
                                <div class="flex space-x-2">
                                    <button wire:click="$dispatch('openViewTicketModal', {{ $ticket->id }})" 
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View
                                    </button>
                                    @if ($canEdit)
                                        <button wire:click="$dispatch('openEditTicketModal', {{ $ticket->id }})" 
                                                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                            Edit
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <i class="fas fa-ticket-alt text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No tickets found</h3>
            <p class="text-gray-500 mb-4">Get started by creating your first ticket.</p>
            @if ($canCreate)
                <button wire:click="$dispatch('openCreateTicketModal')" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    Create Ticket
                </button>
            @endif
        </div>
    @endif

    <!-- Include Modals -->
    <livewire:tickets.ticket-create />
    <livewire:tickets.ticket-edit />
    <livewire:tickets.ticket-show />
</div>
