<!-- View Ticket Modal -->
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
            <div class="bg-white">
                <!-- Header -->
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-xl font-medium text-gray-900">
                                Ticket {{ $ticket?->ticket_number ?? '' }}
                            </h3>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($ticket?->priority === 'urgent') bg-red-100 text-red-800
                                @elseif($ticket?->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($ticket?->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ $priorityOptions[$ticket->priority ?? ''] ?? ucfirst($ticket->priority ?? '') }}
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($ticket?->status === 'open') bg-red-100 text-red-800
                                @elseif($ticket?->status === 'in_progress') bg-yellow-100 text-yellow-800
                                @elseif($ticket?->status === 'pending') bg-gray-100 text-gray-800
                                @elseif($ticket?->status === 'resolved') bg-blue-100 text-blue-800
                                @elseif($ticket?->status === 'closed') bg-green-100 text-green-800
                                @else bg-orange-100 text-orange-800
                                @endif">
                                {{ $statusOptions[$ticket->status ?? ''] ?? ucfirst($ticket->status ?? '') }}
                            </span>
                        </div>
                        <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px">
                        <button wire:click="setActiveTab('details')" 
                                class="{{ $activeTab === 'details' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-2 px-6 text-sm font-medium border-b-2">
                            <i class="fas fa-info-circle mr-2"></i>Details
                        </button>
                        <button wire:click="setActiveTab('comments')" 
                                class="{{ $activeTab === 'comments' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-2 px-6 text-sm font-medium border-b-2">
                            <i class="fas fa-comments mr-2"></i>Comments ({{ $ticket?->comments->count() ?? 0 }})
                        </button>
                        <button wire:click="setActiveTab('chat')" 
                                class="{{ $activeTab === 'chat' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-2 px-6 text-sm font-medium border-b-2">
                            <i class="fas fa-comment-dots mr-2"></i>Chat ({{ $ticket?->chats->count() ?? 0 }})
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="max-h-96 overflow-y-auto">
                    @if ($activeTab === 'details')
                        <!-- Details Tab -->
                        <div class="p-6">
                            @if ($ticket)
                                <!-- Ticket Header -->
                                <div class="mb-6">
                                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $ticket->title }}</h2>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <div class="h-6 w-6 rounded flex items-center justify-center" style="background-color: {{ $ticket->project->color }}20;">
                                                <span class="text-xs font-bold" style="color: {{ $ticket->project->color }};">{{ strtoupper(substr($ticket->project->name, 0, 1)) }}</span>
                                            </div>
                                            <span class="ml-2">{{ $ticket->project->name }}</span>
                                        </div>
                                        <span>Created {{ $ticketStats['time_ago'] }}</span>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                                    <div class="prose max-w-none">
                                        <p class="whitespace-pre-wrap text-gray-700">{{ $ticket->description }}</p>
                                    </div>
                                </div>

                                <!-- Quick Actions (if can edit) -->
                                @if ($canEdit)
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                        <!-- Status -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                            <select wire:change="updateTicketStatus($event.target.value)" 
                                                    class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                                @foreach ($statusOptions as $value => $label)
                                                    <option value="{{ $value }}" {{ $ticket->status === $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Priority -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                            <select wire:change="updateTicketPriority($event.target.value)" 
                                                    class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                                @foreach ($priorityOptions as $value => $label)
                                                    <option value="{{ $value }}" {{ $ticket->priority === $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Assignment -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                                            <select wire:change="assignTicket($event.target.value)" 
                                                    class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Unassigned</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}" {{ $ticket->assigned_to === $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif

                                <!-- Ticket Information -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Ticket Information</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">Created:</span>
                                            <p class="font-medium">{{ $ticketStats['created_at'] }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Updated:</span>
                                            <p class="font-medium">{{ $ticketStats['updated_at'] }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Created By:</span>
                                            <p class="font-medium">{{ $ticketStats['created_by'] }}</p>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Assigned:</span>
                                            <p class="font-medium">{{ $ticket->assignedUser?->name ?? 'Unassigned' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @elseif ($activeTab === 'comments')
                        <!-- Comments Tab -->
                        <div class="p-6">
                            @if ($ticket)
                                <!-- Add Comment Form -->
                                @if ($canComment)
                                    <div class="mb-6">
                                        <form wire:submit="addComment">
                                            <div class="mb-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Add Comment</label>
                                                <textarea wire:model="newComment" 
                                                          rows="3"
                                                          class="w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                                          placeholder="Share your thoughts about this ticket..."></textarea>
                                                @error('newComment')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <button type="submit" 
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <i class="fas fa-paper-plane mr-2"></i>Post Comment
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                <!-- Comments List -->
                                <div class="space-y-4">
                                    @if ($ticket->comments->count() > 0)
                                        @foreach ($ticket->comments as $comment)
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex items-start space-x-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                                                <span class="text-white text-sm font-medium">
                                                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="flex items-center space-x-2 mb-1">
                                                                <span class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</span>
                                                                <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                                            </div>
                                                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $comment->content }}</p>
                                                        </div>
                                                    </div>
                                                    @if ($comment->user_id === auth()->id() || auth()->user()->hasRole('admin'))
                                                        <button wire:click="deleteComment({{ $comment->id }})" 
                                                                wire:confirm="Are you sure you want to delete this comment?"
                                                                class="text-red-500 hover:text-red-700 text-sm">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-8 text-gray-500">
                                            <i class="fas fa-comments text-4xl mb-2"></i>
                                            <p>No comments yet. Be the first to comment!</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @elseif ($activeTab === 'chat')
                        <!-- Chat Tab -->
                        <div class="p-6">
                            @if ($ticket)
                                <!-- Chat Messages -->
                                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                                    @if ($ticket->chats->count() > 0)
                                        @foreach ($ticket->chats as $chat)
                                            <div class="flex items-start space-x-3 {{ $chat->user_id === auth()->id() ? 'justify-end' : '' }}">
                                                @if ($chat->user_id !== auth()->id())
                                                    <div class="flex-shrink-0">
                                                        <div class="h-6 w-6 rounded-full bg-green-500 flex items-center justify-center">
                                                            <span class="text-white text-xs font-medium">
                                                                {{ strtoupper(substr($chat->user->name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="flex-1 max-w-xs">
                                                    <div class="{{ $chat->user_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-900' }} rounded-lg px-3 py-2">
                                                        <p class="text-sm">{{ $chat->message }}</p>
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1 {{ $chat->user_id === auth()->id() ? 'text-right' : '' }}">
                                                        {{ $chat->user->name }} • {{ $chat->created_at->format('H:i') }}
                                                    </p>
                                                </div>
                                                @if ($chat->user_id === auth()->id())
                                                    <div class="flex-shrink-0">
                                                        <div class="h-6 w-6 rounded-full bg-blue-500 flex items-center justify-center">
                                                            <span class="text-white text-xs font-medium">
                                                                {{ strtoupper(substr($chat->user->name, 0, 1)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-8 text-gray-500">
                                            <i class="fas fa-comment-dots text-4xl mb-2"></i>
                                            <p>No messages yet. Start the conversation!</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Chat Input -->
                                @if ($canChat)
                                    <form wire:submit="addChatMessage" class="border-t pt-4">
                                        <div class="flex space-x-2">
                                            <input type="text" 
                                                   wire:model="newChatMessage" 
                                                   class="flex-1 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                                   placeholder="Type your message...">
                                            <button type="submit" 
                                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </div>
                                        @error('newChatMessage')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </form>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Modal Actions -->
                <div class="bg-gray-50 px-6 py-3 sm:flex sm:flex-row-reverse">
                    @if ($canEdit)
                        <button wire:click="$dispatch('openEditTicketModal', {{ $ticket?->id }})" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            <i class="fas fa-edit mr-2"></i>Edit Ticket
                        </button>
                    @endif
                    <button type="button" 
                            wire:click="closeModal" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
