<div class="flex-1 overflow-y-auto">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Menu</h1>
                <p class="text-gray-500 text-sm">Kelola item menu restoran Anda</p>
            </div>
            <button wire:click="openModal"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl transition-colors shadow-lg shadow-teal-600/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Produk
            </button>
        </div>
    </div>

    <div class="p-6">
        {{-- Filters --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                {{-- Search --}}
                <div class="relative flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari produk..."
                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                {{-- Category Filter --}}
                <select wire:model.live="categoryFilter"
                    class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}">{{ $cat->icon }} {{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Batch Action Bar --}}
        @if(count($selectedIds) > 0)
            <div class="bg-teal-50 border border-teal-200 rounded-xl p-4 mb-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-teal-600 text-white rounded-full text-sm font-bold">
                        {{ count($selectedIds) }}
                    </span>
                    <span class="text-teal-800 font-medium">Item Dipilih</span>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="$set('selectedIds', [])" wire:click="$set('selectAll', false)"
                        class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-600 font-medium rounded-xl border border-gray-200 transition-colors">
                        Batal Pilih
                    </button>
                    <button wire:click="confirmBatchDelete"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Terpilih
                    </button>
                </div>
            </div>
        @endif

        {{-- Products Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 w-12">
                                <input type="checkbox" wire:model.live="selectAll"
                                    class="w-4 h-4 text-teal-600 bg-gray-100 border-gray-300 rounded focus:ring-teal-500 focus:ring-2 cursor-pointer">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Harga</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($products as $product)
                            <tr wire:key="product-{{ $product->id }}" class="hover:bg-gray-50 transition-colors {{ in_array((string)$product->id, $selectedIds) ? 'bg-teal-50' : '' }}">
                                <td class="px-4 py-4">
                                    <input type="checkbox" wire:model.live="selectedIds" value="{{ $product->id }}"
                                        class="w-4 h-4 text-teal-600 bg-gray-100 border-gray-300 rounded focus:ring-teal-500 focus:ring-2 cursor-pointer">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $imageUrl = $product->image_url 
                                                ? (str_starts_with($product->image_url, 'http') 
                                                    ? $product->image_url 
                                                    : asset('storage/' . $product->image_url))
                                                : 'https://placehold.co/80x80/gray/white?text=No+Image';
                                        @endphp
                                        <img src="{{ $imageUrl }}"
                                            alt="{{ $product->name }}" class="w-12 h-12 rounded-lg object-cover">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                            <p class="text-gray-500 text-xs truncate max-w-xs">{{ $product->description }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $productCategory = $categories->firstWhere('slug', $product->category);
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium capitalize {{ $productCategory?->bg_color ?? 'bg-gray-100' }} {{ $productCategory?->color ?? 'text-gray-700' }}">
                                        {{ $productCategory?->icon ?? '🏷️' }}
                                        {{ $productCategory?->name ?? $product->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="openModal({{ $product->id }})"
                                            class="p-2 text-gray-500 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $product->id }})"
                                            class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p>Produk tidak ditemukan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="closeModal">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg animate-bounce-in max-h-[90vh] flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                    <h3 class="text-lg font-bold text-gray-800">
                        {{ $editingId ? 'Edit Produk' : 'Tambah Produk Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form wire:submit="save" class="p-6 space-y-4 overflow-y-auto flex-1">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk *</label>
                        <input type="text" wire:model="name"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                            <select wire:model="category"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->slug }}">{{ $cat->icon }} {{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) *</label>
                            <input type="number" wire:model="price" min="0"
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            @error('price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk</label>

                        {{-- Image Preview Area --}}
                        @if($image)
                            {{-- Preview of newly uploaded image --}}
                            <div class="mb-3 flex items-start gap-3">
                                <div class="relative flex-shrink-0">
                                    <img src="{{ $image->temporaryUrl() }}" alt="New image preview"
                                        class="w-20 h-20 rounded-xl object-cover border-2 border-teal-500 shadow-md"
                                        style="max-width: 80px; max-height: 80px; min-width: 80px; min-height: 80px;">
                                    <button type="button" wire:click="$set('image', null)"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-md transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="flex flex-col justify-center">
                                    <p class="text-xs text-teal-600 font-medium">Gambar baru dipilih</p>
                                    <p class="text-xs text-gray-400">Siap diunggah</p>
                                </div>
                            </div>
                        @elseif($editingId && $existing_image_url)
                            {{-- Preview of existing image (Edit mode) --}}
                            <div class="mb-3 flex items-start gap-3">
                                <div class="relative flex-shrink-0">
                                    @php
                                        $existingImgUrl = $existing_image_url
                                            ? (str_starts_with($existing_image_url, 'http')
                                                ? $existing_image_url
                                                : asset('storage/' . $existing_image_url))
                                            : '';
                                    @endphp
                                    <img src="{{ $existingImgUrl }}" alt="Current product image"
                                        class="w-20 h-20 rounded-xl object-cover border border-gray-200 shadow-sm"
                                        style="max-width: 80px; max-height: 80px; min-width: 80px; min-height: 80px;">
                                </div>
                                <div class="flex flex-col justify-center">
                                    <p class="text-xs text-gray-600 font-medium">Gambar saat ini</p>
                                    <p class="text-xs text-gray-400">Pilih file baru untuk mengganti</p>
                                </div>
                            </div>
                        @endif

                        {{-- File Upload Input --}}
                        <div class="relative">
                            <input type="file" wire:model="image" accept="image/*" id="image-upload"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-center gap-2 hover:bg-gray-100 transition-colors cursor-pointer">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm text-gray-600">
                                    {{ $editingId && $existing_image_url ? 'Ganti Gambar' : 'Pilih Gambar' }}
                                </span>
                            </div>
                        </div>

                        {{-- Loading indicator --}}
                        <div wire:loading wire:target="image" class="mt-2">
                            <div class="flex items-center gap-2 text-teal-600 text-sm">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span>Mengunggah...</span>
                            </div>
                        </div>

                        @error('image') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-400 mt-1">Ukuran maks: 2MB. Didukung: JPG, PNG, GIF, WebP</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea wire:model="description" rows="3"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-none"></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" wire:click="closeModal"
                            class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl transition-colors">
                            {{ $editingId ? 'Perbarui' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="cancelDelete">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm animate-bounce-in text-center p-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Hapus Produk?</h3>
                <p class="text-gray-500 text-sm mb-6">Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex gap-3">
                    <button wire:click="cancelDelete"
                        class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                        Batal
                    </button>
                    <button wire:click="delete"
                        class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Batch Delete Confirmation Modal --}}
    @if($showBatchDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="cancelBatchDelete">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm animate-bounce-in text-center p-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Hapus {{ count($selectedIds) }} Produk?</h3>
                <p class="text-gray-500 text-sm mb-6">Semua produk yang dipilih akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex gap-3">
                    <button wire:click="cancelBatchDelete"
                        class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                        Batal
                    </button>
                    <button wire:click="batchDelete"
                        class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors">
                        Hapus Semua
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('product-saved', (data) => {
                const params = data[0];
                
                let message = '';
                if (params.type === 'created') {
                    message = `Menu baru "${params.name}" telah ditambahkan.`;
                } else if (params.type === 'updated') {
                    message = `Menu "${params.name}" telah diperbarui.`;
                } else if (params.type === 'deleted') {
                    message = `Menu "${params.name}" telah dihapus.`;
                } else if (params.type === 'batch-deleted') {
                    message = `${params.count} menu telah dihapus.`;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d9488',
                    timer: 3000,
                    timerProgressBar: true,
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    }
                });
            });
        });
    </script>
@endpush