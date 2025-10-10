<!-- Edit Project Modal -->
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
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <form wire:submit="save">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl leading-6 font-medium text-gray-900">
                                    Edit Project
                                </h3>
                                <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <!-- Error Message -->
                            @if ($errors->has('update'))
                                <div class="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-circle text-red-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-red-800">{{ $errors->first('update') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Form Fields -->
                            <div class="space-y-6">
                                <!-- Project Name -->
                                <div>
                                    <label for="edit_name" class="block text-sm font-medium text-gray-700">
                                        Project Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="edit_name" 
                                           wire:model="name" 
                                           wire:input="generatePrefix"
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-500 @enderror"
                                           placeholder="Enter project name">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Company Selection -->
                                <div>
                                    <label for="edit_company_id" class="block text-sm font-medium text-gray-700">
                                        Company <span class="text-red-500">*</span>
                                    </label>
                                    <select id="edit_company_id" 
                                            wire:model="company_id" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('company_id') border-red-500 @enderror">
                                        <option value="">Select a company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Prefix and Color Row -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Prefix -->
                                    <div>
                                        <label for="edit_prefix" class="block text-sm font-medium text-gray-700">
                                            Project Prefix <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <input type="text" 
                                                   id="edit_prefix" 
                                                   wire:model="prefix" 
                                                   class="flex-1 border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('prefix') border-red-500 @enderror"
                                                   placeholder="PRJ"
                                                   maxlength="10">
                                            <button type="button" 
                                                    wire:click="generatePrefix"
                                                    class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm hover:bg-gray-100">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                        @error('prefix')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1 text-xs text-gray-500">Used for ticket numbering (e.g., PRJ-001)</p>
                                    </div>

                                    <!-- Color -->
                                    <div>
                                        <label for="edit_color" class="block text-sm font-medium text-gray-700">
                                            Project Color <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <input type="color" 
                                                   id="edit_color" 
                                                   wire:model="color" 
                                                   class="h-10 w-20 border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('color') border-red-500 @enderror">
                                            <input type="text" 
                                                   wire:model="color" 
                                                   class="flex-1 border-gray-300 rounded-r-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('color') border-red-500 @enderror"
                                                   placeholder="#3B82F6">
                                            <button type="button" 
                                                    wire:click="randomColor"
                                                    class="ml-2 inline-flex items-center px-3 rounded-md border border-gray-300 bg-white text-gray-500 text-sm hover:bg-gray-50">
                                                <i class="fas fa-palette"></i>
                                            </button>
                                        </div>
                                        @error('color')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <p class="mt-1 text-xs text-gray-500">Visual identifier for the project</p>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="edit_description" class="block text-sm font-medium text-gray-700">
                                        Description
                                    </label>
                                    <textarea id="edit_description" 
                                              wire:model="description" 
                                              rows="4"
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('description') border-red-500 @enderror"
                                              placeholder="Describe the project goals and objectives..."></textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">{{ strlen($description) }}/1000 characters</p>
                                </div>

                                <!-- Active Status -->
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               wire:model="is_active" 
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">Active</span>
                                    </label>
                                    <p class="mt-1 text-xs text-gray-500">Inactive projects won't be available for new tickets</p>
                                </div>

                                <!-- Preview -->
                                @if ($name)
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="text-sm font-medium text-gray-700 mb-3">Preview</h4>
                                        <div class="flex items-center">
                                            <div class="h-12 w-12 rounded-lg flex items-center justify-center" style="background-color: {{ $color }}20;">
                                                <span class="text-lg font-bold" style="color: {{ $color }};">{{ strtoupper(substr($name, 0, 1)) }}</span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-lg font-medium text-gray-900">{{ $name }}</div>
                                                <div class="text-sm text-gray-500">{{ $prefix }}</div>
                                            </div>
                                            <div class="ml-auto">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Current Stats (if project exists) -->
                                @if ($project)
                                    <div class="bg-blue-50 rounded-lg p-4">
                                        <h4 class="text-sm font-medium text-blue-900 mb-3">Current Project Stats</h4>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                                            <div>
                                                <div class="text-2xl font-bold text-blue-900">{{ $project->tickets_count ?? 0 }}</div>
                                                <div class="text-xs text-blue-700">Total Tickets</div>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-bold text-orange-600">{{ $project->tickets()->where('status', 'open')->count() }}</div>
                                                <div class="text-xs text-blue-700">Open Tickets</div>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-bold text-green-600">{{ $project->tickets()->where('status', 'closed')->count() }}</div>
                                                <div class="text-xs text-blue-700">Closed Tickets</div>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-bold text-purple-600">{{ $project->created_at->format('M d') }}</div>
                                                <div class="text-xs text-blue-700">Created</div>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-xs text-blue-700">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Changing the prefix will affect new ticket numbering only
                                        </div>
                                    </div>
                                @endif
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
                            <i class="fas fa-spinner fa-spin mr-2"></i>Updating...
                        </span>
                        <span wire:loading.remove>
                            <i class="fas fa-save mr-2"></i>Update Project
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
