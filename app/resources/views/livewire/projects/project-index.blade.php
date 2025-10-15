<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Projects</h1>
                <p class="mt-1 text-sm text-gray-500">Manage your projects and track progress</p>
            </div>
            @if ($canCreate)
                <button wire:click="$dispatch('openCreateProjectModal')" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    New Project
                </button>
            @endif
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <div class="relative">
                    <input type="text" 
                           wire:model.debounce.300ms="search" 
                           placeholder="Search projects..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Company Filter -->
            <div>
                <select wire:model="selectedCompany" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Companies</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Per Page -->
            <div>
                <select wire:model="perPage" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="12">12 per page</option>
                    <option value="24">24 per page</option>
                    <option value="48">48 per page</option>
                    <option value="96">96 per page</option>
                </select>
            </div>
        </div>

        <!-- Advanced Filters -->
        <div class="mt-4 flex flex-wrap items-center gap-4">
            <!-- View Mode Toggle -->
            <div class="flex items-center space-x-2">
                <button wire:click="setViewMode('grid')" 
                        class="p-2 rounded {{ $viewMode === 'grid' ? 'bg-blue-100 text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                    <i class="fas fa-th"></i>
                </button>
                <button wire:click="setViewMode('list')" 
                        class="p-2 rounded {{ $viewMode === 'list' ? 'bg-blue-100 text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                    <i class="fas fa-list"></i>
                </button>
            </div>

            <!-- Show Inactive -->
            <label class="flex items-center">
                <input type="checkbox" wire:model="showInactive" class="rounded border-gray-300 text-blue-600">
                <span class="ml-2 text-sm text-gray-700">Show inactive</span>
            </label>

            <!-- Favorites Only -->
            <label class="flex items-center">
                <input type="checkbox" wire:model="showFavoritesOnly" class="rounded border-gray-300 text-blue-600">
                <span class="ml-2 text-sm text-gray-700">Favorites only</span>
            </label>

            <!-- Sort -->
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-700">Sort by:</span>
                <select wire:model="sortBy" class="text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <option value="name">Name</option>
                    <option value="created_at">Created</option>
                    <option value="updated_at">Updated</option>
                    <option value="tickets_count">Tickets</option>
                </select>
                <button wire:click="sortBy('{{ $sortBy }}')" class="p-1 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-sort-amount-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    @if ($errors->has('delete'))
        <div class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-800">{{ $errors->first('delete') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Projects Grid/List -->
    @if ($projects->count() > 0)
        @if ($viewMode === 'grid')
            <!-- Grid View -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($projects as $project)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                        <!-- Project Header -->
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 rounded-lg flex items-center justify-center" style="background-color: {{ $project->color }}20;">
                                        <span class="text-lg font-bold" style="color: {{ $project->color }};">{{ strtoupper(substr($project->name, 0, 1)) }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $project->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $project->prefix }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <!-- Favorite Toggle -->
                                    <button wire:click="toggleFavorite({{ $project->id }})" 
                                            class="p-1 text-gray-400 hover:text-yellow-500 transition-colors">
                                        <i class="fas fa-star {{ $this->isFavorite($project->id) ? 'text-yellow-500' : '' }}"></i>
                                    </button>
                                    <!-- Status Badge -->
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $project->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $project->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Company -->
                            <div class="mb-3">
                                <span class="text-sm text-gray-500">Company:</span>
                                <span class="ml-1 text-sm font-medium text-gray-900">{{ $project->company->name }}</span>
                            </div>

                            <!-- Description -->
                            @if ($project->description)
                                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($project->description, 80) }}</p>
                            @endif

                            <!-- Stats -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ $project->tickets_count }}</div>
                                    <div class="text-xs text-gray-500">Total Tickets</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-orange-600">{{ $project->open_tickets_count }}</div>
                                    <div class="text-xs text-gray-500">Open Tickets</div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            @if ($project->tickets_count > 0)
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>Progress</span>
                                        <span>{{ round((($project->tickets_count - $project->open_tickets_count) / $project->tickets_count) * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" 
                                             style="width: {{ (($project->tickets_count - $project->open_tickets_count) / $project->tickets_count) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                                <div class="flex space-x-2">
                                    <button wire:click="$dispatch('openViewProjectModal', {{ $project->id }})" 
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View
                                    </button>
                                    @if ($canEdit)
                                        <button wire:click="$dispatch('openEditProjectModal', {{ $project->id }})" 
                                                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                            Edit
                                        </button>
                                    @endif
                                </div>
                                @if ($canEdit)
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="toggleActive({{ $project->id }})" 
                                                class="text-sm {{ $project->active ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800' }}">
                                            {{ $project->active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                        @if ($canDelete && $project->tickets_count === 0)
                                            <button wire:click="deleteProject({{ $project->id }})" 
                                                    wire:confirm="Are you sure you want to delete this project?"
                                                    class="text-red-600 hover:text-red-800 text-sm">
                                                Delete
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- List View -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Project
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Company
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tickets
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($projects as $project)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-lg flex items-center justify-center" style="background-color: {{ $project->color }}20;">
                                            <span class="text-sm font-bold" style="color: {{ $project->color }};">{{ strtoupper(substr($project->name, 0, 1)) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $project->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $project->prefix }}</div>
                                        </div>
                                        @if ($this->isFavorite($project->id))
                                            <i class="fas fa-star text-yellow-500 ml-2"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $project->company->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $project->tickets_count }} total</div>
                                    <div class="text-sm text-orange-600">{{ $project->open_tickets_count }} open</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $project->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $project->active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="$dispatch('openViewProjectModal', {{ $project->id }})" 
                                                class="text-blue-600 hover:text-blue-900">
                                            View
                                        </button>
                                        @if ($canEdit)
                                            <button wire:click="$dispatch('openEditProjectModal', {{ $project->id }})" 
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                Edit
                                            </button>
                                            <button wire:click="toggleActive({{ $project->id }})" 
                                                    class="text-orange-600 hover:text-orange-900">
                                                {{ $project->active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        @endif
                                        @if ($canDelete && $project->tickets_count === 0)
                                            <button wire:click="deleteProject({{ $project->id }})" 
                                                    wire:confirm="Are you sure you want to delete this project?"
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
        @endif

        <!-- Pagination -->
        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No projects found</h3>
            <p class="text-gray-500 mb-4">Get started by creating your first project.</p>
            @if ($canCreate)
                <button wire:click="$dispatch('openCreateProjectModal')" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-plus mr-2"></i>
                    Create Project
                </button>
            @endif
        </div>
    @endif

    <!-- Include Modals -->
    <livewire:projects.project-create />
    <livewire:projects.project-edit />
    <livewire:projects.project-show />
</div>
