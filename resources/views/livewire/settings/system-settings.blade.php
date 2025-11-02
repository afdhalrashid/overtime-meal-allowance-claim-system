<div>
    <x-slot name="title">System Settings</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">System Settings</h1>
            <p class="mt-2 text-gray-600">Configure currency format and payment rates</p>
        </div>

        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form wire:submit.prevent="save" class="space-y-8">
                        <!-- Currency Settings -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Currency Settings</h3>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-4">
                                    <label for="currency_symbol" class="w-32 text-sm font-medium text-gray-700">
                                        Currency Symbol
                                    </label>
                                    <input type="text" id="currency_symbol" wire:model="currency_symbol"
                                           class="flex-1 max-w-xs border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="RM">
                                    @error('currency_symbol') <p class="ml-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex items-center space-x-4">
                                    <label for="currency_code" class="w-32 text-sm font-medium text-gray-700">
                                        Currency Code
                                    </label>
                                    <input type="text" id="currency_code" wire:model="currency_code"
                                           class="flex-1 max-w-xs border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="MYR" maxlength="3">
                                    @error('currency_code') <p class="ml-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="flex items-center space-x-4">
                                    <label for="currency_position" class="w-32 text-sm font-medium text-gray-700">
                                        Currency Position
                                    </label>
                                    <select id="currency_position" wire:model="currency_position"
                                            class="flex-1 max-w-xs border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="before">Before Amount (RM 100.00)</option>
                                        <option value="after">After Amount (100.00 RM)</option>
                                    </select>
                                    @error('currency_position') <p class="ml-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                                    </select>
                                    @error('currency_position') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Preview -->
                            <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    <strong>Preview:</strong>
                                    @if($currency_position === 'before')
                                        {{ $currency_symbol }} 100.00
                                    @else
                                        100.00 {{ $currency_symbol }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Overtime Rates -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Overtime Rates (per hour)</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="overtime_rate_weekday" class="block text-sm font-medium text-gray-700 mb-2">
                                        Weekday Rate
                                    </label>

                                    <div class="inline-flex items-stretch rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 bg-gray-50 text-sm font-medium">
                                            {{ $currency_symbol }}
                                        </span>
                                        <input type="number" step="0.01" id="overtime_rate_weekday" wire:model="overtime_rate_weekday"
                                               class="w-40 px-3 py-2 border rounded-r-md focus:outline-none focus:ring-2 focus:ring-blue-400"/>
                                    </div>
                                    @error('overtime_rate_weekday') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="overtime_rate_weekend" class="block text-sm font-medium text-gray-700 mb-2">
                                        Weekend Rate
                                    </label>

                                    <div class="inline-flex items-stretch rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 bg-gray-50 text-sm font-medium">
                                            {{ $currency_symbol }}
                                        </span>
                                        <input type="number" step="0.01" id="overtime_rate_weekend" wire:model="overtime_rate_weekend"
                                               class="w-40 px-3 py-2 border rounded-r-md focus:outline-none focus:ring-2 focus:ring-blue-400"/>
                                    </div>
                                    @error('overtime_rate_weekend') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="overtime_rate_holiday" class="block text-sm font-medium text-gray-700 mb-2">
                                        Holiday Rate
                                    </label>
                                    <div class="inline-flex items-stretch rounded-md shadow-sm">
                                        <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 bg-gray-50 text-sm font-medium">
                                            {{ $currency_symbol }}
                                        </span>
                                        <input type="number" step="0.01" id="overtime_rate_holiday" wire:model="overtime_rate_holiday"
                                               class="w-40 px-3 py-2 border rounded-r-md focus:outline-none focus:ring-2 focus:ring-blue-400"/>
                                    </div>
                                    @error('overtime_rate_holiday') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Meal Allowance -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Meal Allowance</h3>
                            <div class="max-w-sm">
                                <label for="meal_allowance_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Amount
                                </label>
                                <div class="inline-flex items-stretch rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 bg-gray-50 text-sm font-medium">
                                        {{ $currency_symbol }}
                                    </span>
                                    <input type="number" step="0.01" id="meal_allowance_amount" wire:model="meal_allowance_amount"
                                            class="w-40 px-3 py-2 border rounded-r-md focus:outline-none focus:ring-2 focus:ring-blue-400"/>
                                </div>

                                @error('meal_allowance_amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                <p class="text-xs text-gray-500 mt-1">Payable when working 2+ hours overtime past 7 PM</p>
                            </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Save Settings
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>
