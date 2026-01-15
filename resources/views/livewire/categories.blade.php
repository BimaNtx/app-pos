<div class="flex-1 overflow-y-auto">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Kategori Menu</h1>
                <p class="text-gray-500 text-sm">Kelola kategori untuk menu restoran</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-4 py-2 bg-teal-50 rounded-xl border border-teal-200">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span class="font-semibold text-teal-700">{{ $categories->total() }}</span>
                    <span class="text-teal-600 text-sm">Kategori</span>
                </div>
                <button wire:click="openModal"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl transition-colors shadow-lg shadow-teal-600/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kategori
                </button>
            </div>
        </div>
    </div>

    <div class="p-6">
        {{-- Search --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kategori..."
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        {{-- Categories Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            @forelse($categories as $category)
                <div wire:key="category-{{ $category->id }}"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="w-14 h-14 {{ $category->bg_color }} rounded-xl flex items-center justify-center text-2xl">
                                {{ $category->icon }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-800 text-lg truncate">{{ $category->name }}</h3>
                                <p class="text-gray-500 text-sm">Slug: {{ $category->slug }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-medium {{ $category->bg_color }} {{ $category->color }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                {{ $category->products_count }} menu
                            </span>
                            <div class="flex items-center gap-1">
                                <button wire:click="openModal({{ $category->id }})"
                                    class="p-2 text-gray-500 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-colors"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="confirmDelete({{ $category->id }})"
                                    class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Tidak ada kategori ditemukan</h3>
                    <p class="text-gray-500 mb-4">Coba ubah kata kunci pencarian atau tambah kategori baru</p>
                    <button wire:click="openModal"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Kategori
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($categories->hasPages())
            <div class="mb-6">
                {{ $categories->links() }}
            </div>
        @endif

        {{-- Info Card --}}
        <div class="bg-gradient-to-r from-teal-50 to-emerald-50 rounded-2xl border border-teal-100 p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-teal-800 mb-1">Informasi Kategori</h4>
                    <p class="text-teal-700 text-sm">
                        Kategori digunakan untuk mengelompokkan menu di halaman kasir dan laporan.
                        Total <strong>{{ $totalProducts }}</strong> menu terdaftar di semua kategori.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" wire:click.self="closeModal">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md animate-bounce-in overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">
                        {{ $editingId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form wire:submit="save" class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori *</label>
                        <input type="text" wire:model="name"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            placeholder="Contoh: Makanan">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Icon Picker --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ikon *</label>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                            <div style="display: grid; grid-template-columns: repeat(8, 1fr); gap: 6px;">
                                @foreach($iconOptions as $iconOption)
                                    <button type="button" wire:click="selectIcon('{{ $iconOption }}')"
                                        style="width: 100%; aspect-ratio: 1; font-size: 1.25rem;"
                                        class="rounded-lg flex items-center justify-center transition-all {{ $icon === $iconOption ? 'bg-teal-500 text-white shadow-md' : 'bg-white hover:bg-gray-100 border border-gray-200' }}">
                                        {{ $iconOption }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Color Picker --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna *</label>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-200">
                            <div style="display: grid; grid-template-columns: repeat(9, 1fr); gap: 6px;">
                                @foreach($colorOptions as $option)
                                    <button type="button"
                                        wire:click="selectColor('{{ $option['color'] }}', '{{ $option['bg'] }}')"
                                        style="width: 100%; aspect-ratio: 1;"
                                        class="rounded-lg {{ $option['bg'] }} transition-all flex items-center justify-center {{ $selectedColor === $option['color'] ? 'ring-2 ring-offset-1 ring-teal-500' : 'hover:scale-105' }}"
                                        title="{{ $option['label'] }}">
                                        @if($selectedColor === $option['color'])
                                            <svg class="w-4 h-4 {{ $option['color'] }}" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200">
                            <div
                                class="w-12 h-12 {{ $selectedBg }} rounded-lg flex items-center justify-center text-xl flex-shrink-0">
                                {{ $icon }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-800 truncate">{{ $name ?: 'Nama Kategori' }}</p>
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $selectedBg }} {{ $selectedColor }}">
                                    0 menu
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" wire:click="closeModal"
                            class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl transition-colors">
                            {{ $editingId ? 'Simpan' : 'Tambah' }}
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
                <h3 class="text-lg font-bold text-gray-800 mb-2">Hapus Kategori?</h3>
                <p class="text-gray-500 text-sm mb-6">Kategori yang memiliki produk tidak dapat dihapus.</p>
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
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('category-saved', (data) => {
                const params = data[0];
                const isCreated = params.type === 'created';

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: isCreated
                        ? `Kategori "${params.name}" telah ditambahkan.`
                        : `Kategori "${params.name}" telah diperbarui.`,
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

            Livewire.on('category-deleted', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Kategori telah dihapus.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d9488',
                    timer: 2000,
                    timerProgressBar: true
                });
            });

            Livewire.on('category-delete-error', (data) => {
                const params = data[0];
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: params.message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc2626'
                });
            });
        });
    </script>
@endpush