<div class="space-y-8">
    <!-- Chart Controls -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Advanced Analytics</h3>
                <p class="text-sm text-gray-600">Interactive charts and detailed insights</p>
            </div>
            
            <div class="flex flex-wrap gap-3 items-center">
                <!-- Chart Type Selector -->
                <div class="bg-gray-50 rounded-lg p-1 flex border border-gray-200">
                    <button wire:click="updateChartType('line')" 
                            class="{{ $chartType === 'line' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-white' }} px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 flex items-center">
                        <i class="fas fa-chart-line mr-2"></i>Line
                    </button>
                    <button wire:click="updateChartType('bar')" 
                            class="{{ $chartType === 'bar' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-white' }} px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i>Bar
                    </button>
                    <button wire:click="updateChartType('area')" 
                            class="{{ $chartType === 'area' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-white' }} px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 flex items-center">
                        <i class="fas fa-chart-area mr-2"></i>Area
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Ticket Trends Chart -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 lg:col-span-2">
            <div class="px-6 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">Ticket Trends</h4>
                        <p class="text-sm text-gray-600 mt-1">Track ticket creation and resolution over time</p>
                    </div>
                    <div class="flex items-center space-x-6 text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2 shadow-sm"></div>
                            <span class="text-gray-700 font-medium">Created</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2 shadow-sm"></div>
                            <span class="text-gray-700 font-medium">Resolved</span>
                        </div>
                    </div>
                </div>
                <div class="h-80 relative bg-gradient-to-b from-gray-50 to-white rounded-xl p-4">
                    @if(count($ticketTrends['labels']) > 0)
                        <canvas id="ticketTrendsChart" class="w-full h-full"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-chart-line text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium">No trend data available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Project Performance Chart -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="px-6 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">Project Performance</h4>
                        <p class="text-sm text-gray-600 mt-1">Tickets by project resolution rate</p>
                    </div>
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-project-diagram text-white text-sm"></i>
                    </div>
                </div>
                <div class="h-64 relative bg-gradient-to-b from-gray-50 to-white rounded-xl p-4">
                    @if(count($projectPerformance['labels']) > 0)
                        <canvas id="projectPerformanceChart" class="w-full h-full"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-project-diagram text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium">No project data available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Priority Analysis Chart -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="px-6 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">Priority Analysis</h4>
                        <p class="text-sm text-gray-600 mt-1">Distribution by priority levels</p>
                    </div>
                    <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                    </div>
                </div>
                <div class="h-64 relative bg-gradient-to-b from-gray-50 to-white rounded-xl p-4">
                    @if(count($priorityAnalysis['labels']) > 0)
                        <canvas id="priorityAnalysisChart" class="w-full h-full"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-exclamation-triangle text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium">No priority data available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Response Time Analysis -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="px-6 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">Response Time Analysis</h4>
                        <p class="text-sm text-gray-600 mt-1">Ticket resolution time distribution</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($responseTimeAnalysis['average'] > 0)
                            <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                Avg: {{ $responseTimeAnalysis['average'] }}h
                            </div>
                        @endif
                        <div class="w-8 h-8 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-white text-sm"></i>
                        </div>
                    </div>
                </div>
                <div class="h-64 relative bg-gradient-to-b from-gray-50 to-white rounded-xl p-4">
                    @if(count($responseTimeAnalysis['labels']) > 0)
                        <canvas id="responseTimeChart" class="w-full h-full"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-clock text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium">No response time data available</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Workload Distribution -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="px-6 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">Workload Distribution</h4>
                        <p class="text-sm text-gray-600 mt-1">Team member ticket assignments</p>
                    </div>
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-white text-sm"></i>
                    </div>
                </div>
                <div class="h-64 relative bg-gradient-to-b from-gray-50 to-white rounded-xl p-4">
                    @if(count($workloadDistribution['labels']) > 0)
                        <canvas id="workloadChart" class="w-full h-full"></canvas>
                    @else
                        <div class="flex items-center justify-center h-full text-gray-500">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-users text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium">No workload data available</p>
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

            // Global chart options
            const defaultOptions = {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        usePointStyle: true,
                    }
                }
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
                                backgroundColor: '{{ $chartType === "area" ? "rgba(59, 130, 246, 0.1)" : "rgba(59, 130, 246, 0.6)" }}',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: '{{ $chartType === "area" ? "true" : "false" }}',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: chartColors.blue,
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                            },
                            {
                                label: 'Resolved',
                                data: @json($ticketTrends['resolved']),
                                borderColor: chartColors.green,
                                backgroundColor: '{{ $chartType === "area" ? "rgba(16, 185, 129, 0.1)" : "rgba(16, 185, 129, 0.6)" }}',
                                borderWidth: 3,
                                tension: 0.4,
                                fill: '{{ $chartType === "area" ? "true" : "false" }}',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: chartColors.green,
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                            }
                        ]
                    },
                    options: {
                        ...defaultOptions,
                        plugins: {
                            ...defaultOptions.plugins,
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
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
                                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                borderColor: chartColors.blue,
                                borderWidth: 2,
                                borderRadius: 6,
                                borderSkipped: false,
                            },
                            {
                                label: 'Resolved',
                                data: @json($projectPerformance['resolved']),
                                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                borderColor: chartColors.green,
                                borderWidth: 2,
                                borderRadius: 6,
                                borderSkipped: false,
                            }
                        ]
                    },
                    options: {
                        ...defaultOptions,
                        plugins: {
                            ...defaultOptions.plugins,
                            legend: {
                                ...defaultOptions.plugins.legend,
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
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
                            borderColor: '#fff',
                            borderWidth: 2,
                            hoverOffset: 4,
                        }]
                    },
                    options: {
                        ...defaultOptions,
                        plugins: {
                            ...defaultOptions.plugins,
                            legend: {
                                ...defaultOptions.plugins.legend,
                                position: 'right',
                                labels: {
                                    ...defaultOptions.plugins.legend.labels,
                                    padding: 20,
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        return data.labels.map((label, i) => ({
                                            text: label,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        }));
                                    }
                                }
                            }
                        },
                        cutout: '60%',
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
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(249, 115, 22, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                            ],
                            borderColor: [
                                chartColors.green,
                                chartColors.blue,
                                chartColors.yellow,
                                chartColors.orange,
                                chartColors.red,
                            ],
                            borderWidth: 2,
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        ...defaultOptions,
                        plugins: {
                            ...defaultOptions.plugins,
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
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
                    type: 'bar',
                    data: {
                        labels: @json($workloadDistribution['labels']),
                        datasets: [
                            {
                                label: 'Assigned',
                                data: @json($workloadDistribution['assigned']),
                                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                borderColor: chartColors.blue,
                                borderWidth: 2,
                                borderRadius: 6,
                                borderSkipped: false,
                            },
                            {
                                label: 'Resolved',
                                data: @json($workloadDistribution['resolved']),
                                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                borderColor: chartColors.green,
                                borderWidth: 2,
                                borderRadius: 6,
                                borderSkipped: false,
                            }
                        ]
                    },
                    options: {
                        ...defaultOptions,
                        indexAxis: 'y',
                        plugins: {
                            ...defaultOptions.plugins,
                            legend: {
                                ...defaultOptions.plugins.legend,
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    stepSize: 1
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
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
