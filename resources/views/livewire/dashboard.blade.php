<div class="flex-1 overflow-y-auto">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-500 text-sm">Overview of your restaurant performance</p>
    </div>

    <div class="p-6 space-y-6">
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Today's Sales --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Today's Sales</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">Rp {{ number_format($this->todaySales, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-14 h-14 bg-teal-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-600 font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        Live
                    </span>
                    <span class="text-gray-400 ml-2">Updated just now</span>
                </div>
            </div>

            {{-- Transactions Today --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Transactions Today</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $this->todayTransactions }}</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-gray-500">Orders completed today</span>
                </div>
            </div>

            {{-- Best Seller --}}
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Best Seller Today</p>
                        @if($this->bestSeller)
                            <p class="text-xl font-bold text-gray-800 mt-1 truncate">{{ $this->bestSeller['name'] }}</p>
                            <p class="text-teal-600 font-medium text-sm">{{ $this->bestSeller['qty'] }} sold</p>
                        @else
                            <p class="text-xl font-bold text-gray-400 mt-1">No sales yet</p>
                        @endif
                    </div>
                    <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Weekly Sales Chart (Simple CSS bars) --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Weekly Sales</h3>
            <div class="flex items-end justify-between gap-2 h-48">
                @php
                    $maxSale = max(array_column($this->weeklySales, 'total')) ?: 1;
                @endphp
                @foreach($this->weeklySales as $day)
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-gray-100 rounded-t-lg relative" style="height: 160px;">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-teal-600 to-teal-400 rounded-t-lg transition-all duration-500" 
                                 style="height: {{ $maxSale > 0 ? ($day['total'] / $maxSale) * 100 : 0 }}%;">
                            </div>
                        </div>
                        <span class="text-xs text-gray-500 font-medium">{{ $day['date'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Recent Transactions</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($this->recentTransactions as $transaction)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm text-teal-600 font-medium">{{ $transaction->transaction_code }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-800">{{ $transaction->customer_name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $transaction->details->sum('quantity') }} items</td>
                            <td class="px-6 py-4 font-semibold text-gray-800">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $transaction->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p>No transactions yet today</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
