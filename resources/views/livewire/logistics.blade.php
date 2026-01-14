<div class="p-6 space-y-6">
    {{-- Top Section: Search & Actions --}}
    <div class="flex flex-col md:flex-row gap-4 justify-between">
        <div class="relative flex-1 max-w-2xl group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-teal-500 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search items..." 
                   class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border-2 border-transparent rounded-2xl text-gray-800 placeholder-gray-400 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 focus:shadow-xl hover:bg-white hover:scale-[1.02] hover:shadow-lg hover:border-teal-200/60 transition-all duration-300 ease-out transform">
        </div>
        
        <button wire:click="create" class="px-6 py-3.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-2xl shadow-lg shadow-teal-500/20 hover:shadow-teal-500/30 transition-all transform hover:-translate-y-0.5 flex items-center gap-2">
            <div class="bg-white/20 p-1 rounded-lg backdrop-blur-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <span>Add New Item</span>
        </button>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-2xl flex items-center gap-3 shadow-sm" role="alert">
            <div class="bg-green-200 p-1.5 rounded-full">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <span class="font-medium">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Item Name</th>
                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Min. Stock</th>
                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logistics as $item)
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        <td class="py-4 px-6">
                            <span class="font-semibold text-gray-700 text-sm">{{ $item->name }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-bold bg-blue-50 text-blue-600">
                                {{ $item->stock }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-sm text-gray-500 font-medium">{{ $item->unit }}</td>
                        <td class="py-4 px-6 text-sm text-gray-400">
                            {{ $item->minimum_stock }} {{ $item->unit }}
                        </td>
                        <td class="py-4 px-6">
                            @if($item->stock <= $item->minimum_stock)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-full border border-red-100">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Low Stock
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-600 text-xs font-bold rounded-full border border-green-100">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    In Stock
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="edit({{ $item->id }})" class="p-2 text-gray-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-all" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:confirm="Are you sure you want to delete this item?" wire:click="delete({{ $item->id }})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">No items found</h3>
                                <p class="text-sm text-gray-500 mt-1">Get started by adding your first inventory item.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $logistics->links() }}
        </div>
    </div>

    {{-- Modal --}}
    @if($isOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click.self="closeModal">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl transform transition-all">
            <h3 class="text-xl font-bold text-gray-800 mb-4">{{ $isEditMode ? 'Edit Item' : 'Add New Item' }}</h3>
            
            <form wire:submit.prevent="store">
                <div class="space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                        <input wire:model="name" type="text" placeholder="e.g., Rice, Sugar, Oil"
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    {{-- Properties Row --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div x-data="{ val: @entangle('stock') }">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Amount</label>
                            <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                                <button type="button" @click="val = Math.max(0, parseInt(val) - 1)" class="px-3 py-2 bg-gray-50 hover:bg-gray-100 border-r border-gray-200 text-gray-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                </button>
                                <input wire:model="stock" type="number" min="0" class="w-full px-2 py-2 text-center border-0 focus:ring-0 appearance-none bg-white">
                                <button type="button" @click="val = parseInt(val) + 1" class="px-3 py-2 bg-gray-50 hover:bg-gray-100 border-l border-gray-200 text-gray-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                            @error('stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                            <select wire:model="unit" 
                                    class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                <option value="">Select Unit</option>
                                <option value="kg">Kilogram (kg)</option>
                                <option value="gr">Gram (gr)</option>
                                <option value="liter">Liter (l)</option>
                                <option value="ml">Milliliter (ml)</option>
                                <option value="pcs">Pieces (pcs)</option>
                                <option value="box">Box</option>
                                <option value="pack">Pack</option>
                                <option value="ikat">Ikat</option>
                                <option value="butir">Butir</option>
                                <option value="porsi">Porsi</option>
                            </select>
                            @error('unit') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Stock (Alert)</label>
                        <input wire:model="minimum_stock" type="number" min="0"
                               class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Stock below this level will show a warning.</p>
                        @error('minimum_stock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" wire:click="closeModal" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl transition-colors">
                        {{ $isEditMode ? 'Update Item' : 'Create Item' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
