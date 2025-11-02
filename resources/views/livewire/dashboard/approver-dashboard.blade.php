<div>
    <x-slot name="title">Approver Dashboard</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Approver Dashboard</h1>
            <p class="mt-2 text-gray-600">Review and approve your team's overtime and meal allowance claims</p>
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

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Pending Approvals -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Approvals</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalPending }}</p>
                    </div>
                </div>
            </div>

            <!-- Approved This Month -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Approved This Month</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalApprovedThisMonth }}</p>
                    </div>
                </div>
            </div>

            <!-- Team Overtime Hours -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Team Hours (This Month)</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalOvertimeHoursThisMonth, 1) }}h</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Claims Section -->
        <div class="mb-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Pending Approvals
                        @if($totalPending > 0)
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ $totalPending }} pending
                            </span>
                        @endif
                    </h3>
                </div>

                @if($pendingClaims->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($pendingClaims as $claim)
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-lg font-medium text-gray-900">{{ $claim->claim_number }}</h4>
                                                <p class="text-sm text-gray-600">{{ $claim->user->name }} ({{ $claim->user->employee_id }})</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-lg font-semibold text-gray-900">{{ \App\Models\SystemSetting::formatCurrency($claim->total_amount) }}</p>
                                                <p class="text-sm text-gray-500">{{ number_format($claim->overtime_hours, 1) }} hours</p>
                                            </div>
                                        </div>

                                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                            <div>
                                                <p><strong>Duty Date:</strong> {{ $claim->duty_date->format('M d, Y') }}</p>
                                                <p><strong>Time:</strong> {{ $claim->start_time->format('H:i') }} - {{ $claim->end_time->format('H:i') }}</p>
                                                <p><strong>Work Type:</strong> {{ ucwords(str_replace('_', ' ', $claim->work_type)) }}</p>
                                            </div>
                                            <div>
                                                @if($claim->work_type === 'out_of_office')
                                                    <p><strong>Travel:</strong> {{ $claim->travel_origin }} → {{ $claim->travel_destination }}</p>
                                                    <p><strong>Purpose:</strong> {{ Str::limit($claim->travel_purpose, 50) }}</p>
                                                @endif
                                                <p><strong>Submitted:</strong> {{ $claim->submitted_at ? $claim->submitted_at->diffForHumans() : $claim->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>

                                        @if($claim->remarks)
                                            <div class="mt-3 p-3 bg-gray-50 rounded-md">
                                                <p class="text-sm text-gray-700"><strong>Remarks:</strong> {{ $claim->remarks }}</p>
                                            </div>
                                        @endif

                                        <div class="mt-4 flex flex-wrap gap-2">
                                            @foreach($claim->documents as $document)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucwords(str_replace('_', ' ', $document->document_type)) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-6 flex items-center justify-end space-x-3">
                                    <a href="{{ route('claims.details', $claim->id) }}"
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        View
                                    </a>
                                    <button onclick="showRejectModal({{ $claim->id }}, '{{ $claim->claim_number }}')"
                                            class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Reject
                                    </button>
                                    <button wire:click="approveClaim({{ $claim->id }})"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Approve
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2">No pending claims to review.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Approvals -->
        @if($recentApprovals->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Approvals</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($recentApprovals as $claim)
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900">{{ $claim->claim_number }}</h4>
                                    <p class="text-sm text-gray-600">{{ $claim->user->name }} - {{ $claim->duty_date->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ \App\Models\SystemSetting::formatCurrency($claim->total_amount) }}</p>
                                    <p class="text-xs text-gray-500">Approved {{ $claim->approved_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Reject Claim
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="rejectClaimNumber">
                                    Are you sure you want to reject this claim?
                                </p>
                                <div class="mt-4">
                                    <label for="rejectionReason" class="block text-sm font-medium text-gray-700">Reason for rejection *</label>
                                    <textarea id="rejectionReason" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500" placeholder="Please provide a reason for rejecting this claim..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="confirmReject()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Reject Claim
                    </button>
                    <button type="button" onclick="hideRejectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentClaimId = null;

        function showRejectModal(claimId, claimNumber) {
            currentClaimId = claimId;
            document.getElementById('rejectClaimNumber').textContent = `Are you sure you want to reject claim ${claimNumber}?`;
            document.getElementById('rejectionReason').value = '';
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function hideRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            currentClaimId = null;
        }

        function confirmReject() {
            const reason = document.getElementById('rejectionReason').value.trim();
            if (!reason) {
                alert('Please provide a reason for rejection.');
                return;
            }

            @this.call('rejectClaim', currentClaimId, reason);
            hideRejectModal();
        }

        // Close modal when clicking outside
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideRejectModal();
            }
        });
    </script>
</div>
