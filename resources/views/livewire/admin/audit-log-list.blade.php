<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-white shadow-sm rounded-lg">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Audit Logs</h1>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Total: {{ $auditLogs->total() }} entries</span>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" wire:model.live="search" placeholder="Search logs..."
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <!-- Event Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Event</label>
                    <select wire:model.live="eventFilter"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event }}">{{ ucfirst($event) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- User Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <select wire:model.live="userFilter"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Model Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                    <select wire:model.live="modelFilter"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Models</option>
                        @foreach($models as $model)
                            <option value="{{ $model }}">{{ class_basename($model) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Date Filters -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" wire:model.live="dateFrom"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" wire:model.live="dateTo"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="flex items-end">
                    <button wire:click="resetFilters" type="button"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Audit Logs Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date/Time
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Event
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Model
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            IP Address
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                                <div class="text-xs text-gray-500">
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->user)
                                    <div class="text-sm font-medium text-gray-900">{{ $log->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $log->user->role }}</div>
                                @else
                                    <span class="text-sm text-gray-500">System</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($log->event === 'created') bg-green-100 text-green-800
                                    @elseif($log->event === 'updated') bg-blue-100 text-blue-800
                                    @elseif($log->event === 'deleted') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($log->event) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ class_basename($log->auditable_type) }}
                                @if($log->auditable_id)
                                    <div class="text-xs text-gray-500">ID: {{ $log->auditable_id }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="max-w-xs truncate" title="{{ $log->notes }}">
                                    {{ $log->notes }}
                                </div>
                                @if($log->old_values || $log->new_values)
                                    <button class="text-xs text-primary-600 hover:text-primary-800 mt-1"
                                            onclick="toggleDetails({{ $log->id }})">
                                        View Changes
                                    </button>
                                    <div id="details-{{ $log->id }}" class="hidden mt-2 p-2 bg-gray-50 rounded text-xs">
                                        @if($log->old_values)
                                            <div class="mb-2">
                                                <strong>Previous Values:</strong>
                                                <pre class="text-xs">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        @endif
                                        @if($log->new_values)
                                            <div>
                                                <strong>New Values:</strong>
                                                <pre class="text-xs">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->ip_address }}
                                @if($log->user_agent)
                                    <div class="text-xs text-gray-400 max-w-xs truncate" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No audit logs found</p>
                                    <p class="text-sm">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($auditLogs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleDetails(id) {
    const details = document.getElementById('details-' + id);
    details.classList.toggle('hidden');
}
</script>
