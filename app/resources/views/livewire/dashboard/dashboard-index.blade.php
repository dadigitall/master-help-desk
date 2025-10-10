<div>
    <!-- Header with Filters -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold text-gray-900 mb-4 sm:mb-0">Dashboard</h1>
            
            <!-- Period and Filter Controls -->
            <div class="flex flex-wrap gap-2">
                <!-- Period Selector -->
                <div class="inline-flex rounded-md shadow-sm" role="group">
                    <button wire:click="updatePeriod('day')" 
                            class="{{ $period === 'day' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} px-3 py-2 text-sm font-medium rounded-l-md border border-gray-300">
                        Day
                    </button>
                    <button wire:click="updatePeriod('week')" 
                            class="{{ $period === 'week' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} px-3 py-2 text-sm font-medium border-t border-b border-gray-300">
                        Week
                    </button>
                    <button wire:click="updatePeriod('month')" 
                            class="{{ $period === 'month' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} px-3 py-2 text-sm font-medium border-t border-b border-gray-300">
                        Month
                    </button>
                    <button wire:click="updatePeriod('year')" 
                            class="{{ $period === 'year' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} px-3 py-2 text-sm font-medium rounded-r-md border border-gray-300">
                        Year
                    </button>
                </div>

                <!-- Company Filter -->
                <select wire:model.live="selectedCompany" 
                        class="block w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>

                <!-- Project Filter -->
                <select wire:model.live="selectedProject" 
                        class="block w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>

                <!-- Refresh Button -->
                <button wire:click="$refresh" 
                        class="px-3 py-2 bg-white text-gray-700 border border-gray-300 rounded-md text-sm hover:bg-gray-50 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Overall Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Tickets -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-ticket-alt text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Tickets</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $overallStats['total_tickets']['current'] }}
                                </div>
                                @if($overallStats['total_tickets']['growth'] != 0)
                                    <div class="ml-2 flex items-baseline text-sm font-semibold">
                                        <span class="{{ $overallStats['total_tickets']['growth'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $overallStats['total_tickets']['growth'] > 0 ? '+' : '' }}{{ $overallStats['total_tickets']['growth'] }}%
                                        </span>
                                        <span class="text-gray-500 ml-1">vs last period</span>
                                    </div>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Open Tickets -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Open Tickets</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $overallStats['open_tickets']['current'] }}
                                </div>
                                @if($overallStats['open_tickets']['growth'] != 0)
                                    <div class="ml-2 flex items-baseline text-sm font-semibold">
                                        <span class="{{ $overallStats['open_tickets']['growth'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $overallStats['open_tickets']['growth'] > 0 ? '+' : '' }}{{ $overallStats['open_tickets']['growth'] }}%
                                        </span>
                                        <span class="text-gray-500 ml-1">vs last period</span>
                                    </div>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resolved Tickets -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-check-circle text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Resolved Tickets</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $overallStats['resolved_tickets']['current'] }}
                                </div>
                                @if($overallStats['resolved_tickets']['growth'] != 0)
                                    <div class="ml-2 flex items-baseline text-sm font-semibold">
                                        <span class="{{ $overallStats['resolved_tickets']['growth'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $overallStats['resolved_tickets']['growth'] > 0 ? '+' : '' }}{{ $overallStats['resolved_tickets']['growth'] }}%
                                        </span>
                                        <span class="text-gray-500 ml-1">vs last period</span>
                                    </div>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Avg Resolution Time -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <i class="fas fa-clock text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Avg Resolution Time</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $overallStats['avg_resolution_time']['current'] }}h
                                </div>
                                @if($overallStats['avg_resolution_time']['growth'] != 0)
                                    <div class="ml-2 flex items-baseline text-sm font-semibold">
                                        <span class="{{ $overallStats['avg_resolution_time']['growth'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $overallStats['avg_resolution_time']['growth'] > 0 ? '+' : '' }}{{ $overallStats['avg_resolution_time']['growth'] }}%
                                        </span>
                                        <span class="text-gray-500 ml-1">vs last period</span>
                                    </div>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Status Distribution Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Ticket Status Distribution</h3>
                <div class="mt-5">
                    @if($ticketStatusDistribution->count() > 0)
                        <div class="space-y-3">
                            @foreach($ticketStatusDistribution as $status)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $status['color'] }}"></div>
                                        <span class="text-sm font-medium text-gray-700">{{ $status['label'] }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 mr-2">{{ $status['count'] }}</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $status['percentage'] }}%</span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full" style="background-color: {{ $status['color'] }}; width: {{ $status['percentage'] }}%"></div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-chart-pie text-4xl mb-2"></i>
                            <p>No data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Priority Distribution Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Priority Distribution</h3>
                <div class="mt-5">
                    @if($priorityDistribution->count() > 0)
                        <div class="space-y-3">
                            @foreach($priorityDistribution as $priority)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $priority['color'] }}"></div>
                                        <span class="text-sm font-medium text-gray-700">{{ $priority['label'] }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 mr-2">{{ $priority['count'] }}</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $priority['percentage'] }}%</span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full" style="background-color: {{ $priority['color'] }}; width: {{ $priority['percentage'] }}%"></div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-chart-bar text-4xl mb-2"></i>
                            <p>No data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Chart and Top Performers -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Timeline Chart -->
        <div class="lg:col-span-2 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Ticket Timeline</h3>
                <div class="mt-5">
                    @if($timelineData->count() > 0)
                        <div class="relative h-64">
                            <div class="absolute inset-0 flex items-end justify-between">
                                @foreach($timelineData as $index => $data)
                                    <div class="flex-1 mx-1">
                                        <div class="bg-blue-500 hover:bg-blue-600 transition-colors duration-200 rounded-t relative group cursor-pointer"
                                             style="height: {{ $timelineData->max('count') > 0 ? ($data['count'] / $timelineData->max('count')) * 100 : 0 }}%"
                                             title="{{ $data['label'] }}: {{ $data['count'] }} tickets">
                                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                                                {{ $data['label'] }}: {{ $data['count'] }}
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500 text-center mt-2 truncate">{{ $data['label'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-chart-line text-4xl mb-2"></i>
                            <p>No data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Top Performers</h3>
                <div class="mt-5 space-y-3">
                    @if($topPerformers->count() > 0)
                        @foreach($topPerformers as $index => $performer)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-medium">{{ strtoupper(substr($performer['name'], 0, 1)) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $performer['name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $performer['avg_resolution_days'] }} days avg</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $performer['resolved_count'] }}</p>
                                    <p class="text-xs text-gray-500">resolved</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-trophy text-4xl mb-2"></i>
                            <p>No data available</p>
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
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Tickets</h3>
                <a href="{{ route('tickets') }}" class="text-sm text-blue-600 hover:text-blue-800">View all</a>
            </div>
            <div class="mt-5">
                @if($recentTickets->count() > 0)
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentTickets as $ticket)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $ticket['ticket_number'] }}</div>
                                                <div class="text-sm text-gray-500 truncate max-w-xs">{{ $ticket['title'] }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                                  style="background-color: {{ $ticket['status_color'] }}20; color: {{ $ticket['status_color'] }}">
                                                {{ $ticket['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                                  style="background-color: {{ $ticket['priority_color'] }}20; color: {{ $ticket['priority_color'] }}">
                                                {{ $ticket['priority'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $ticket['project_name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $ticket['assigned_to'] ?? 'Unassigned' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $ticket['created_at'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>No recent tickets</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
