<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">HR Reports & Analytics</h1>
        <p class="text-gray-600 mt-1">Comprehensive overview of claims and organizational insights</p>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                <select wire:model.live="selectedPeriod" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="today">Today</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                    <option value="last_month">Last Month</option>
                    <option value="this_quarter">This Quarter</option>
                    <option value="this_year">This Year</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            @if($selectedPeriod === 'custom')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" wire:model.live="dateFrom" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" wire:model.live="dateTo" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <select wire:model.live="departmentFilter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="statusFilter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending_approval">Pending Approval</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="processed">Processed</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex justify-between items-center">
            <div class="text-sm text-gray-500">
                Period: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
            </div>
            <button wire:click="exportData" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                Export Data
            </button>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Claims</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalClaims) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Amount</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\SystemSetting::formatCurrency($totalAmount) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending Claims</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($pendingClaims) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Avg Claim Amount</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\SystemSetting::formatCurrency($avgClaimAmount) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Overview & Department Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Claims by Status -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Claims by Status</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Pending Approval</span>
                    <div class="flex items-center">
                        <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                            <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $totalClaims > 0 ? ($pendingClaims / $totalClaims) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-medium">{{ $pendingClaims }}</span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Approved</span>
                    <div class="flex items-center">
                        <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $totalClaims > 0 ? ($approvedClaims / $totalClaims) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-medium">{{ $approvedClaims }}</span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Processed</span>
                    <div class="flex items-center">
                        <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $totalClaims > 0 ? ($processedClaims / $totalClaims) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-medium">{{ $processedClaims }}</span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Paid</span>
                    <div class="flex items-center">
                        <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $totalClaims > 0 ? ($paidClaims / $totalClaims) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-medium">{{ $paidClaims }}</span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Rejected</span>
                    <div class="flex items-center">
                        <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ $totalClaims > 0 ? ($rejectedClaims / $totalClaims) * 100 : 0 }}%"></div>
                        </div>
                        <span class="text-sm font-medium">{{ $rejectedClaims }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Processing Time -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Processing Metrics</h3>
            <div class="space-y-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600">{{ $avgProcessingTime }}</div>
                    <div class="text-sm text-gray-500">Average Processing Days</div>
                </div>
                <div class="border-t pt-4">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-xl font-semibold text-green-600">{{ number_format((($approvedClaims + $processedClaims + $paidClaims) / max($totalClaims, 1)) * 100, 1) }}%</div>
                            <div class="text-xs text-gray-500">Approval Rate</div>
                        </div>
                        <div>
                            <div class="text-xl font-semibold text-red-600">{{ number_format(($rejectedClaims / max($totalClaims, 1)) * 100, 1) }}%</div>
                            <div class="text-xs text-gray-500">Rejection Rate</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Performance -->
    <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Department Performance</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Claims</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($departmentPerformance as $dept)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $dept['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dept['total_claims'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $dept['pending_claims'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $dept['pending_claims'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \App\Models\SystemSetting::formatCurrency($dept['total_amount']) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \App\Models\SystemSetting::formatCurrency($dept['avg_amount']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Claimants -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Top Claimants (by Amount)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Claims</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($topClaimants as $claimant)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $claimant['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $claimant['department'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $claimant['count'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \App\Models\SystemSetting::formatCurrency($claimant['amount']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
