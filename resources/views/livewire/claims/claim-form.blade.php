<div>
    <x-slot name="title">{{ $isEditing ? 'Edit Claim' : 'New Claim' }}</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ $isEditing ? 'Edit Claim' : 'Submit New Claim' }}
                    </h1>
                    <p class="mt-2 text-gray-600">Complete the form below to submit your overtime/meal allowance claim</p>
                </div>
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
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

        <!-- Form -->
        <form wire:submit.prevent="save" class="space-y-8">
            <!-- Basic Information Card -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Duty Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Duty Date -->
                    <div>
                        <label for="duty_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Duty Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="duty_date" wire:model.live="duty_date"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('duty_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Work Type -->
                    <div>
                        <label for="work_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Work Type <span class="text-red-500">*</span>
                        </label>
                        <select id="work_type" wire:model.live="work_type"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="in_office">In Office</option>
                            <option value="out_of_office">Out of Office</option>
                        </select>
                        @error('work_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Start Time -->
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Time <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="start_time" wire:model.live="start_time"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('start_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- End Time -->
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                            End Time <span class="text-red-500">*</span>
                        </label>
                        <input type="time" id="end_time" wire:model.live="end_time"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @error('end_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Travel Information (Only for Out of Office) -->
            @if($work_type === 'out_of_office')
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Travel Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Travel Start Time -->
                        <div>
                            <label for="travel_start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Travel Start Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="travel_start_time" wire:model.live="travel_start_time"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            @error('travel_start_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Travel End Time -->
                        <div>
                            <label for="travel_end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Travel End Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="travel_end_time" wire:model.live="travel_end_time"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            @error('travel_end_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Travel Origin -->
                        <div>
                            <label for="travel_origin" class="block text-sm font-medium text-gray-700 mb-2">
                                Origin <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="travel_origin" wire:model="travel_origin"
                                   placeholder="Starting location"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            @error('travel_origin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Travel Destination -->
                        <div>
                            <label for="travel_destination" class="block text-sm font-medium text-gray-700 mb-2">
                                Destination <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="travel_destination" wire:model="travel_destination"
                                   placeholder="Destination location"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            @error('travel_destination') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Travel Purpose -->
                        <div class="md:col-span-2">
                            <label for="travel_purpose" class="block text-sm font-medium text-gray-700 mb-2">
                                Purpose of Travel <span class="text-red-500">*</span>
                            </label>
                            <textarea id="travel_purpose" wire:model="travel_purpose" rows="3"
                                      placeholder="Describe the purpose of your travel"
                                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                            @error('travel_purpose') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- Calculation Summary -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-6 rounded-lg">
                <h3 class="text-lg font-medium text-blue-900 mb-4">Calculation Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($overtime_hours, 1) }}</p>
                        <p class="text-sm text-blue-700">Overtime Hours</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-900">{{ \App\Models\SystemSetting::formatCurrency($meal_allowance_amount) }}</p>
                        <p class="text-sm text-blue-700">Meal Allowance</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-900">{{ \App\Models\SystemSetting::formatCurrency($total_amount) }}</p>
                        <p class="text-sm text-blue-700">Total Amount</p>
                    </div>
                </div>
            </div>

            <!-- Documents Upload -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Required Documents</h3>

                <div class="space-y-6">
                    <!-- Attendance Record -->
                    <div>
                        <label for="attendance_record" class="block text-sm font-medium text-gray-700 mb-2">
                            Attendance Record <span class="text-red-500">*</span>
                        </label>
                        <p class="text-sm text-gray-500 mb-2">
                            Upload {{ $work_type === 'out_of_office' ? 'Mac Check-In' : 'Teams-HR' }} record (PDF, JPG, PNG, DOCX - Max 5MB)
                        </p>

                        @if($isEditing && $claim && $claim->attendanceRecordDocument)
                            <div class="mb-3 p-3 bg-gray-50 rounded-md border border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ $claim->attendanceRecordDocument->original_name }}</span>
                                    </div>
                                    <a href="{{ route('documents.view', $claim->attendanceRecordDocument->id) }}"
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Upload a new file to replace the existing one</p>
                            </div>
                        @endif

                        <input type="file" id="attendance_record" wire:model="attendance_record"
                               accept=".pdf,.jpg,.jpeg,.png,.docx"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        @error('attendance_record') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Supporting Documents -->
                    <div>
                        <label for="supporting_documents" class="block text-sm font-medium text-gray-700 mb-2">
                            Supporting Documents <span class="text-red-500">*</span>
                        </label>
                        <p class="text-sm text-gray-500 mb-2">
                            Upload supporting documents (Program memos, itineraries, emails, etc.)
                        </p>

                        @if($isEditing && $claim && $claim->supportingDocumentsCollection->count() > 0)
                            <div class="mb-3 space-y-2">
                                @foreach($claim->supportingDocumentsCollection as $document)
                                    <div class="p-3 bg-gray-50 rounded-md border border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span class="text-sm text-gray-700">{{ $document->original_name }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('documents.view', $document->id) }}"
                                                   target="_blank"
                                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    View
                                                </a>
                                                <button type="button" wire:click="removeDocument({{ $document->id }})"
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <p class="text-xs text-gray-500">Upload additional files or replace existing ones</p>
                            </div>
                        @endif                        <input type="file" id="supporting_documents" wire:model="supporting_documents"
                               accept=".pdf,.jpg,.jpeg,.png,.docx" multiple
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        @error('supporting_documents.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Remarks -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Additional Information</h3>

                <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                        Remarks (Optional)
                    </label>
                    <textarea id="remarks" wire:model="remarks" rows="4"
                              placeholder="Any additional information about this claim..."
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                    @error('remarks') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('staff.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>

                <div class="mx-2 flex space-x-3">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700">
                        Save as Draft
                    </button>

                    <button type="button" wire:click="submitForApproval"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white !bg-green-600 hover:!bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Submit for Approval
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
