<div>
    <x-slot name="title">Claim Details - {{ $claim->claim_number }}</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Claim Details</h1>
                    <p class="mt-2 text-gray-600">{{ $claim->claim_number }}</p>
                </div>
                <div class="flex space-x-3">
                    @if(Auth::user()->isApprover() && $claim->status === 'pending_approval' && $claim->user->manager_id === Auth::id())
                        <button wire:click="approveClaim"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Approve Claim
                        </button>
                        <button wire:click="$set('showRejectModal', true)"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Reject Claim
                        </button>
                    @endif

                    @if(Auth::user()->isHRAdmin() && $claim->status === 'approved')
                        <button wire:click="$set('showProcessModal', true)"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Process Claim
                        </button>
                    @endif

                    @if(Auth::user()->isPayroll() && $claim->status === 'processed')
                        <button wire:click="markAsPaid"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Mark as Paid
                        </button>
                    @endif

                    <a href="javascript:history.back()"
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Claim Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Claim Information</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Claim Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $claim->claim_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($claim->status === 'approved') bg-green-100 text-green-800
                                        @elseif($claim->status === 'pending_approval') bg-yellow-100 text-yellow-800
                                        @elseif($claim->status === 'processed') bg-blue-100 text-blue-800
                                        @elseif($claim->status === 'paid') bg-green-100 text-green-800
                                        @elseif($claim->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $claim->status)) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Duty Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $claim->duty_date->format('M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Overtime Hours</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($claim->overtime_hours, 1) }} hours</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Overtime Rate</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ \App\Models\SystemSetting::formatCurrency($claim->overtime_rate) }}/hour</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Meal Allowance</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ \App\Models\SystemSetting::formatCurrency($claim->meal_allowance) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">{{ \App\Models\SystemSetting::formatCurrency($claim->total_amount) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Submitted Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $claim->submitted_at ? $claim->submitted_at->format('M d, Y g:i A') : 'Not submitted' }}
                                </dd>
                            </div>
                        </dl>

                        @if($claim->description)
                            <div class="mt-6">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-2 text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ $claim->description }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Documents -->
                @if($claim->documents->count() > 0)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Supporting Documents</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="space-y-3">
                                @foreach($claim->documents as $document)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                                        <div class="flex items-center space-x-3">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $document->original_name }}</p>
                                                <p class="text-sm text-gray-500">{{ number_format($document->file_size / 1024, 1) }} KB</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('documents.view', $document) }}"
                                           target="_blank"
                                           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            View
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Employee Information -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Employee Information</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $claim->user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $claim->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Department</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $claim->user->department->name ?? 'N/A' }}</dd>
                        </div>
                        @if($claim->user->manager)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Manager</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $claim->user->manager->name }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Processing History -->
                @if($claim->approved_at || $claim->processed_at || $claim->paid_at || $claim->rejection_reason)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Processing History</h3>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            @if($claim->approved_at)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 {{ $claim->status === 'rejected' ? 'bg-red-100' : 'bg-green-100' }} rounded-full flex items-center justify-center">
                                            @if($claim->status === 'rejected')
                                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $claim->status === 'rejected' ? 'Rejected' : 'Approved' }} by {{ $claim->approver->name ?? 'N/A' }}
                                        </p>
                                        <p class="text-sm text-gray-500">{{ $claim->approved_at->format('M d, Y g:i A') }}</p>
                                        @if($claim->approval_remarks)
                                            <p class="text-sm text-gray-700 mt-1">{{ $claim->approval_remarks }}</p>
                                        @endif
                                        @if($claim->rejection_reason)
                                            <p class="text-sm text-red-700 mt-1"><strong>Reason:</strong> {{ $claim->rejection_reason }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($claim->processed_at)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Processed for payment</p>
                                        <p class="text-sm text-gray-500">{{ $claim->processed_at->format('M d, Y g:i A') }}</p>
                                        @if($claim->process_remarks)
                                            <p class="text-sm text-gray-700 mt-1">{{ $claim->process_remarks }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($claim->paid_at)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Payment completed</p>
                                        <p class="text-sm text-gray-500">{{ $claim->paid_at->format('M d, Y g:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Quick Stats -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Quick Stats</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $claim->created_at->format('M d, Y g:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $claim->updated_at->format('M d, Y g:i A') }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Process Modal -->
    @if($showProcessModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="$set('showProcessModal', false)">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Process Claim</h3>
                    <div class="mb-4">
                        <label for="processRemarks" class="block text-sm font-medium text-gray-700 mb-2">Processing Remarks (Optional)</label>
                        <textarea wire:model="processRemarks"
                                  id="processRemarks"
                                  rows="3"
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Add any processing notes..."></textarea>
                    </div>
                    <div class="flex space-x-3">
                        <button wire:click="processClaim"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm font-medium">
                            Process Claim
                        </button>
                        <button wire:click="$set('showProcessModal', false)"
                                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-md text-sm font-medium">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Modal -->
    @if($showRejectModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="$set('showRejectModal', false)">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Claim</h3>
                    <div class="mb-4">
                        <label for="rejectionReason" class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason</label>
                        <textarea wire:model="rejectionReason"
                                  id="rejectionReason"
                                  rows="3"
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                  placeholder="Please provide a reason for rejection..."
                                  required></textarea>
                    </div>
                    <div class="flex space-x-3">
                        <button wire:click="rejectClaim"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md text-sm font-medium">
                            Reject Claim
                        </button>
                        <button wire:click="$set('showRejectModal', false)"
                                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-md text-sm font-medium">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
