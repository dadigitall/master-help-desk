<!-- View Company Modal -->
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
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl leading-6 font-bold text-gray-900">
                        Company Details
                    </h3>
                    <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                @if ($company)
                    <!-- Company Information -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Main Info -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Basic Info Card -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-bold text-xl">{{ strtoupper(substr($company->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-xl font-semibold text-gray-900">{{ $company->name }}</h4>
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $company->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $company->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Email Address</label>
                                        <p class="text-gray-900">{{ $company->email }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Phone Number</label>
                                        <p class="text-gray-900">{{ $company->phone ?? 'Not provided' }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-500">Address</label>
                                        <p class="text-gray-900">{{ $company->address ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Projects Card -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h5 class="text-lg font-semibold text-gray-900 mb-4">
                                    Projects ({{ $company->projects->count() }})
                                </h5>
                                @if ($company->projects->count() > 0)
                                    <div class="space-y-3">
                                        @foreach ($company->projects as $project)
                                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 rounded-full flex items-center justify-center" style="background-color: {{ $project->color }}20;">
                                                            <span class="text-xs font-bold" style="color: {{ $project->color }};">{{ strtoupper(substr($project->name, 0, 1)) }}</span>
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm font-medium text-gray-900">{{ $project->name }}</p>
                                                            <p class="text-xs text-gray-500">{{ $project->user->name }} • {{ $project->prefix }}</p>
                                                        </div>
                                                    </div>
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $project->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $project->active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </div>
                                                @if ($project->description)
                                                    <p class="mt-2 text-sm text-gray-600">{{ Str::limit($project->description, 100) }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-gray-500">No projects found for this company</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Sidebar Stats -->
                        <div class="space-y-6">
                            <!-- Stats Card -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h5 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h5>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Total Projects</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $company->projects->count() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Active Projects</span>
                                        <span class="text-sm font-medium text-green-600">{{ $company->projects->where('active', true)->count() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">Inactive Projects</span>
                                        <span class="text-sm font-medium text-red-600">{{ $company->projects->where('active', false)->count() }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Metadata Card -->
                            <div class="bg-gray-50 rounded-lg p-6">
                                <h5 class="text-lg font-semibold text-gray-900 mb-4">Metadata</h5>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Created</label>
                                        <p class="text-sm text-gray-900">{{ $company->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Last Updated</label>
                                        <p class="text-sm text-gray-900">{{ $company->updated_at->format('M d, Y \a\t h:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Modal Actions -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" 
                        wire:click="closeModal" 
                        class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
