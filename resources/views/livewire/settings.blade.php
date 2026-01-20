<div class="flex-1 overflow-y-auto">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 px-4 md:px-6 py-4">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan</h1>
        <p class="text-gray-500 text-sm">Konfigurasi pengaturan restoran Anda</p>
    </div>

    <div class="p-4 md:p-6">
        {{-- Success Message --}}
        @if($saved)
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-green-800">Pengaturan berhasil disimpan!</p>
                    <p class="text-green-600 text-sm">Perubahan Anda telah diterapkan.</p>
                </div>
            </div>
        @endif

        {{-- Settings Form --}}
        <form wire:submit="save" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Restaurant Info Section --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800">Informasi Restoran</h3>
                <p class="text-gray-500 text-sm">Informasi ini akan muncul di struk</p>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Restoran *</label>
                    <input type="text" wire:model="restaurantName"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
                        placeholder="Masukkan nama restoran">
                    @error('restaurantName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                    <textarea wire:model="restaurantAddress" rows="3"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all resize-none"
                        placeholder="Masukkan alamat restoran"></textarea>
                    @error('restaurantAddress') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Tax Settings Section --}}
            <div class="px-6 py-4 border-t border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800">Pengaturan Pajak</h3>
                <p class="text-gray-500 text-sm">Konfigurasi perhitungan pajak untuk pesanan</p>
            </div>
            <div class="p-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Persentase Pajak (%)</label>
                    <div class="relative w-48">
                        <input type="number" wire:model="taxPercentage" min="0" max="100" step="0.1"
                            class="w-full px-4 py-3 pr-12 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">%</span>
                    </div>
                    @error('taxPercentage') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    <p class="text-gray-500 text-xs mt-2">Persentase ini akan ditambahkan ke subtotal</p>
                </div>

                {{-- Discount Section --}}
                <div class="mt-8">
                    <div class="rounded-2xl border {{ $discountEnabled ? 'border-teal-200 bg-teal-50/30' : 'border-gray-200 bg-gray-50/50' }} transition-all duration-300 overflow-hidden">
                        {{-- Header & Toggle --}}
                        <div class="p-6 flex items-center justify-between cursor-pointer" wire:click="$toggle('discountEnabled')">
                            <div class="flex-1 flex items-center gap-3">
                                <div class="bg-teal-100/50 p-2 rounded-lg transition-colors duration-300 {{ $discountEnabled ? 'bg-teal-100 text-teal-600' : 'bg-gray-100 text-gray-400' }}">
                                    <svg class="w-6 h-6 transition-transform duration-500 ease-in-out {{ $discountEnabled ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold transition-colors duration-300 {{ $discountEnabled ? 'text-teal-900' : 'text-gray-700' }}">Pengaturan Diskon</h3>
                                    <p class="text-sm mt-1 transition-colors duration-300 {{ $discountEnabled ? 'text-teal-600' : 'text-gray-500' }}">
                                        {{ $discountEnabled ? 'Diskon otomatis aktif' : 'Aktifkan untuk memberikan potongan harga otomatis' }}
                                    </p>
                                </div>
                            </div>
                            
                            </div>
                        </div>

                        {{-- Collapsible Content --}}
                        <div class="transition-all duration-500 ease-in-out overflow-hidden"
                             style="{{ $discountEnabled ? 'max-height: 1000px; opacity: 1;' : 'max-height: 0px; opacity: 0;' }}">
                            <div class="p-6 pt-0 space-y-6">
                                    <hr class="border-teal-100/50 mb-6">
                                    
                                    {{-- Percentage Input --}}
                                    <div class="bg-white rounded-xl p-5 border border-teal-100 shadow-sm">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">Besar Diskon</label>
                                        <p class="text-gray-500 text-sm mb-3">Persentase ini akan otomatis memotong total belanja pelanggan ketika syarat terpenuhi.</p>
                                        <div class="relative w-full md:w-40">
                                            <input type="number" wire:model="discountPercentage" min="0" max="100" step="0.1"
                                                class="w-full text-lg font-bold text-center px-4 py-3 pr-12 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all">
                                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</span>
                                        </div>
                                        @error('discountPercentage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Conditions Grid --}}
                                    <div class="grid md:grid-cols-2 gap-6">
                                        {{-- Condition Type --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Syarat Diskon</label>
                                            <div class="grid grid-cols-2 gap-3">
                                                <label class="cursor-pointer group relative">
                                                    <input type="radio" wire:model.live="discountConditionMode" value="total" class="peer sr-only">
                                                    <div class="p-4 rounded-xl border-2 border-gray-200 bg-white hover:border-teal-300 peer-checked:bg-teal-50 peer-checked:border-teal-500 peer-checked:text-teal-700 transition-all text-center h-full flex flex-col items-center justify-center gap-2 shadow-sm group-active:scale-95">
                                                        <div class="bg-teal-100 p-2 rounded-full text-teal-600 mb-1">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        </div>
                                                        <span class="font-bold text-sm">Total Belanja</span>
                                                        <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                            <svg class="w-4 h-4 text-teal-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                        </div>
                                                    </div>
                                                </label>
                                                <label class="cursor-pointer group relative">
                                                    <input type="radio" wire:model.live="discountConditionMode" value="quantity" class="peer sr-only">
                                                    <div class="p-4 rounded-xl border-2 border-gray-200 bg-white hover:border-teal-300 peer-checked:bg-teal-50 peer-checked:border-teal-500 peer-checked:text-teal-700 transition-all text-center h-full flex flex-col items-center justify-center gap-2 shadow-sm group-active:scale-95">
                                                        <div class="bg-blue-100 p-2 rounded-full text-blue-600 mb-1">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                        </div>
                                                        <span class="font-bold text-sm">Jumlah Barang</span>
                                                        <div class="absolute top-2 right-2 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                            <svg class="w-4 h-4 text-teal-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Threshold Input --}}
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                                @if($discountConditionMode === 'quantity')
                                                    Minimal Barang
                                                @else
                                                    Minimal Total (Rp)
                                                @endif
                                            </label>
                                            
                                            <div class="bg-white p-1 rounded-xl border border-gray-200 shadow-sm focus-within:ring-2 focus-within:ring-teal-500 focus-within:border-teal-500 transition-all">
                                                @if($discountConditionMode === 'quantity')
                                                    <div class="relative">
                                                        <input type="number" wire:model="discountMinItems" min="1"
                                                            class="w-full pl-4 pr-12 py-3 bg-transparent border-none focus:ring-0 text-lg font-semibold text-gray-800 placeholder-gray-300">
                                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-medium text-gray-400">Item</span>
                                                    </div>
                                                    @error('discountMinItems') <span class="text-red-500 text-xs mt-1 px-4 mb-2 block">{{ $message }}</span> @enderror
                                                @else
                                                    <div class="relative">
                                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-medium">Rp</span>
                                                        <input type="number" wire:model="discountMinTotal" min="0" step="1000"
                                                            class="w-full pl-12 pr-4 py-3 bg-transparent border-none focus:ring-0 text-lg font-semibold text-gray-800 placeholder-gray-300">
                                                    </div>
                                                    @error('discountMinTotal') <span class="text-red-500 text-xs mt-1 px-4 mb-2 block">{{ $message }}</span> @enderror
                                                @endif
                                            </div>
                                            <p class="text-gray-400 text-xs mt-2 ml-1">
                                                @if($discountConditionMode === 'quantity')
                                                    Diskon aktif jika keranjang berisi minimal jumlah ini.
                                                @else
                                                    Diskon aktif jika subtotal mencapai jumlah ini.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Save Button --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-xl transition-colors shadow-lg shadow-teal-600/30">
                    <svg wire:loading wire:target="save" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <svg wire:loading.remove wire:target="save" class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </form>

        {{-- Data Management Section --}}
        <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800">Manajemen Data</h3>
                <p class="text-gray-500 text-sm">Operasi database dan cache</p>
            </div>
            <div class="p-6 space-y-4">
                {{-- Clear Cache --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="font-medium text-gray-800">Bersihkan Cache</p>
                        <p class="text-gray-500 text-sm">Bersihkan cache aplikasi untuk mempercepat performa</p>
                    </div>
                    <button type="button" wire:click="clearCache" wire:loading.attr="disabled" wire:target="clearCache"
                        class="w-full md:w-auto shrink-0 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors text-sm inline-flex items-center justify-center gap-2">
                        <svg wire:loading wire:target="clearCache" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Bersihkan</span>
                    </button>
                </div>

                {{-- Backup Data --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4 bg-blue-50 rounded-xl">
                    <div>
                        <p class="font-medium text-blue-800">Backup Data</p>
                        <p class="text-blue-600 text-sm">Download seluruh data sebagai file backup</p>
                    </div>
                    <button type="button" wire:click="downloadBackup" wire:loading.attr="disabled" wire:target="downloadBackup"
                        class="w-full md:w-auto shrink-0 px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium rounded-lg transition-colors text-sm inline-flex items-center justify-center gap-2">
                        <svg wire:loading wire:target="downloadBackup" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg wire:loading.remove wire:target="downloadBackup" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Download Backup</span>
                    </button>
                </div>

                {{-- Restore Data --}}
                <div class="p-4 bg-green-50 rounded-xl">
                    <div class="mb-3">
                        <p class="font-medium text-green-800">Restore Data</p>
                        <p class="text-green-600 text-sm">Upload file backup untuk mengembalikan data</p>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center gap-3">
                        <input type="file" wire:model="restoreFile" accept=".json" id="restore-file-input"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-100 file:text-green-700 hover:file:bg-green-200 cursor-pointer">
                        <button type="button" id="restore-btn" @if(!$restoreFile) disabled @endif
                            class="w-full md:w-auto shrink-0 px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-medium rounded-lg transition-colors text-sm inline-flex items-center justify-center gap-2">
                            <svg wire:loading wire:target="restoreData" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg wire:loading.remove wire:target="restoreData" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span>Restore</span>
                        </button>
                    </div>
                    @error('restoreFile') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                {{-- Reset Data (Danger Zone) --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-4 bg-red-50 rounded-xl border border-red-200">
                    <div>
                        <p class="font-medium text-red-800">⚠️ Reset Semua Data</p>
                        <p class="text-red-600 text-sm">Ini akan menghapus semua transaksi, produk, dan kategori secara permanen</p>
                    </div>
                    <button type="button" id="reset-btn"
                        class="w-full md:w-auto shrink-0 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors text-sm">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            // Settings saved
            Livewire.on('settings-saved', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Pengaturan berhasil disimpan.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d9488',
                    timer: 3000,
                    timerProgressBar: true
                });
            });

            // Cache cleared
            Livewire.on('cache-cleared', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Cache aplikasi berhasil dibersihkan.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d9488',
                    timer: 3000,
                    timerProgressBar: true
                });
            });

            // Data reset
            Livewire.on('data-reset', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Data Direset!',
                    text: 'Semua data transaksi dan produk telah dihapus.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d9488'
                });
            });

            // Data restored
            Livewire.on('data-restored', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data berhasil dipulihkan dari file backup.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d9488'
                }).then(() => {
                    window.location.reload();
                });
            });

            // Restore error
            Livewire.on('restore-error', (data) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message || 'Terjadi kesalahan saat restore data.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc2626'
                });
            });
        });

        // Reset button with RESET confirmation
        document.getElementById('reset-btn').addEventListener('click', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                html: `
                    <p class="mb-4">Tindakan ini akan <strong>menghapus semua data</strong> secara permanen:</p>
                    <ul class="text-left text-sm mb-4 list-disc list-inside">
                        <li>Semua transaksi</li>
                        <li>Semua produk & kategori</li>
                        <li>Semua pengeluaran</li>
                        <li>Semua data logistik</li>
                    </ul>
                    <p class="text-red-600 font-bold">Ketik "RESET" untuk mengkonfirmasi:</p>
                `,
                input: 'text',
                inputPlaceholder: 'Ketik RESET di sini',
                showCancelButton: true,
                confirmButtonText: 'Hapus Semua Data',
                confirmButtonColor: '#dc2626',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (value !== 'RESET') {
                        return 'Ketik "RESET" dengan benar untuk melanjutkan';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('resetData');
                }
            });
        });

        // Restore button with confirmation
        document.getElementById('restore-btn').addEventListener('click', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Konfirmasi Restore',
                html: `
                    <p class="mb-2">Data saat ini akan <strong>ditimpa</strong> dengan data dari file backup.</p>
                    <p class="text-sm text-gray-500">Pastikan Anda sudah membuat backup data saat ini jika diperlukan.</p>
                `,
                showCancelButton: true,
                confirmButtonText: 'Ya, Restore Data',
                confirmButtonColor: '#16a34a',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('restoreData');
                }
            });
        });
    </script>
@endpush