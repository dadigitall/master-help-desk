<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Header with Filters -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">Dashboard Analytics</h1>
                <p class="text-gray-600">Real-time insights and performance metrics</p>
            </div>
            
            <!-- Period and Filter Controls -->
            <div class="flex flex-wrap gap-3 items-center">
                <!-- Period Selector -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-1 flex">
                    <button wire:click="updatePeriod('day')" 
                            class="{{ $period === 'day' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} px-4 py-2 text-sm font-medium rounded-md transition-all duration-200">
                        Day
                    </button>
                    <button wire:click="updatePeriod('week')" 
                            class="{{ $period === 'week' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} px-4 py-2 text-sm font-medium rounded-md transition-all duration-200">
                        Week
                    </button>
                    <button wire:click="updatePeriod('month')" 
                            class="{{ $period === 'month' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} px-4 py-2 text-sm font-medium rounded-md transition-all duration-200">
                        Month
                    </button>
                    <button wire:click="updatePeriod('year')" 
                            class="{{ $period === 'year' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }} px-4 py-2 text-sm font-medium rounded-md transition-all duration-200">
                        Year
                    </button>
                </div>

                <!-- Company Filter -->
                <select wire:model.live="selectedCompany" 
                        class="bg-white border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>

                <!-- Project Filter -->
                <select wire:model.live="selectedProject" 
                        class="bg-white border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>

                <!-- Refresh Button -->
                <button wire:click="$refresh" 
                        class="bg-white text-gray-600 border border-gray-200 rounded-lg px-4 py-2 text-sm hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm transition-all duration-200">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Overall Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Tickets -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-ticket-alt text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($overallStats['total_tickets']['growth'] != 0)
                            <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $overallStats['total_tickets']['growth'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas fa-arrow-{{ $overallStats['total_tickets']['growth'] > 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ $overallStats['total_tickets']['growth'] > 0 ? '+' : '' }}{{ $overallStats['total_tickets']['growth'] }}%
                            </div>
                        @endif
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Total Tickets</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $overallStats['total_tickets']['current'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">vs last period</p>
                </div>
            </div>
        </div>

        <!-- Open Tickets -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-exclamation-circle text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($overallStats['open_tickets']['growth'] != 0)
                            <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $overallStats['open_tickets']['growth'] > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                <i class="fas fa-arrow-{{ $overallStats['open_tickets']['growth'] > 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ $overallStats['open_tickets']['growth'] > 0 ? '+' : '' }}{{ $overallStats['open_tickets']['growth'] }}%
                            </div>
                        @endif
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Open Tickets</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $overallStats['open_tickets']['current'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">vs last period</p>
                </div>
            </div>
        </div>

        <!-- Resolved Tickets -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-check-circle text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($overallStats['resolved_tickets']['growth'] != 0)
                            <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $overallStats['resolved_tickets']['growth'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas fa-arrow-{{ $overallStats['resolved_tickets']['growth'] > 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ $overallStats['resolved_tickets']['growth'] > 0 ? '+' : '' }}{{ $overallStats['resolved_tickets']['growth'] }}%
                            </div>
                        @endif
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Resolved Tickets</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $overallStats['resolved_tickets']['current'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">vs last period</p>
                </div>
            </div>
        </div>

        <!-- Avg Resolution Time -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-clock text-white text-lg"></i>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($overallStats['avg_resolution_time']['growth'] != 0)
                            <div class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $overallStats['avg_resolution_time']['growth'] > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                <i class="fas fa-arrow-{{ $overallStats['avg_resolution_time']['growth'] > 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ $overallStats['avg_resolution_time']['growth'] > 0 ? '+' : '' }}{{ $overallStats['avg_resolution_time']['growth'] }}%
                            </div>
                        @endif
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Avg Resolution Time</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $overallStats['avg_resolution_time']['current'] }}h</p>
                    <p class="text-xs text-gray-500 mt-1">vs last period</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Status Distribution Chart -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="px-6 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Status Distribution</h3>
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-pie text-white text-sm"></i>
                    </div>
                </div>
                <div class="mt-6">
                    @if($ticketStatusDistribution->count() > 0)
                        <div class="space-y-4">
                            @foreach($ticketStatusDistribution as $status)
                                <div class="group">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded-full mr-3 shadow-sm" style="background-color: {{ $status['color'] }}"></div>
                                            <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">{{ $status['label'] }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-500">{{ $status['count'] }}</span>
                                            <span class="text-sm font-semibold text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ $status['percentage'] }}%</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                        <div class="h-3 rounded-full transition-all duration-500 ease-out relative overflow-hidden" 
                                             style="background-color: {{ $status['color'] }}; width: {{ $status['percentage'] }}%">
                                            <div class="absolute inset-0 bg-white opacity-20 animate-pulse"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-chart-pie text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-sm">No data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Priority Distribution Chart -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="px-6 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Priority Distribution</h3>
                    <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                    </div>
                </div>
                <div class="mt-6">
                    @if($priorityDistribution->count() > 0)
                        <div class="space-y-4">
                            @foreach($priorityDistribution as $priority)
                                <div class="group">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 rounded-full mr-3 shadow-sm" style="background-color: {{ $priority['color'] }}"></div>
                                            <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900 transition-colors">{{ $priority['label'] }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-500">{{ $priority['count'] }}</span>
                                            <span class="text-sm font-semibold text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ $priority['percentage'] }}%</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                        <div class="h-3 rounded-full transition-all duration-500 ease-out relative overflow-hidden" 
                                             style="background-color: {{ $priority['color'] }}; width: {{ $priority['percentage'] }}%">
                                            <div class="absolute inset-0 bg-white opacity-20 animate-pulse"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-chart-bar text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-sm">No data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Chart and Top Performers -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Timeline Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="px-6 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Ticket Timeline</h3>
                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-teal-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-sm"></i>
                    </div>
                </div>
                <div class="mt-6">
                    @if($timelineData->count() > 0)
                        <div class="relative h-80 bg-gradient-to-b from-gray-50 to-white rounded-xl p-4">
                            <div class="absolute inset-0 flex items-end justify-between px-2">
                                @foreach($timelineData as $index => $data)
                                    <div class="flex-1 mx-0.5">
                                        <div class="bg-gradient-to-t from-blue-500 to-blue-400 hover:from-blue-600 hover:to-blue-500 transition-all duration-300 rounded-t relative group cursor-pointer shadow-lg hover:shadow-xl transform hover:-translate-y-1"
                                             style="height: {{ $timelineData->max('count') > 0 ? ($data['count'] / $timelineData->max('count')) * 100 : 0 }}%">
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-300 whitespace-nowrap shadow-xl z-10">
                                                <div class="font-semibold">{{ $data['label'] }}</div>
                                                <div class="text-blue-300">{{ $data['count'] }} tickets</div>
                                                <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1 w-2 h-2 bg-gray-900 rotate-45"></div>
                                            </div>
                                            @if($data['count'] > 0)
                                                <div class="absolute top-2 left-1/2 transform -translate-x-1/2 text-white text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                    {{ $data['count'] }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-600 text-center mt-3 font-medium truncate">{{ $data['label'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-16 text-gray-500">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-chart-line text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-sm font-medium">No timeline data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="px-6 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Top Performers</h3>
                    <div class="w-8 h-8 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-trophy text-white text-sm"></i>
                    </div>
                </div>
                <div class="mt-6 space-y-3">
                    @if($topPerformers->count() > 0)
                        @foreach($topPerformers as $index => $performer)
                            <div class="group flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 relative">
                                        <div class="w-10 h-10 bg-gradient-to-br {{ $index === 0 ? 'from-yellow-400 to-yellow-600' : ($index === 1 ? 'from-gray-300 to-gray-500' : ($index === 2 ? 'from-orange-300 to-orange-500' : 'from-blue-400 to-blue-600')) }} rounded-xl flex items-center justify-center shadow-md">
                                            <span class="text-white text-sm font-bold">{{ strtoupper(substr($performer['name'], 0, 1)) }}</span>
                                        </div>
                                        @if($index === 0)
                                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-yellow-400 rounded-full animate-pulse"></div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $performer['name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $performer['avg_resolution_days'] }} days avg</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-gray-900">{{ $performer['resolved_count'] }}</p>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">resolved</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-16 text-gray-500">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-trophy text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-sm font-medium">No performers data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Charts Section -->
    <div class="mb-6">
        <livewire:dashboard.dashboard-charts />
    </div>

    <!-- Recent Tickets -->
    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
        <div class="px-6 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Recent Tickets</h3>
                    <p class="text-sm text-gray-600 mt-1">Latest ticket updates and activities</p>
                </div>
                <a href="{{ route('tickets') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                    View all
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="mt-6">
                @if($recentTickets->count() > 0)
                    <div class="overflow-hidden rounded-xl border border-gray-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ticket</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Priority</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Project</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Assigned</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Created</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($recentTickets as $ticket)
                                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-all duration-200 group">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                            {{ substr($ticket['ticket_number'], -3) }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ $ticket['ticket_number'] }}</div>
                                                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ $ticket['title'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border shadow-sm" 
                                                      style="background-color: {{ $ticket['status_color'] }}10; color: {{ $ticket['status_color'] }}; border-color: {{ $ticket['status_color'] }}30">
                                                    <div class="w-1.5 h-1.5 rounded-full mr-1.5" style="background-color: {{ $ticket['status_color'] }}"></div>
                                                    {{ $ticket['status'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border shadow-sm" 
                                                      style="background-color: {{ $ticket['priority_color'] }}10; color: {{ $ticket['priority_color'] }}; border-color: {{ $ticket['priority_color'] }}30">
                                                    <div class="w-1.5 h-1.5 rounded-full mr-1.5" style="background-color: {{ $ticket['priority_color'] }}"></div>
                                                    {{ $ticket['priority'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-6 h-6 bg-gradient-to-br from-purple-500 to-purple-600 rounded flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                        {{ strtoupper(substr($ticket['project_name'], 0, 1)) }}
                                                    </div>
                                                    <span class="ml-2 text-sm text-gray-900 font-medium">{{ $ticket['project_name'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($ticket['assigned_to'])
                                                    <div class="flex items-center">
                                                        <div class="w-6 h-6 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                            {{ strtoupper(substr($ticket['assigned_to'], 0, 1)) }}
                                                        </div>
                                                        <span class="ml-2 text-sm text-gray-900 font-medium">{{ $ticket['assigned_to'] }}</span>
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                        <i class="fas fa-user-slash mr-1"></i>
                                                        Unassigned
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center text-sm text-gray-500">
                                                    <i class="fas fa-clock mr-2 text-gray-400"></i>
                                                    {{ $ticket['created_at'] }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="text-center py-16 text-gray-500">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-3xl text-gray-400"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-600">No recent tickets</p>
                        <p class="text-xs text-gray-500 mt-1">New tickets will appear here</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
