<div class="flex-1 overflow-y-auto">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <h1 class="text-2xl font-bold text-gray-800">Settings</h1>
        <p class="text-gray-500 text-sm">Configure your restaurant settings</p>
    </div>

    <div class="p-6 max-w-2xl">
        {{-- Success Message --}}
        @if($saved)
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="font-medium text-green-800">Settings saved successfully!</p>
                <p class="text-green-600 text-sm">Your changes have been applied.</p>
            </div>
        </div>
        @endif

        {{-- Settings Form --}}
        <form wire:submit="save" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Restaurant Info Section --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800">Restaurant Information</h3>
                <p class="text-gray-500 text-sm">This information will appear on receipts</p>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Restaurant Name *</label>
                    <input type="text" wire:model="restaurantName" 
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                           placeholder="Enter restaurant name">
                    @error('restaurantName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea wire:model="restaurantAddress" rows="3"
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all resize-none"
                              placeholder="Enter restaurant address"></textarea>
                    @error('restaurantAddress') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Tax Settings Section --}}
            <div class="px-6 py-4 border-t border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800">Tax Settings</h3>
                <p class="text-gray-500 text-sm">Configure tax calculation for orders</p>
            </div>
            <div class="p-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tax Percentage (%)</label>
                    <div class="relative w-48">
                        <input type="number" wire:model="taxPercentage" min="0" max="100" step="0.1"
                               class="w-full px-4 py-3 pr-12 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">%</span>
                    </div>
                    @error('taxPercentage') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    <p class="text-gray-500 text-xs mt-2">This percentage will be added to the subtotal</p>
                </div>
            </div>

            {{-- Save Button --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                <button type="submit" 
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl transition-colors shadow-lg shadow-teal-600/30">
                    <svg wire:loading wire:target="save" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg wire:loading.remove wire:target="save" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span wire:loading.remove wire:target="save">Save Settings</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </form>

        {{-- Data Management Section --}}
        <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800">Data Management</h3>
                <p class="text-gray-500 text-sm">Database and cache operations</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="font-medium text-gray-800">Clear Cache</p>
                        <p class="text-gray-500 text-sm">Clear application cache</p>
                    </div>
                    <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors text-sm">
                        Clear
                    </button>
                </div>
                <div class="flex items-center justify-between p-4 bg-red-50 rounded-xl">
                    <div>
                        <p class="font-medium text-red-800">Reset All Data</p>
                        <p class="text-red-600 text-sm">This will delete all transactions and products</p>
                    </div>
                    <button type="button" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-medium rounded-lg transition-colors text-sm">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
