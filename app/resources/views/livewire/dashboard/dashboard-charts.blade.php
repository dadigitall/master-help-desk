<div>
    <!-- Chart Controls -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <h3 class="text-lg font-medium text-gray-900">Advanced Analytics</h3>
        
        <div class="flex flex-wrap gap-2">
            <!-- Chart Type Selector -->
            <div class="inline-flex rounded-md shadow-sm" role="group">
                <button wire:click="updateChartType('line')" 
                        class="{{ $chartType === 'line' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} px-3 py-2 text-sm font-medium rounded-l-md border border-gray-300">
                    <i class="fas fa-chart-line mr-1"></i>Line
                </button>
                <button wire:click="updateChartType('bar')" 
                        class="{{ $chartType === 'bar' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} px-3 py-2 text-sm font-medium border-t border-b border-gray-300">
                    <i class="fas fa-chart-bar mr-1"></i>Bar
                </button>
                <button wire:click="updateChartType('area')" 
                        class="{{ $chartType === 'area' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} px-3 py-2 text-sm font-medium rounded-r-md border border-gray-300">
                    <i class="fas fa-chart-area mr-1"></i>Area
                </button>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Ticket Trends Chart -->
        <div class="bg-white shadow rounded-lg lg:col-span-2">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-md font-medium text-gray-900">Ticket Trends</h4>
                    <div class="flex items-center space-x-4 text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-gray-600">Created</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-gray-600">Resolved</span>
                        </div>
                    </div>
                </div>
                <div class="h-64 relative">
                    @if(count($ticketTrends['labels']) > 0)
                        <canvas id="ticketTrendsChart" class="w-full h-full"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <i class="fas fa-chart-line text-4xl mb-2"></i>
                                <p>No data available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Project Performance Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h4 class="text-md font-medium text-gray-900 mb-4">Project Performance</h4>
                <div class="h-64 relative">
                    @if(count($projectPerformance['labels']) > 0)
                        <canvas id="projectPerformanceChart" class="w-full h-full"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <i class="fas fa-project-diagram text-4xl mb-2"></i>
                                <p>No project data available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Priority Analysis Chart -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h4 class="text-md font-medium text-gray-900 mb-4">Priority Analysis</h4>
                <div class="h-64 relative">
                    @if(count($priorityAnalysis['labels']) > 0)
                        <canvas id="priorityAnalysisChart" class="w-full h-full"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <i class="fas fa-exclamation-triangle text-4xl mb-2"></i>
                                <p>No priority data available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Response Time Analysis -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-md font-medium text-gray-900">Response Time Analysis</h4>
                    @if($responseTimeAnalysis['average'] > 0)
                        <span class="text-sm text-gray-600">
                            Avg: {{ $responseTimeAnalysis['average'] }}h
                        </span>
                    @endif
                </div>
                <div class="h-64 relative">
                    @if(count($responseTimeAnalysis['labels']) > 0)
                        <canvas id="responseTimeChart" class="w-full h-full"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <i class="fas fa-clock text-4xl mb-2"></i>
                                <p>No response time data available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Workload Distribution -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h4 class="text-md font-medium text-gray-900 mb-4">Workload Distribution</h4>
                <div class="h-64 relative">
                    @if(count($workloadDistribution['labels']) > 0)
                        <canvas id="workloadChart" class="w-full h-full"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <i class="fas fa-users text-4xl mb-2"></i>
                                <p>No workload data available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            const chartColors = {
                blue: '#3B82F6',
                green: '#10B981',
                yellow: '#F59E0B',
                red: '#EF4444',
                purple: '#8B5CF6',
                orange: '#F97316',
                pink: '#EC4899',
                teal: '#14B8A6',
            };

            // Ticket Trends Chart
            @if(count($ticketTrends['labels']) > 0)
                new Chart(document.getElementById('ticketTrendsChart'), {
                    type: '{{ $chartType }}',
                    data: {
                        labels: @json($ticketTrends['labels']),
                        datasets: [
                            {
                                label: 'Created',
                                data: @json($ticketTrends['created']),
                                borderColor: chartColors.blue,
                                backgroundColor: '{{ $chartType === "area" ? "rgba(59, 130, 246, 0.1)" : "rgba(59, 130, 246, 0.8)" }}',
                                tension: 0.4,
                                fill: '{{ $chartType === "area" ? "true" : "false" }}',
                            },
                            {
                                label: 'Resolved',
                                data: @json($ticketTrends['resolved']),
                                borderColor: chartColors.green,
                                backgroundColor: '{{ $chartType === "area" ? "rgba(16, 185, 129, 0.1)" : "rgba(16, 185, 129, 0.8)" }}',
                                tension: 0.4,
                                fill: '{{ $chartType === "area" ? "true" : "false" }}',
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            @endif

            // Project Performance Chart
            @if(count($projectPerformance['labels']) > 0)
                new Chart(document.getElementById('projectPerformanceChart'), {
                    type: 'bar',
                    data: {
                        labels: @json($projectPerformance['labels']),
                        datasets: [
                            {
                                label: 'Total',
                                data: @json($projectPerformance['total']),
                                backgroundColor: chartColors.blue,
                            },
                            {
                                label: 'Resolved',
                                data: @json($projectPerformance['resolved']),
                                backgroundColor: chartColors.green,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            @endif

            // Priority Analysis Chart
            @if(count($priorityAnalysis['labels']) > 0)
                new Chart(document.getElementById('priorityAnalysisChart'), {
                    type: 'doughnut',
                    data: {
                        labels: @json($priorityAnalysis['labels']),
                        datasets: [{
                            data: @json($priorityAnalysis['total']),
                            backgroundColor: @json($priorityAnalysis['colors']),
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            @endif

            // Response Time Chart
            @if(count($responseTimeAnalysis['labels']) > 0)
                new Chart(document.getElementById('responseTimeChart'), {
                    type: 'bar',
                    data: {
                        labels: @json($responseTimeAnalysis['labels']),
                        datasets: [{
                            label: 'Tickets',
                            data: @json($responseTimeAnalysis['data']),
                            backgroundColor: [
                                chartColors.green,
                                chartColors.blue,
                                chartColors.yellow,
                                chartColors.orange,
                                chartColors.red,
                            ],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            @endif

            // Workload Distribution Chart
            @if(count($workloadDistribution['labels']) > 0)
                new Chart(document.getElementById('workloadChart'), {
                    type: 'horizontalBar',
                    data: {
                        labels: @json($workloadDistribution['labels']),
                        datasets: [
                            {
                                label: 'Assigned',
                                data: @json($workloadDistribution['assigned']),
                                backgroundColor: chartColors.blue,
                            },
                            {
                                label: 'Resolved',
                                data: @json($workloadDistribution['resolved']),
                                backgroundColor: chartColors.green,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            @endif

            // Handle chart type changes
            Livewire.on('chartTypeChanged', (type) => {
                // Charts will be re-rendered automatically when component updates
            });
        });
    </script>
</div>
