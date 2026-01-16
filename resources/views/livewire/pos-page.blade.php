<div x-data="{ cartOpen: false }" x-on:checkout-success.window="setTimeout(() => window.print(), 500)"
    class="flex-1 flex h-full overflow-x-hidden">

    {{-- Success Modal --}}
    @if($showSuccess)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl animate-bounce-in" wire:click.stop>
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Berhasil!</h3>
                <p class="text-gray-500 mb-1">Kode Transaksi:</p>
                <p class="text-teal-600 font-mono font-bold text-lg mb-4">{{ $lastTransactionCode }}</p>
                <p class="text-gray-400 text-sm mb-6">Struk sedang dicetak...</p>
                <button wire:click="newOrder"
                    class="w-full py-3 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl transition-colors">
                    Pesanan Baru
                </button>
            </div>
        </div>
    @endif

    {{-- Note Edit Modal --}}
    @if($showNoteModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4"
            wire:click.self="closeNoteModal">
            <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl animate-bounce-in">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Tambah Catatan / Kustomisasi</h3>
                <textarea wire:model="itemNote" rows="3" placeholder="Contoh: Es sedikit, Pedas ekstra, Tanpa bawang..."
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none"></textarea>
                <div class="flex gap-3 mt-4">
                    <button wire:click="closeNoteModal"
                        class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                        Batal
                    </button>
                    <button wire:click="saveNote"
                        class="flex-1 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl transition-colors">
                        Simpan Catatan
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Payment Modal --}}
    @if($showPaymentModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4"
            wire:click.self="closePaymentModal">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl animate-bounce-in overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4 text-white">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold">Pembayaran</h3>
                        <button wire:click="closePaymentModal" class="p-1 hover:bg-white/20 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-teal-100 text-sm mt-1">Selesaikan transaksi Anda</p>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Total --}}
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <p class="text-gray-500 text-sm">Total Pembayaran</p>
                        <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($this->total, 0, ',', '.') }}</p>
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button wire:click="$set('paymentMethod', 'cash')"
                                class="py-3 px-4 rounded-xl font-medium text-sm transition-all flex flex-col items-center gap-1
                                                                {{ $paymentMethod === 'cash' ? 'bg-teal-600 text-white ring-2 ring-teal-600 ring-offset-2' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Cash
                            </button>
                            <button wire:click="$set('paymentMethod', 'qris')"
                                class="py-3 px-4 rounded-xl font-medium text-sm transition-all flex flex-col items-center gap-1
                                                                {{ $paymentMethod === 'qris' ? 'bg-teal-600 text-white ring-2 ring-teal-600 ring-offset-2' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                QRIS
                            </button>
                            <button wire:click="$set('paymentMethod', 'debit')"
                                class="py-3 px-4 rounded-xl font-medium text-sm transition-all flex flex-col items-center gap-1
                                                                {{ $paymentMethod === 'debit' ? 'bg-teal-600 text-white ring-2 ring-teal-600 ring-offset-2' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Debit
                            </button>
                        </div>
                    </div>

                    {{-- Cash Input (only for cash) --}}
                    @if($paymentMethod === 'cash')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Uang Diterima</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
                                <input type="number" wire:model.live="amountReceived"
                                    class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-lg font-semibold focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                    placeholder="0">
                            </div>
                            {{-- Quick Cash Buttons --}}
                            <div class="grid grid-cols-4 gap-2 mt-3">
                                <button wire:click="setQuickCash('exact')"
                                    class="py-2 bg-teal-50 hover:bg-teal-100 text-teal-700 text-sm font-medium rounded-lg transition-colors">
                                    Pas
                                </button>
                                <button wire:click="setQuickCash('50000')"
                                    class="py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                                    50K
                                </button>
                                <button wire:click="setQuickCash('100000')"
                                    class="py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                                    100K
                                </button>
                                <button wire:click="setQuickCash('200000')"
                                    class="py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                                    200K
                                </button>
                            </div>
                        </div>

                        {{-- Change Display --}}
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-200">
                            <div class="flex items-center justify-between">
                                <span class="text-green-700 font-medium">Kembalian</span>
                                <span class="text-2xl font-bold text-green-700">Rp
                                    {{ number_format($this->change, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <button wire:click="confirmPayment" wire:loading.attr="disabled" @if(!$this->canPay) disabled @endif
                        class="w-full py-3.5 bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white font-semibold rounded-xl transition-all shadow-lg shadow-teal-600/30 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg wire:loading wire:target="confirmPayment" class="w-5 h-5 animate-spin" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <svg wire:loading.remove wire:target="confirmPayment" class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Konfirmasi Pembayaran & Cetak</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Mobile Cart Button --}}
    <button x-on:click="cartOpen = true"
        class="lg:hidden fixed bottom-6 right-6 z-30 w-14 h-14 bg-teal-600 hover:bg-teal-700 text-white rounded-full shadow-lg shadow-teal-600/40 flex items-center justify-center transition-all">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        @if($this->cartCount > 0)
            <span
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-6 h-6 flex items-center justify-center rounded-full font-bold">
                {{ $this->cartCount }}
            </span>
        @endif
    </button>

    {{-- Center: Product Catalog --}}
    <div class="flex-1 flex flex-col h-full min-w-0">
        {{-- Search & Category Header --}}
        <div class="bg-white border-b border-gray-200 p-4 flex-shrink-0">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Menu</h2>
                    <p class="text-gray-500 text-xs">Pilih item untuk ditambahkan</p>
                </div>
                {{-- Search Bar --}}
                <div class="relative w-full sm:w-64">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-100 border-0 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all">
                </div>
            </div>

            {{-- Category Tabs --}}
            <div class="flex flex-wrap gap-2 mt-3 w-full" style="flex-wrap: wrap !important;">
                <button wire:click="setCategory('all')"
                    class="px-4 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all {{ $category === 'all' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Semua
                </button>
                @foreach($categories as $cat)
                    <button wire:click="setCategory('{{ $cat->slug }}')"
                        class="px-4 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all {{ $category === $cat->slug ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $cat->icon }} {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Product Grid (ONLY SCROLLABLE AREA) --}}
        <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3">
                @forelse($this->products as $product)
                    <div wire:click="addToCart({{ $product->id }})" wire:key="product-{{ $product->id }}"
                        class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden group cursor-pointer">
                        <div class="relative h-24 overflow-hidden">
                            @php
                                $productImgUrl = $product->image_url
                                    ? (str_starts_with($product->image_url, 'http')
                                        ? $product->image_url
                                        : asset('storage/' . $product->image_url))
                                    : 'https://placehold.co/400x300/gray/white?text=No+Image';
                            @endphp
                            <img src="{{ $productImgUrl }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                            <div class="absolute top-1.5 left-1.5">
                                @php
                                    $productCategory = $categories->firstWhere('slug', $product->category);
                                @endphp
                                <span
                                    class="px-1.5 py-0.5 bg-white/90 backdrop-blur-sm rounded text-[10px] font-medium capitalize {{ $productCategory?->color ?? 'text-gray-600' }}">
                                    {{ $productCategory?->icon ?? '🏷️' }}
                                </span>
                            </div>
                            {{-- Quick Add Overlay --}}
                            <div
                                class="absolute inset-0 bg-teal-600/0 group-hover:bg-teal-600/30 transition-colors flex items-center justify-center">
                                <div
                                    class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transform scale-50 group-hover:scale-100 transition-all">
                                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-2.5">
                            <h3 class="font-semibold text-gray-800 text-sm truncate leading-tight">{{ $product->name }}</h3>
                            <span class="text-teal-600 font-bold text-sm">Rp
                                {{ number_format($product->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-12 text-gray-400">
                        <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="font-medium">Produk tidak ditemukan</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Cart Overlay (Mobile) --}}
    <div x-show="cartOpen" x-on:click="cartOpen = false" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="lg:hidden fixed inset-0 bg-black/50 z-40" x-cloak></div>

    {{-- Right Sidebar: Cart (FIXED HEIGHT, FLEX COLUMN) --}}
    <aside :class="cartOpen ? 'translate-x-0' : 'translate-x-full'"
        class="fixed lg:static inset-y-0 right-0 z-50 w-80 lg:w-[340px] bg-white border-l border-gray-200 lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col h-full flex-shrink-0">

        {{-- Cart Header --}}
        <div class="p-3 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-base font-bold text-gray-800">Pesanan Saat Ini</h3>
                <button x-on:click="cartOpen = false"
                    class="lg:hidden p-1.5 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Order Type Toggle --}}
            <div class="flex bg-gray-100 rounded-lg p-0.5">
                <button wire:click="setOrderType('dine_in')"
                    class="flex-1 py-1.5 px-2 text-xs font-medium rounded-md transition-all {{ $orderType === 'dine_in' ? 'bg-white text-teal-700 shadow-sm' : 'text-gray-600 hover:text-gray-800' }}">
                    🍽️ Makan di Tempat
                </button>
                <button wire:click="setOrderType('takeaway')"
                    class="flex-1 py-1.5 px-2 text-xs font-medium rounded-md transition-all {{ $orderType === 'takeaway' ? 'bg-white text-teal-700 shadow-sm' : 'text-gray-600 hover:text-gray-800' }}">
                    🥡 Bawa Pulang
                </button>
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="px-3 py-2 border-b border-gray-100 flex-shrink-0">
            @if($orderType === 'dine_in')
                <div class="grid grid-cols-5 gap-2">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Meja # <span
                                class="text-red-500">*</span></label>
                        <input type="number" wire:model.blur="tableNumber" placeholder="No."
                            class="w-full px-2 py-1.5 bg-gray-50 border rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent {{ $errors->has('tableNumber') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                        @error('tableNumber')
                            <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-3">
                        <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Pelanggan <span
                                class="text-red-500">*</span></label>
                        <input type="text" wire:model.blur="customerName" placeholder="Nama"
                            class="w-full px-2 py-1.5 bg-gray-50 border rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent {{ $errors->has('customerName') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                        @error('customerName')
                            <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @else
                <div>
                    <label class="block text-[10px] font-medium text-gray-500 mb-0.5">Nama Pelanggan <span
                            class="text-red-500">*</span></label>
                    <input type="text" wire:model.blur="customerName" placeholder="Masukkan nama pelanggan"
                        class="w-full px-2 py-1.5 bg-gray-50 border rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent {{ $errors->has('customerName') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                    @error('customerName')
                        <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        </div>

        {{-- Cart Items (SCROLLABLE) --}}
        <div class="flex-1 overflow-y-auto p-3 space-y-2">
            @forelse($cart as $index => $item)
                <div wire:key="cart-{{ $index }}" class="bg-gray-50 rounded-lg p-2">
                    <div class="flex gap-2">
                        @php
                            $cartImgUrl = $item['image_url']
                                ? (str_starts_with($item['image_url'], 'http')
                                    ? $item['image_url']
                                    : asset('storage/' . $item['image_url']))
                                : 'https://placehold.co/80x80/gray/white?text=No+Image';
                        @endphp
                        <img src="{{ $cartImgUrl }}" alt="{{ $item['name'] }}"
                            class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-gray-800 text-xs truncate">{{ $item['name'] }}</h4>
                            <p class="text-teal-600 font-semibold text-xs">Rp
                                {{ number_format($item['price'], 0, ',', '.') }}
                            </p>
                            @if($item['note'])
                                <p class="text-orange-600 text-[10px] mt-0.5 truncate">📝 {{ $item['note'] }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col items-center gap-0.5">
                            <button wire:click="openNoteModal({{ $index }})"
                                class="p-1 text-gray-400 hover:text-teal-600 hover:bg-teal-50 rounded transition-colors"
                                title="Tambah catatan">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button wire:click="removeFromCart({{ $index }})"
                                class="p-1 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-gray-200/50">
                        <div class="flex items-center gap-1.5">
                            <button wire:click="updateQuantity({{ $index }}, -1)"
                                class="w-6 h-6 bg-white border border-gray-200 rounded flex items-center justify-center text-gray-500 hover:bg-gray-100 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            <span class="text-xs font-semibold text-gray-800 w-5 text-center">{{ $item['quantity'] }}</span>
                            <button wire:click="updateQuantity({{ $index }}, 1)"
                                class="w-6 h-6 bg-teal-600 rounded flex items-center justify-center text-white hover:bg-teal-700 transition-colors">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                        <span class="font-semibold text-gray-800 text-xs">Rp
                            {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                    <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-sm font-medium">Keranjang kosong</p>
                    <p class="text-xs">Ketuk item untuk menambahkan</p>
                </div>
            @endforelse
        </div>

        {{-- Cart Footer (PINNED AT BOTTOM) --}}
        <div class="border-t border-gray-200 p-3 bg-white shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] flex-shrink-0">
            <div class="space-y-1 mb-3">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="text-gray-800">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Pajak (10%)</span>
                    <span class="text-gray-800">Rp {{ number_format($this->tax, 0, ',', '.') }}</span>
                </div>
                <div class="h-px bg-gray-200 my-1.5"></div>
                <div class="flex justify-between items-baseline">
                    <span class="text-gray-800 font-semibold text-sm">Total</span>
                    <span class="text-teal-600 font-bold text-xl">Rp
                        {{ number_format($this->total, 0, ',', '.') }}</span>
                </div>
            </div>
            <button wire:click="openPaymentModal" @if(count($cart) === 0) disabled @endif
                class="w-full py-3 bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 text-white font-semibold rounded-xl transition-all shadow-lg shadow-teal-600/30 flex items-center justify-center gap-2 disabled:from-gray-300 disabled:to-gray-400 disabled:shadow-none disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Lanjut ke Pembayaran
            </button>
        </div>
    </aside>

    {{-- Print Receipt --}}
    <div id="print-receipt" class="hidden print:block p-8 bg-white">
        <div class="max-w-xs mx-auto text-center">
            <h1 class="text-2xl font-bold mb-1">Kasir App</h1>
            <p class="text-gray-500 text-sm mb-4">Struk Restoran</p>
            <div class="border-t border-b border-dashed border-gray-300 py-3 my-4 text-left">
                <p class="text-sm"><strong>Kode:</strong> {{ $lastTransactionCode }}</p>
                <p class="text-sm"><strong>Tipe:</strong>
                    {{ $orderType === 'dine_in' ? 'Makan di Tempat' : 'Bawa Pulang' }}</p>
                @if($orderType === 'dine_in' && $tableNumber)
                    <p class="text-sm"><strong>Meja:</strong> {{ $tableNumber }}</p>
                @endif
                <p class="text-sm"><strong>Pelanggan:</strong> {{ $customerName }}</p>
                <p class="text-sm"><strong>Tanggal:</strong> {{ now()->format('d M Y, H:i') }}</p>
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
                    @foreach($cart as $item)
                        <tr class="border-b border-gray-100">
                            <td class="text-left py-2">
                                {{ $item['name'] }}
                                @if($item['note'])<br><span class="text-xs text-gray-500">→ {{ $item['note'] }}</span>@endif
                            </td>
                            <td class="text-center py-2">{{ $item['quantity'] }}</td>
                            <td class="text-right py-2">Rp
                                {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="border-t border-dashed border-gray-300 pt-3 space-y-1 text-left">
                <div class="flex justify-between text-sm"><span>Subtotal</span><span>Rp
                        {{ number_format($this->subtotal, 0, ',', '.') }}</span></div>
                <div class="flex justify-between text-sm"><span>Tax (10%)</span><span>Rp
                        {{ number_format($this->tax, 0, ',', '.') }}</span></div>
                <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200">
                    <span>Total</span><span>Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                </div>
                @if($paymentMethod === 'cash')
                    <div class="flex justify-between text-sm pt-2"><span>Tunai</span><span>Rp
                            {{ number_format($amountReceived ?: 0, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between text-sm"><span>Kembalian</span><span>Rp
                            {{ number_format($this->change, 0, ',', '.') }}</span></div>
                @else
                    <div class="flex justify-between text-sm pt-2"><span>Dibayar via</span><span
                            class="uppercase">{{ $paymentMethod }}</span></div>
                @endif
            </div>
            <p class="text-gray-400 text-xs mt-6">Terima kasih atas pesanan Anda!</p>
        </div>
    </div>
</div>