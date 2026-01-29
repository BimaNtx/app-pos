@php
    use App\Helpers\NumberHelper;
@endphp

<div class="flex-1 overflow-y-auto">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-500 text-sm">Ringkasan performa restoran Anda</p>
    </div>

    <div class="p-4 sm:p-6 space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
            {{-- Today's Sales --}}
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 min-w-0 overflow-hidden">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Penjualan Hari Ini</p>
                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 mt-1 truncate"
                           title="{{ NumberHelper::formatRupiahFull($this->todaySales) }}">
                            {{ NumberHelper::formatRupiah($this->todaySales, true) }}
                        </p>
                        @if($this->todaySales >= 1000000)
                            <p class="text-xs text-gray-400 mt-1 truncate">
                                {{ NumberHelper::formatRupiahFull($this->todaySales) }}
                            </p>
                        @endif
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-teal-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 sm:mt-4 flex items-center text-xs sm:text-sm">
                    <span class="text-green-600 font-medium flex items-center">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        Live
                    </span>
                    <span class="text-gray-400 ml-2">Baru saja diperbarui</span>
                </div>
            </div>

            {{-- Transactions Today --}}
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 min-w-0 overflow-hidden">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Transaksi Hari Ini</p>
                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 mt-1">{{ number_format($this->todayTransactions) }}</p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 sm:mt-4 flex items-center text-xs sm:text-sm">
                    <span class="text-gray-500">Pesanan selesai hari ini</span>
                </div>
            </div>

            {{-- Best Seller --}}
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 min-w-0 overflow-hidden sm:col-span-2 md:col-span-1">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Terlaris Hari Ini</p>
                        @if($this->bestSeller)
                            <p class="text-lg sm:text-xl font-bold text-gray-800 mt-1 truncate"
                                title="{{ $this->bestSeller['name'] }}">{{ $this->bestSeller['name'] }}</p>
                            <p class="text-teal-600 font-medium text-xs sm:text-sm">{{ number_format($this->bestSeller['qty']) }} terjual</p>
                        @else
                            <p class="text-lg sm:text-xl font-bold text-gray-400 mt-1">Belum ada penjualan</p>
                        @endif
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-orange-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Weekly Sales Chart (Simple CSS bars) --}}
        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Penjualan Mingguan</h3>
            <div class="flex items-end justify-between gap-1 sm:gap-2 h-40 sm:h-48">
                @php
                    $maxSale = max(array_column($this->weeklySales, 'total')) ?: 1;
                @endphp
                @foreach($this->weeklySales as $day)
                    <div class="flex-1 flex flex-col items-center gap-1 sm:gap-2 group relative">
                        <div class="w-full bg-gray-100 rounded-t-lg relative cursor-pointer" style="height: 130px;">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-teal-600 to-teal-400 rounded-t-lg transition-all duration-500 group-hover:from-teal-700 group-hover:to-teal-500"
                                style="height: {{ $maxSale > 0 ? ($day['total'] / $maxSale) * 100 : 0 }}%;">
                            </div>
                            {{-- Tooltip with smart formatting --}}
                            <div
                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 sm:px-3 py-1.5 sm:py-2 bg-white text-gray-800 text-xs font-medium rounded-lg shadow-xl border border-gray-200 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                <div class="text-center">
                                    <p class="font-semibold text-gray-800">{{ $day['date'] }}</p>
                                    <p class="text-teal-600 font-bold">{{ NumberHelper::formatRupiah($day['total'], true) }}</p>
                                    @if($day['total'] >= 1000000)
                                        <p class="text-gray-400 text-[10px]">{{ NumberHelper::formatRupiahFull($day['total']) }}</p>
                                    @endif
                                </div>
                                {{-- Tooltip arrow --}}
                                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1">
                                    <div class="border-4 border-transparent border-t-white"></div>
                                </div>
                            </div>
                        </div>
                        <span class="text-[10px] sm:text-xs text-gray-500 font-medium">{{ $day['date'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Transaksi Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kode</th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Pelanggan</th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Item</th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total</th>
                            <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($this->recentTransactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    <span class="font-mono text-xs sm:text-sm text-teal-600 font-medium">{{ $transaction->transaction_code }}</span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-gray-800 text-sm hidden sm:table-cell">
                                    <span class="truncate block max-w-[120px]" title="{{ $transaction->customer_name }}">{{ $transaction->customer_name }}</span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-gray-500 text-sm">{{ $transaction->details->sum('quantity') }} item</td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    <span class="font-semibold text-gray-800 text-xs sm:text-sm"
                                          title="{{ NumberHelper::formatRupiahFull($transaction->total_amount) }}">
                                        {{ NumberHelper::formatRupiah($transaction->total_amount, true) }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-gray-500 text-xs sm:text-sm hidden md:table-cell">{{ $transaction->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 sm:px-6 py-8 sm:py-12 text-center text-gray-400">
                                    <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-2 sm:mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="text-sm">Belum ada transaksi hari ini</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>