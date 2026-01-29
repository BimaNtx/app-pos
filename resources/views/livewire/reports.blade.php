@php
    use App\Helpers\NumberHelper;
@endphp

<div class="flex-1 overflow-y-auto">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Laporan</h1>
                <p class="text-gray-500 text-sm">Analitik dan wawasan penjualan</p>
            </div>
            {{-- Period Selector --}}
            <div class="flex gap-2 items-center flex-wrap">
                @foreach(['today' => 'Hari Ini', 'week' => 'Minggu Ini', 'month' => 'Bulan Ini', 'year' => 'Tahun Ini'] as $key => $label)
                    <button wire:click="setPeriod('{{ $key }}')"
                        class="px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors {{ $period === $key ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </button>
                @endforeach

                {{-- Download PDF Button --}}
                <a href="{{ route('reports.download-pdf', $period) }}"
                    class="ml-2 px-3 sm:px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="hidden sm:inline">Download PDF</span>
                    <span class="sm:hidden">PDF</span>
                </a>
            </div>
        </div>
    </div>

    <div class="p-4 sm:p-6 space-y-6">
        {{-- Sales Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            {{-- Total Penjualan --}}
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 min-w-0 overflow-hidden">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Total Penjualan</p>
                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 mt-1 truncate"
                           title="{{ NumberHelper::formatRupiahFull($this->totalSales) }}">
                            {{ NumberHelper::formatRupiah($this->totalSales, true) }}
                        </p>
                        @if($this->totalSales >= 1000000)
                            <p class="text-xs text-gray-400 mt-1 truncate">
                                {{ NumberHelper::formatRupiahFull($this->totalSales) }}
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
            </div>

            {{-- Total Transaksi --}}
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 min-w-0 overflow-hidden">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Total Transaksi</p>
                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 mt-1">
                            {{ number_format($this->totalTransactions) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Rata-rata Pesanan --}}
            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-gray-100 min-w-0 overflow-hidden sm:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-gray-500 text-xs sm:text-sm font-medium">Rata-rata Pesanan</p>
                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 mt-1 truncate"
                           title="{{ NumberHelper::formatRupiahFull($this->averageOrder) }}">
                            {{ NumberHelper::formatRupiah($this->averageOrder, true) }}
                        </p>
                        @if($this->averageOrder >= 1000000)
                            <p class="text-xs text-gray-400 mt-1 truncate">
                                {{ NumberHelper::formatRupiahFull($this->averageOrder) }}
                            </p>
                        @endif
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-purple-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Financial Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            {{-- Total Pemasukan --}}
            <div class="rounded-2xl p-4 sm:p-6 shadow-lg min-w-0 overflow-hidden"
                style="background: linear-gradient(135deg, #22c55e 0%, #059669 100%);">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium" style="color: rgba(255,255,255,0.8);">Total Pemasukan</p>
                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mt-1 truncate"
                           title="{{ NumberHelper::formatRupiahFull($this->totalSales) }}">
                            {{ NumberHelper::formatRupiah($this->totalSales, true) }}
                        </p>
                        <p class="text-xs mt-2 truncate" style="color: rgba(255,255,255,0.7);">
                            Dari {{ number_format($this->totalTransactions) }} transaksi
                            @if($this->totalSales >= 1000000)
                                <br><span class="opacity-75">{{ NumberHelper::formatRupiahFull($this->totalSales) }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center flex-shrink-0"
                        style="background: rgba(255,255,255,0.2);">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total Pengeluaran --}}
            <div class="rounded-2xl p-4 sm:p-6 shadow-lg min-w-0 overflow-hidden"
                style="background: linear-gradient(135deg, #ef4444 0%, #e11d48 100%);">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium" style="color: rgba(255,255,255,0.8);">Total Pengeluaran</p>
                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mt-1 truncate"
                           title="{{ NumberHelper::formatRupiahFull($this->totalExpenses) }}">
                            {{ NumberHelper::formatRupiah($this->totalExpenses, true) }}
                        </p>
                        <p class="text-xs mt-2 truncate" style="color: rgba(255,255,255,0.7);">
                            {{ $this->expensesByCategory->count() }} kategori
                            @if($this->totalExpenses >= 1000000)
                                <br><span class="opacity-75">{{ NumberHelper::formatRupiahFull($this->totalExpenses) }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center flex-shrink-0"
                        style="background: rgba(255,255,255,0.2);">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Laba Bersih --}}
            <div class="rounded-2xl p-4 sm:p-6 shadow-lg min-w-0 overflow-hidden sm:col-span-2 lg:col-span-1"
                style="background: linear-gradient(135deg, {{ $this->netProfit >= 0 ? '#3b82f6, #4f46e5' : '#4b5563, #374151' }});">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium" style="color: rgba(255,255,255,0.8);">Laba Bersih</p>
                        <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mt-1 truncate"
                           title="{{ NumberHelper::formatRupiahFull($this->netProfit) }}">
                            {{ NumberHelper::formatRupiah($this->netProfit, true) }}
                        </p>
                        <p class="text-xs mt-2 truncate" style="color: rgba(255,255,255,0.7);">
                            @if($this->totalSales > 0)
                                Margin: {{ number_format(($this->netProfit / $this->totalSales) * 100, 1) }}%
                            @else
                                -
                            @endif
                            @if(abs($this->netProfit) >= 1000000)
                                <br><span class="opacity-75">{{ NumberHelper::formatRupiahFull($this->netProfit) }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center flex-shrink-0"
                        style="background: rgba(255,255,255,0.2);">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Expense Breakdown by Category --}}
        @if($this->expensesByCategory->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-2">
                    <h3 class="font-semibold text-gray-800 text-sm sm:text-base">Pengeluaran per Kategori</h3>
                    <a href="{{ route('expenses') }}" class="text-xs sm:text-sm text-teal-600 hover:text-teal-700 font-medium whitespace-nowrap">
                        Lihat Semua →
                    </a>
                </div>
                <div class="p-4 sm:p-6">
                    @php
                        $totalExp = $this->expensesByCategory->sum('total_amount') ?: 1;
                        $expColors = [
                            'bahan_baku' => ['bg' => 'bg-orange-500', 'light' => 'bg-orange-100'],
                            'operasional' => ['bg' => 'bg-blue-500', 'light' => 'bg-blue-100'],
                            'gaji' => ['bg' => 'bg-green-500', 'light' => 'bg-green-100'],
                            'lainnya' => ['bg' => 'bg-gray-500', 'light' => 'bg-gray-100'],
                        ];
                        $expIcons = [
                            'bahan_baku' => '🥬',
                            'operasional' => '⚡',
                            'gaji' => '💰',
                            'lainnya' => '📦',
                        ];
                    @endphp

                    @foreach($this->expensesByCategory as $exp)
                        <div class="mb-4 last:mb-0">
                            <div class="flex items-center justify-between mb-2 gap-2">
                                <span class="font-medium text-gray-700 flex items-center gap-2 text-sm min-w-0">
                                    <span class="flex-shrink-0">{{ $expIcons[$exp->category] ?? '📦' }}</span>
                                    <span class="truncate">{{ $categoryLabels[$exp->category] ?? ucfirst($exp->category) }}</span>
                                </span>
                                <span class="text-gray-600 font-medium text-sm flex-shrink-0"
                                      title="{{ NumberHelper::formatRupiahFull($exp->total_amount) }}">
                                    {{ NumberHelper::formatRupiah($exp->total_amount, true) }}
                                </span>
                            </div>
                            <div
                                class="w-full h-2 sm:h-3 {{ $expColors[$exp->category]['light'] ?? 'bg-gray-100' }} rounded-full overflow-hidden">
                                <div class="h-full {{ $expColors[$exp->category]['bg'] ?? 'bg-gray-500' }} rounded-full transition-all duration-500"
                                    style="width: {{ ($exp->total_amount / $totalExp) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            {{-- Top Products --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800 text-sm sm:text-base">Produk Terlaris</h3>
                </div>
                <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                    @forelse($this->topProducts as $index => $product)
                        <div class="px-4 sm:px-6 py-3 flex items-center gap-3 sm:gap-4">
                            <span
                                class="w-7 h-7 sm:w-8 sm:h-8 bg-gray-100 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold text-gray-500 flex-shrink-0">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 text-sm truncate">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ $product->category }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-semibold text-gray-800 text-sm">{{ number_format($product->total_qty) }} terjual</p>
                                <p class="text-xs text-teal-600"
                                   title="{{ NumberHelper::formatRupiahFull($product->total_revenue) }}">
                                    {{ NumberHelper::formatRupiah($product->total_revenue, true) }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 sm:px-6 py-8 text-center text-gray-400">
                            <p class="text-sm">Tidak ada data penjualan</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Sales by Category --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800 text-sm sm:text-base">Penjualan per Kategori</h3>
                </div>
                <div class="p-4 sm:p-6">
                    @php
                        $totalCatRevenue = $this->salesByCategory->sum('total_revenue') ?: 1;
                        $colors = ['food' => 'bg-orange-500', 'drink' => 'bg-blue-500', 'dessert' => 'bg-pink-500'];
                        $bgColors = ['food' => 'bg-orange-100', 'drink' => 'bg-blue-100', 'dessert' => 'bg-pink-100'];
                    @endphp

                    @forelse($this->salesByCategory as $cat)
                        <div class="mb-4 last:mb-0">
                            <div class="flex items-center justify-between mb-2 gap-2">
                                <span class="font-medium text-gray-700 capitalize flex items-center gap-2 text-sm min-w-0">
                                    <span class="flex-shrink-0">@if($cat->category === 'food') 🍚 @elseif($cat->category === 'drink') 🥤 @else 🍰 @endif</span>
                                    <span class="truncate">{{ $cat->category }}</span>
                                </span>
                                <span class="text-gray-600 font-medium text-sm flex-shrink-0"
                                      title="{{ NumberHelper::formatRupiahFull($cat->total_revenue) }}">
                                    {{ NumberHelper::formatRupiah($cat->total_revenue, true) }}
                                </span>
                            </div>
                            <div
                                class="w-full h-2 sm:h-3 {{ $bgColors[$cat->category] ?? 'bg-gray-100' }} rounded-full overflow-hidden">
                                <div class="h-full {{ $colors[$cat->category] ?? 'bg-gray-500' }} rounded-full transition-all duration-500"
                                    style="width: {{ ($cat->total_revenue / $totalCatRevenue) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 py-8">
                            <p class="text-sm">Tidak ada data penjualan</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>