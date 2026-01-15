<div class="flex-1 overflow-y-auto">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Reports / Laporan</h1>
                <p class="text-gray-500 text-sm">Sales analytics and insights</p>
            </div>
            {{-- Period Selector --}}
            <div class="flex gap-2">
                @foreach(['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month', 'year' => 'This Year'] as $key => $label)
                    <button wire:click="setPeriod('{{ $key }}')"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $period === $key ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="p-6 space-y-6">
        {{-- Sales Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Sales</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">Rp
                            {{ number_format($this->totalSales, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-14 h-14 bg-teal-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Transactions</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($this->totalTransactions) }}
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Average Order</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">Rp
                            {{ number_format($this->averageOrder, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Financial Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total Pemasukan --}}
            <div class="rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, #22c55e 0%, #059669 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: rgba(255,255,255,0.8);">Total Pemasukan</p>
                        <p class="text-3xl font-bold text-white mt-1">Rp {{ number_format($this->totalSales, 0, ',', '.') }}</p>
                        <p class="text-xs mt-2" style="color: rgba(255,255,255,0.7);">Dari {{ $this->totalTransactions }} transaksi</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total Pengeluaran --}}
            <div class="rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, #ef4444 0%, #e11d48 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: rgba(255,255,255,0.8);">Total Pengeluaran</p>
                        <p class="text-3xl font-bold text-white mt-1">Rp {{ number_format($this->totalExpenses, 0, ',', '.') }}</p>
                        <p class="text-xs mt-2" style="color: rgba(255,255,255,0.7);">{{ $this->expensesByCategory->count() }} kategori</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Laba Bersih --}}
            <div class="rounded-2xl p-6 shadow-lg" style="background: linear-gradient(135deg, {{ $this->netProfit >= 0 ? '#3b82f6, #4f46e5' : '#4b5563, #374151' }});">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color: rgba(255,255,255,0.8);">Laba Bersih</p>
                        <p class="text-3xl font-bold text-white mt-1">
                            {{ $this->netProfit >= 0 ? '' : '-' }}Rp {{ number_format(abs($this->netProfit), 0, ',', '.') }}
                        </p>
                        <p class="text-xs mt-2" style="color: rgba(255,255,255,0.7);">
                            @if($this->totalSales > 0)
                                Margin: {{ number_format(($this->netProfit / $this->totalSales) * 100, 1) }}%
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Pengeluaran per Kategori</h3>
                    <a href="{{ route('expenses') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                        Lihat Semua →
                    </a>
                </div>
                <div class="p-6">
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
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-gray-700 flex items-center gap-2">
                                    {{ $expIcons[$exp->category] ?? '📦' }}
                                    {{ $categoryLabels[$exp->category] ?? ucfirst($exp->category) }}
                                </span>
                                <span class="text-gray-600 font-medium">Rp
                                    {{ number_format($exp->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div
                                class="w-full h-3 {{ $expColors[$exp->category]['light'] ?? 'bg-gray-100' }} rounded-full overflow-hidden">
                                <div class="h-full {{ $expColors[$exp->category]['bg'] ?? 'bg-gray-500' }} rounded-full transition-all duration-500"
                                    style="width: {{ ($exp->total_amount / $totalExp) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Top Products --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Top Selling Products</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($this->topProducts as $index => $product)
                        <div class="px-6 py-3 flex items-center gap-4">
                            <span
                                class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-sm font-bold text-gray-500">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ $product->category }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-800">{{ $product->total_qty }} sold</p>
                                <p class="text-xs text-teal-600">Rp
                                    {{ number_format($product->total_revenue, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-400">
                            <p>No sales data available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Sales by Category --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Sales by Category</h3>
                </div>
                <div class="p-6">
                    @php
                        $totalCatRevenue = $this->salesByCategory->sum('total_revenue') ?: 1;
                        $colors = ['food' => 'bg-orange-500', 'drink' => 'bg-blue-500', 'dessert' => 'bg-pink-500'];
                        $bgColors = ['food' => 'bg-orange-100', 'drink' => 'bg-blue-100', 'dessert' => 'bg-pink-100'];
                    @endphp

                    @forelse($this->salesByCategory as $cat)
                        <div class="mb-4 last:mb-0">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-gray-700 capitalize flex items-center gap-2">
                                    @if($cat->category === 'food') 🍚 @elseif($cat->category === 'drink') 🥤 @else 🍰 @endif
                                    {{ $cat->category }}
                                </span>
                                <span class="text-gray-600 font-medium">Rp
                                    {{ number_format($cat->total_revenue, 0, ',', '.') }}</span>
                            </div>
                            <div
                                class="w-full h-3 {{ $bgColors[$cat->category] ?? 'bg-gray-100' }} rounded-full overflow-hidden">
                                <div class="h-full {{ $colors[$cat->category] ?? 'bg-gray-500' }} rounded-full transition-all duration-500"
                                    style="width: {{ ($cat->total_revenue / $totalCatRevenue) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 py-8">
                            <p>No sales data available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>