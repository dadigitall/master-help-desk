<!-- Create Ticket Modal -->
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
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <form wire:submit="save">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl leading-6 font-medium text-gray-900">
                                    Create New Ticket
                                </h3>
                                <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <!-- Error Message -->
                            @if ($errors->has('create'))
                                <div class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-circle text-red-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-red-800">{{ $errors->first('create') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Ticket Number Preview -->
                            @if ($ticketNumberPreview)
                                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-ticket-alt text-blue-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-800">
                                                <strong>Ticket Number:</strong> {{ $ticketNumberPreview }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Form Fields -->
                            <div class="space-y-6">
                                <!-- Title -->
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700">
                                        Ticket Title <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="title" 
                                           wire:model="title" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('title') border-red-500 @enderror"
                                           placeholder="Brief description of the issue">
                                    @error('title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Project and Type Row -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Project -->
                                    <div>
                                        <label for="project_id" class="block text-sm font-medium text-gray-700">
                                            Project <span class="text-red-500">*</span>
                                        </label>
                                        <select id="project_id" 
                                                wire:model="project_id" 
                                                wire:change="updatedProjectId"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('project_id') border-red-500 @enderror">
                                            <option value="">Select a project</option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('project_id')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Type -->
                                    <div>
                                        <label for="type" class="block text-sm font-medium text-gray-700">
                                            Type <span class="text-red-500">*</span>
                                        </label>
                                        <select id="type" 
                                                wire:model="type" 
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('type') border-red-500 @enderror">
                                            @foreach ($typeOptions as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Priority and Assigned To Row -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Priority -->
                                    <div>
                                        <label for="priority" class="block text-sm font-medium text-gray-700">
                                            Priority <span class="text-red-500">*</span>
                                        </label>
                                        <select id="priority" 
                                                wire:model="priority" 
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('priority') border-red-500 @enderror">
                                            @foreach ($priorityOptions as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('priority')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Assigned To -->
                                    <div>
                                        <label for="assigned_to" class="block text-sm font-medium text-gray-700">
                                            Assigned To
                                        </label>
                                        <select id="assigned_to" 
                                                wire:model="assigned_to" 
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('assigned_to') border-red-500 @enderror">
                                            <option value="">Select assignee (optional)</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        @if (!$project_id)
                                            <p class="mt-1 text-xs text-gray-500">Select a project first to see available users</p>
                                        @endif
                                        @error('assigned_to')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">
                                        Initial Status <span class="text-red-500">*</span>
                                    </label>
                                    <select id="status" 
                                            wire:model="status" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('status') border-red-500 @enderror">
                                        @foreach ($statusOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700">
                                        Description <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="description" 
                                              wire:model="description" 
                                              rows="6"
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('description') border-red-500 @enderror"
                                              placeholder="Detailed description of the issue, steps to reproduce, expected behavior, etc..."></textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">{{ strlen($description) }} characters (minimum 10)</p>
                                </div>

                                <!-- Priority Indicator -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Priority Guidelines</h4>
                                    <div class="space-y-2 text-xs text-gray-600">
                                        <div class="flex items-center">
                                            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                            <strong>Low:</strong> Minor issues, cosmetic problems, nice-to-have features
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></span>
                                            <strong>Medium:</strong> Important issues that don't block core functionality
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-3 h-3 bg-orange-500 rounded-full mr-2"></span>
                                            <strong>High:</strong> Major issues affecting core functionality
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                            <strong>Urgent:</strong> Critical issues, system down, data loss, security breaches
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                            wire:loading.attr="disabled">
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin mr-2"></i>Creating...
                        </span>
                        <span wire:loading.remove>
                            <i class="fas fa-plus mr-2"></i>Create Ticket
                        </span>
                    </button>
                    <button type="button" 
                            wire:click="closeModal" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
