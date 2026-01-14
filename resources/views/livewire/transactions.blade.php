<div class="flex-1 overflow-y-auto" x-data x-on:reprint-receipt.window="setTimeout(() => window.print(), 300)">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <h1 class="text-2xl font-bold text-gray-800">Transaction History</h1>
        <p class="text-gray-500 text-sm">View and reprint past transactions</p>
    </div>

    <div class="p-6">
        {{-- Filters --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                {{-- Search --}}
                <div class="relative flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by code or customer..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                {{-- Date Filter --}}
                <input type="date" wire:model.live="dateFilter" 
                       class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                @if($dateFilter)
                <button wire:click="$set('dateFilter', '')" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition-colors">
                    Clear
                </button>
                @endif
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transactions as $transaction)
                        <tr wire:key="trx-{{ $transaction->id }}" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-500 text-sm">
                                <div>{{ $transaction->created_at->format('d M Y') }}</div>
                                <div class="text-xs">{{ $transaction->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm text-teal-600 font-medium">{{ $transaction->transaction_code }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-800">{{ $transaction->customer_name }}</td>
                            <td class="px-6 py-4">
                                <div class="text-gray-800">{{ $transaction->details->sum('quantity') }} items</div>
                                <div class="text-xs text-gray-500 truncate max-w-xs">
                                    {{ $transaction->details->take(2)->pluck('product.name')->join(', ') }}
                                    @if($transaction->details->count() > 2)
                                        +{{ $transaction->details->count() - 2 }} more
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="reprint({{ $transaction->id }})" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    Reprint
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p>No transactions found</p>
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

    {{-- Print Receipt (Hidden, visible only when printing) --}}
    @if($reprintTransaction)
    <div id="print-receipt" class="hidden print:block p-8 bg-white">
        <div class="max-w-xs mx-auto text-center">
            <h1 class="text-2xl font-bold mb-1">Kasir App</h1>
            <p class="text-gray-500 text-sm mb-4">Restaurant Receipt</p>
            <div class="border-t border-b border-dashed border-gray-300 py-3 my-4">
                <p class="text-sm"><strong>Transaction:</strong> {{ $reprintTransaction->transaction_code }}</p>
                <p class="text-sm"><strong>Customer:</strong> {{ $reprintTransaction->customer_name }}</p>
                <p class="text-sm"><strong>Date:</strong> {{ $reprintTransaction->created_at->format('d M Y, H:i') }}</p>
            </div>
            <table class="w-full text-sm mb-4">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2">Item</th>
                        <th class="text-center py-2">Qty</th>
                        <th class="text-right py-2">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reprintTransaction->details as $detail)
                    <tr class="border-b border-gray-100">
                        <td class="text-left py-2">{{ $detail->product->name }}</td>
                        <td class="text-center py-2">{{ $detail->quantity }}</td>
                        <td class="text-right py-2">Rp {{ number_format($detail->price_at_time * $detail->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @php
                $subtotal = $reprintTransaction->details->sum(fn($d) => $d->price_at_time * $d->quantity);
                $tax = $reprintTransaction->total_amount - $subtotal;
            @endphp
            <div class="border-t border-dashed border-gray-300 pt-3 space-y-1">
                <div class="flex justify-between text-sm">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Tax</span>
                    <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200">
                    <span>Total</span>
                    <span>Rp {{ number_format($reprintTransaction->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
            <p class="text-gray-400 text-xs mt-6">Thank you for your order!</p>
            <p class="text-gray-300 text-xs">(Reprint)</p>
        </div>
    </div>
    @endif
</div>
