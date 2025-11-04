<div class="relative" x-data="{ open: false }">
    <!-- Notification Bell -->
    <button @click="open = !open; if(open) $wire.loadNotifications()"
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 rounded-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-4.5-4.5A7 7 0 1115 17zm0 0v3a2 2 0 01-4 0v-3m4 0H9"/>
        </svg>

        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="open"
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">

        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead"
                        class="text-xs text-primary-600 hover:text-primary-800">
                    Mark all as read
                </button>
            @endif
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0
                           {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50' }}">
                    <div class="flex items-start space-x-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0 mt-1">
                            @if(str_contains($notification->type, 'ClaimStatusUpdated'))
                                @if(isset($notification->data['status']) && $notification->data['status'] === 'approved')
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                @elseif(isset($notification->data['status']) && $notification->data['status'] === 'rejected')
                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                @else
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                @endif
                            @elseif(str_contains($notification->type, 'NewClaimSubmitted'))
                                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                            @else
                                <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="text-sm text-gray-900">
                                @if(str_contains($notification->type, 'ClaimStatusUpdated'))
                                    <span class="font-medium">Claim {{ $notification->data['claim_number'] ?? 'N/A' }}</span>
                                    @if(isset($notification->data['status']))
                                        @if($notification->data['status'] === 'approved')
                                            has been approved ✅
                                        @elseif($notification->data['status'] === 'rejected')
                                            has been rejected ❌
                                        @elseif($notification->data['status'] === 'processed')
                                            has been processed by HR ✅
                                        @elseif($notification->data['status'] === 'paid')
                                            has been paid 💰
                                        @else
                                            status updated to {{ ucfirst(str_replace('_', ' ', $notification->data['status'])) }}
                                        @endif
                                    @endif
                                @elseif(str_contains($notification->type, 'NewClaimSubmitted'))
                                    New claim from <span class="font-medium">{{ $notification->data['employee_name'] ?? 'N/A' }}</span> requires approval
                                @elseif(str_contains($notification->type, 'DeadlineReminder'))
                                    Claim submission deadline reminder: {{ $notification->data['days_until_deadline'] ?? 'N/A' }} days remaining
                                @else
                                    Notification received
                                @endif
                            </div>

                            @if(isset($notification->data['duty_date']))
                                <div class="text-xs text-gray-500 mt-1">
                                    Duty Date: {{ $notification->data['duty_date'] }}
                                </div>
                            @endif

                            @if(isset($notification->data['total_amount']))
                                <div class="text-xs text-gray-500">
                                    Amount: {{ \App\Models\SystemSetting::formatCurrency($notification->data['total_amount']) }}
                                </div>
                            @endif

                            @if(isset($notification->data['rejection_reason']))
                                <div class="text-xs text-red-600 mt-1">
                                    Reason: {{ $notification->data['rejection_reason'] }}
                                </div>
                            @endif

                            <div class="text-xs text-gray-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <!-- Mark as read button -->
                        @if(!$notification->read_at)
                            <button wire:click="markAsRead('{{ $notification->id }}')"
                                    class="flex-shrink-0 text-xs text-gray-400 hover:text-gray-600">
                                ×
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-4.5-4.5A7 7 0 1115 17zm0 0v3a2 2 0 01-4 0v-3m4 0H9"/>
                    </svg>
                    <p class="mt-2 text-sm">No notifications yet</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if($notifications && $notifications->count() >= 10)
            <div class="px-4 py-3 border-t border-gray-200 text-center">
                <a href="#" class="text-sm text-primary-600 hover:text-primary-800">
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>
