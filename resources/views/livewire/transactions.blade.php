<div class="flex-1 overflow-y-auto" x-data x-on:reprint-receipt.window="setTimeout(() => window.print(), 300)">
    {{-- Flash Message --}}
    @if(session('message'))
        <div class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg animate-pulse">
            {{ session('message') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Transaksi</h1>
        <p class="text-gray-500 text-sm">Lihat, edit, dan kelola transaksi sebelumnya</p>
    </div>

    <div class="p-6">
        {{-- Filters --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                {{-- Trash Toggle --}}
                <button wire:click="toggleTrash"
                    class="px-4 py-2.5 rounded-xl font-medium transition-all flex items-center gap-2 {{ $showTrash ? 'bg-red-100 text-red-700 ring-2 ring-red-300' : 'bg-gray-100 hover:bg-gray-200 text-gray-600' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ $showTrash ? 'Lihat Aktif' : 'Sampah' }}
                </button>
                {{-- Search --}}
                <div class="relative flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari berdasarkan kode atau pelanggan..."
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                {{-- Date Filter --}}
                <input type="date" wire:model.live="dateFilter"
                    class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                @if($dateFilter)
                    <button wire:click="$set('dateFilter', '')"
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-colors">
                        Hapus
                    </button>
                @endif
            </div>
            @if($showTrash)
                <div class="mt-3 px-3 py-2 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Menampilkan transaksi yang sudah dihapus. Klik "Pulihkan" untuk mengembalikan.
                </div>
            @endif
        </div>

        {{-- Transactions Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal & Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kode Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transactions as $transaction)
                            <tr wire:key="trx-{{ $transaction->id }}" class="hover:bg-gray-50 transition-colors {{ $showTrash ? 'bg-red-50/50' : '' }}">
                                <td class="px-6 py-4 text-gray-500 text-sm">
                                    <div>{{ $transaction->created_at->format('d M Y') }}</div>
                                    <div class="text-xs">{{ $transaction->created_at->format('H:i') }}</div>
                                    @if($showTrash)
                                        <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-xs font-medium bg-red-100 text-red-700">
                                            Dihapus
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="font-mono text-sm text-teal-600 font-medium">{{ $transaction->transaction_code }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-800">{{ $transaction->customer_name }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-800">{{ $transaction->details->sum('quantity') }} item</div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">
                                        {{ $transaction->details->take(2)->pluck('product.name')->join(', ') }}
                                        @if($transaction->details->count() > 2)
                                            +{{ $transaction->details->count() - 2 }} lainnya
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($showTrash)
                                            {{-- Restore Button (only in trash view) --}}
                                            <button wire:click="restoreTransaction({{ $transaction->id }})"
                                                class="inline-flex items-center justify-center px-3 py-2 bg-green-50 hover:bg-green-100 text-green-600 rounded-lg transition-colors gap-1"
                                                title="Pulihkan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                </svg>
                                                <span class="text-sm font-medium">Pulihkan</span>
                                            </button>
                                        @else
                                            {{-- View Detail --}}
                                            <button wire:click="viewDetail({{ $transaction->id }})"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors"
                                                title="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            {{-- Edit --}}
                                            <button wire:click="editTransaction({{ $transaction->id }})"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg transition-colors"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            {{-- Delete --}}
                                            <button wire:click="confirmDeleteTransaction({{ $transaction->id }})"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                            {{-- Reprint --}}
                                            <button wire:click="reprint({{ $transaction->id }})"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-teal-50 hover:bg-teal-100 text-teal-600 rounded-lg transition-colors"
                                                title="Cetak Ulang">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p>{{ $showTrash ? 'Tidak ada transaksi di sampah' : 'Tidak ada transaksi ditemukan' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedTransaction)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModals"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal Content --}}
                <div
                    class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    {{-- Header --}}
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Detail Transaksi</h3>
                            <p class="text-sm text-teal-600 font-mono">{{ $selectedTransaction->transaction_code }}</p>
                        </div>
                        <button wire:click="closeModals" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Transaction Info --}}
                    <div class="py-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Pelanggan</span>
                            <span class="font-medium text-gray-900">{{ $selectedTransaction->customer_name }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Tanggal</span>
                            <span
                                class="font-medium text-gray-900">{{ $selectedTransaction->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    {{-- Items --}}
                    <div class="border-t border-gray-100 pt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Item Pesanan</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            @foreach($selectedTransaction->details as $detail)
                                <div class="flex justify-between items-center py-2 px-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-800">
                                            {{ $detail->product->name ?? 'Produk tidak ditemukan' }}</p>
                                        <p class="text-xs text-gray-500">{{ $detail->quantity }} x Rp
                                            {{ number_format($detail->price_at_time, 0, ',', '.') }}</p>
                                    </div>
                                    <span class="font-semibold text-gray-800">Rp
                                        {{ number_format($detail->quantity * $detail->price_at_time, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Total --}}
                    @php
                        $subtotal = $selectedTransaction->details->sum(fn($d) => $d->price_at_time * $d->quantity);
                        // Use stored tax amount if available, otherwise calculate from total
                        $storedTaxPercent = $selectedTransaction->tax_percentage ?? $taxPercentage;
                        $storedTaxAmount = $selectedTransaction->tax_amount ?? ($selectedTransaction->total_amount - $subtotal);
                    @endphp
                    <div class="border-t border-gray-100 pt-4 mt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="text-gray-700">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Pajak ({{ number_format($storedTaxPercent, 0) }}%)</span>
                            <span class="text-gray-700">Rp {{ number_format($storedTaxAmount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span class="text-teal-600">Rp
                                {{ number_format($selectedTransaction->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 mt-6">
                        <button wire:click="closeModals"
                            class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                            Tutup
                        </button>
                        <button wire:click="editTransaction({{ $selectedTransaction->id }})"
                            class="flex-1 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl transition-colors">
                            Edit Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Edit Modal --}}
    @if($showEditModal && $selectedTransaction)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModals"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal Content --}}
                <div
                    class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    {{-- Header --}}
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Edit Transaksi</h3>
                            <p class="text-sm text-teal-600 font-mono">{{ $selectedTransaction->transaction_code }}</p>
                        </div>
                        <button wire:click="closeModals" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Customer Name --}}
                    <div class="py-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pelanggan</label>
                        <input type="text" wire:model="editCustomerName"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    </div>

                    {{-- Items --}}
                    <div class="border-t border-gray-100 pt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Item Pesanan</h4>
                        <div class="space-y-3 max-h-48 overflow-y-auto">
                            @foreach($editItems as $index => $item)
                                <div class="flex items-center gap-3 py-2 px-3 bg-gray-50 rounded-lg"
                                    wire:key="edit-item-{{ $index }}">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800 text-sm">{{ $item['product_name'] }}</p>
                                        <p class="text-xs text-gray-500">Rp
                                            {{ number_format($item['price_at_time'], 0, ',', '.') }}/item</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button wire:click="updateItemQuantity({{ $index }}, {{ $item['quantity'] - 1 }})"
                                            class="w-8 h-8 flex items-center justify-center bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors"
                                            {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <span class="w-8 text-center font-semibold">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateItemQuantity({{ $index }}, {{ $item['quantity'] + 1 }})"
                                            class="w-8 h-8 flex items-center justify-center bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                    @if(count($editItems) > 1)
                                        <button wire:click="removeItem({{ $index }})"
                                            class="w-8 h-8 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Total Preview --}}
                    @php
                        $editSubtotal = collect($editItems)->sum(fn($item) => $item['quantity'] * $item['price_at_time']);
                        $editTax = $editSubtotal * ($taxPercentage / 100);
                        $editTotal = $editSubtotal + $editTax;
                    @endphp
                    <div class="border-t border-gray-100 pt-4 mt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="text-gray-700">Rp {{ number_format($editSubtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Pajak ({{ $taxPercentage }}%)</span>
                            <span class="text-gray-700">Rp {{ number_format($editTax, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-200">
                            <span>Total Baru</span>
                            <span class="text-teal-600">Rp {{ number_format($editTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 mt-6">
                        <button wire:click="closeModals"
                            class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                            Batal
                        </button>
                        <button wire:click="updateTransaction"
                            class="flex-1 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Print Receipt (Hidden, visible only when printing) --}}
    @if($reprintTransaction)
        <div id="print-receipt" class="hidden print:block p-8 bg-white">
            <div class="max-w-xs mx-auto text-center">
                <h1 class="text-2xl font-bold mb-1">{{ $restaurantName }}</h1>
                @if($restaurantAddress)
                    <p class="text-gray-600 text-sm mb-1">{{ $restaurantAddress }}</p>
                @endif
                <p class="text-gray-500 text-sm mb-4">Struk Restoran</p>
                <div class="border-t border-b border-dashed border-gray-300 py-3 my-4">
                    <p class="text-sm"><strong>Transaksi:</strong> {{ $reprintTransaction->transaction_code }}</p>
                    <p class="text-sm"><strong>Pelanggan:</strong> {{ $reprintTransaction->customer_name }}</p>
                    <p class="text-sm"><strong>Tanggal:</strong> {{ $reprintTransaction->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <table class="w-full text-sm mb-4">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2">Item</th>
                            <th class="text-center py-2">Jml</th>
                            <th class="text-right py-2">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reprintTransaction->details as $detail)
                            <tr class="border-b border-gray-100">
                                <td class="text-left py-2">{{ $detail->product->name }}</td>
                                <td class="text-center py-2">{{ $detail->quantity }}</td>
                                <td class="text-right py-2">Rp
                                    {{ number_format($detail->price_at_time * $detail->quantity, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @php
                    $subtotal = $reprintTransaction->details->sum(fn($d) => $d->price_at_time * $d->quantity);
                    // Use stored tax data if available
                    $storedTaxPercent = $reprintTransaction->tax_percentage ?? $taxPercentage;
                    $storedTaxAmount = $reprintTransaction->tax_amount ?? ($reprintTransaction->total_amount - $subtotal);
                @endphp
                <div class="border-t border-dashed border-gray-300 pt-3 space-y-1">
                    <div class="flex justify-between text-sm">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>Pajak ({{ number_format($storedTaxPercent, 0) }}%)</span>
                        <span>Rp {{ number_format($storedTaxAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200">
                        <span>Total</span>
                        <span>Rp {{ number_format($reprintTransaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                <p class="text-gray-400 text-xs mt-6">Terima kasih atas pesanan Anda!</p>
                <p class="text-gray-300 text-xs">(Cetak Ulang)</p>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="cancelDelete">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm text-center p-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Hapus Transaksi?</h3>
                <p class="text-gray-500 text-sm mb-6">Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak
                    dapat dibatalkan.</p>
                <div class="flex gap-3">
                    <button wire:click="cancelDelete"
                        class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                        Batal
                    </button>
                    <button wire:click="deleteTransaction"
                        class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>