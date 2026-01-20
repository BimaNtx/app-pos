<div>
    {{-- Page Header --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4 flex-shrink-0">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Karyawan</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola data karyawan dan hak akses</p>
            </div>
            <button wire:click="openModal"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="font-medium">Tambah Karyawan</span>
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white border-b border-gray-200 px-6 py-4 flex-shrink-0">
        <div class="flex flex-col sm:flex-row gap-4">
            {{-- Search --}}
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Cari nama atau email karyawan..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors">
            </div>

            {{-- Position Filter --}}
            <select wire:model.live="positionFilter"
                class="px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white">
                <option value="all">Semua Posisi</option>
                <option value="admin">Administrator</option>
                <option value="kasir">Kasir</option>
                <option value="chef">Chef / Dapur</option>
            </select>

            {{-- Status Filter --}}
            <select wire:model.live="statusFilter"
                class="px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white">
                <option value="all">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>
        </div>
    </div>

    {{-- Content Container --}}
    <div class="">
        {{-- Mobile Card View --}}
        <div class="p-4 lg:hidden">
            <div class="space-y-3">
                @forelse($employees as $employee)
                    <div wire:key="mobile-employee-{{ $employee->id }}"
                        class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        {{-- Header: Avatar + Name + Status --}}
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                @if($employee->avatar_url)
                                    <img src="{{ $employee->avatar_url }}" alt="{{ $employee->name }}"
                                        class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center">
                                        <span class="text-teal-600 font-semibold">{{ substr($employee->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $employee->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $employee->email }}</p>
                                </div>
                            </div>
                            <button wire:click="toggleStatus({{ $employee->id }})"
                                class="inline-flex items-center gap-1.5 cursor-pointer" title="Klik untuk mengubah status">
                                @if($employee->is_active)
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                    <span class="text-xs text-green-600">Aktif</span>
                                @else
                                    <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                    <span class="text-xs text-gray-500">Nonaktif</span>
                                @endif
                            </button>
                        </div>

                        {{-- Info: Position + Phone --}}
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span @class([
                                'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                                'bg-purple-100 text-purple-700' => $employee->position === 'admin',
                                'bg-blue-100 text-blue-700' => $employee->position === 'kasir',
                                'bg-orange-100 text-orange-700' => $employee->position === 'chef',
                            ])>
                                {{ $employee->position_label }}
                            </span>
                            @if($employee->is_admin)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">
                                    Admin
                                </span>
                            @endif
                            @if($employee->phone)
                                <span class="text-xs text-gray-500">
                                    <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $employee->phone }}
                                </span>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-1 pt-3 border-t border-gray-100">
                            <button wire:click="openModal({{ $employee->id }})"
                                class="p-2 text-gray-500 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-colors"
                                title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button wire:click="confirmResetPassword({{ $employee->id }})"
                                class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                title="Reset Password">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </button>
                            <button wire:click="confirmDelete({{ $employee->id }})"
                                class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <p class="text-gray-500 font-medium">Tidak ada data karyawan</p>
                        <p class="text-gray-400 text-sm mt-1">Klik tombol "Tambah Karyawan" untuk menambahkan data baru</p>
                    </div>
                @endforelse
            </div>

            {{-- Mobile Pagination --}}
            @if($employees->hasPages())
                <div class="mt-4">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>

        {{-- Desktop Table View --}}
        <div class="p-6 hidden lg:block">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Karyawan</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Posisi</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Kontak</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($employees as $employee)
                                <tr wire:key="desktop-employee-{{ $employee->id }}" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        @if($employee->avatar_url)
                                            <img src="{{ $employee->avatar_url }}" alt="{{ $employee->name }}"
                                                class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center">
                                                <span
                                                    class="text-teal-600 font-semibold text-sm">{{ substr($employee->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $employee->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $employee->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span @class([
                                        'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                                        'bg-purple-100 text-purple-700' => $employee->position === 'admin',
                                        'bg-blue-100 text-blue-700' => $employee->position === 'kasir',
                                        'bg-orange-100 text-orange-700' => $employee->position === 'chef',
                                    ])>
                                        {{ $employee->position_label }}
                                    </span>
                                    @if($employee->is_admin)
                                        <span
                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">
                                            Admin
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $employee->phone ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="toggleStatus({{ $employee->id }})"
                                        class="inline-flex items-center gap-1.5 cursor-pointer group"
                                        title="Klik untuk mengubah status">
                                        @if($employee->is_active)
                                            <span class="w-2 h-2 bg-green-500 rounded-full group-hover:bg-green-600"></span>
                                            <span class="text-sm text-green-600 group-hover:text-green-700">Aktif</span>
                                        @else
                                            <span class="w-2 h-2 bg-gray-400 rounded-full group-hover:bg-gray-500"></span>
                                            <span class="text-sm text-gray-500 group-hover:text-gray-600">Nonaktif</span>
                                        @endif
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Edit --}}
                                        <button wire:click="openModal({{ $employee->id }})"
                                            class="p-2 text-gray-500 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-colors"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        {{-- Reset Password --}}
                                        <button wire:click="confirmResetPassword({{ $employee->id }})"
                                            class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                            title="Reset Password">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                            </svg>
                                        </button>
                                        {{-- Delete --}}
                                        <button wire:click="confirmDelete({{ $employee->id }})"
                                            class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus">
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
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="text-gray-500 font-medium">Tidak ada data karyawan</p>
                                        <p class="text-gray-400 text-sm mt-1">Klik tombol "Tambah Karyawan" untuk
                                            menambahkan
                                            data baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if($employees->hasPages())
                <div class="mt-4">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-init="$el.querySelector('input[name=name]')?.focus()">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 bg-gray-900/50 transition-opacity" wire:click="closeModal"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full mx-4 overflow-hidden animate-bounce-in">
                    {{-- Modal Header --}}
                    <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white">
                            {{ $editingId ? 'Edit Karyawan' : 'Tambah Karyawan Baru' }}
                        </h3>
                    </div>

                    {{-- Modal Body --}}
                    <form wire:submit="save" class="p-6 space-y-4">
                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" wire:model="name"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="Masukkan nama lengkap">
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" wire:model="email"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="contoh@email.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                            <input type="text" wire:model="phone"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="0812-xxxx-xxxx">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Position --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                            <select wire:model="position"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500 bg-white">
                                <option value="kasir">Kasir</option>
                                <option value="chef">Chef / Dapur</option>
                                <option value="admin">Administrator</option>
                            </select>
                            @error('position')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Avatar --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
                            <div class="flex items-center gap-4">
                                @if($avatar)
                                    <img src="{{ $avatar->temporaryUrl() }}" alt="Preview"
                                        class="w-16 h-16 rounded-full object-cover">
                                @elseif($existing_avatar)
                                    <img src="{{ asset('storage/' . $existing_avatar) }}" alt="Current"
                                        class="w-16 h-16 rounded-full object-cover">
                                @else
                                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                                <input type="file" wire:model="avatar" accept="image/*"
                                    class="flex-1 text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                            </div>
                            @error('avatar')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password (only for new) --}}
                        @if(!$editingId)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                    <input type="password" wire:model="password"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                        placeholder="Min. 6 karakter">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                                    <input type="password" wire:model="password_confirmation"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                        placeholder="Ulangi password">
                                </div>
                            </div>
                        @endif

                        {{-- Checkboxes --}}
                        <div class="flex items-center gap-6 pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_admin"
                                    class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <span class="text-sm text-gray-700">Hak Akses Admin</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="is_active"
                                    class="w-4 h-4 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                                <span class="text-sm text-gray-700">Aktif</span>
                            </label>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button" wire:click="closeModal"
                                class="px-4 py-2.5 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2.5 bg-teal-600 text-white rounded-xl hover:bg-teal-700 transition-colors font-medium flex items-center gap-2">
                                <span wire:loading.remove wire:target="save">
                                    {{ $editingId ? 'Simpan Perubahan' : 'Tambah Karyawan' }}
                                </span>
                                <span wire:loading wire:target="save" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900/50 transition-opacity" wire:click="cancelDelete"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 animate-bounce-in">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Hapus Karyawan?</h3>
                            <p class="text-sm text-gray-500 mt-1">Data karyawan yang dihapus tidak dapat dikembalikan.</p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button wire:click="cancelDelete"
                            class="px-4 py-2.5 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                            Batal
                        </button>
                        <button wire:click="delete"
                            class="px-4 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-medium">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Reset Password Modal --}}
    @if($showResetPasswordModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900/50 transition-opacity" wire:click="cancelResetPassword"></div>

                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 animate-bounce-in">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Reset Password?</h3>
                            <p class="text-sm text-gray-500 mt-1">Password akan direset ke: <strong
                                    class="text-gray-700">password123</strong></p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button wire:click="cancelResetPassword"
                            class="px-4 py-2.5 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                            Batal
                        </button>
                        <button wire:click="resetPassword"
                            class="px-4 py-2.5 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition-colors font-medium">
                            Reset Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>     document.addEventListener('livewire:initialized', () => {         Livewire.on('employee-saved', (event) => {             Swal.fire({                 icon: 'success',                 title: 'Berhasil!',                 text: `Karyawan "${event[0].name}" berhasil ${event[0].type === 'created' ? 'ditambahkan' : 'diperbarui'}`,                 timer: 2000,                 showConfirmButton: false             });         });
             Livewire.on('employee-deleted', () => {             Swal.fire({                 icon: 'success',                 title: 'Dihapus!',                 text: 'Data karyawan berhasil dihapus',                 timer: 2000,                 showConfirmButton: false             });         });
             Livewire.on('employee-error', (event) => {             Swal.fire({                 icon: 'error',                 title: 'Gagal!',                 text: event[0].message,                 timer: 3000,                 showConfirmButton: false             });         });
             Livewire.on('password-reset', (event) => {             Swal.fire({                 icon: 'success',                 title: 'Password Direset!',                 text: `Password "${event[0].name}" berhasil direset ke "password123"`,                 timer: 3000,                 showConfirmButton: false             });         });
             Livewire.on('status-toggled', (event) => {             Swal.fire({                 icon: 'success',                 title: 'Status Diubah!',                 text: `Status "${event[0].name}" berhasil diubah menjadi ${event[0].status}`,                 timer: 2000,                 showConfirmButton: false             });         });     });
    </script>
@endpush