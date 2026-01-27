<div>
    {{-- Modal Backdrop & Container --}}
    @if($showModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto" x-data x-init="document.body.classList.add('overflow-hidden')" 
         x-on:keydown.escape.window="$wire.closeModal()">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             wire:click="closeModal"></div>
        
        {{-- Modal Panel --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl transform transition-all overflow-hidden flex flex-col"
                 x-on:click.outside="$wire.closeModal()"
                 x-data="{ activeTab: 'profile' }"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                
                {{-- Header Background --}}
                <div class="relative bg-teal-600 pt-6 pb-6 px-6 text-center shrink-0">
                    <button wire:click="closeModal" class="absolute top-4 right-4 p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-full transition-colors z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    
                    {{-- User Avatar (Always Visible) --}}
                    <div class="relative inline-block mb-2">
                        <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-3xl font-bold text-white mx-auto shadow-lg border-2 border-white/30">
                            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-white truncate">{{ Auth::user()->name ?? 'Admin' }}</h3>
                    <p class="text-teal-100/80 text-sm truncate">{{ Auth::user()->email ?? 'admin@kasir.app' }}</p>

                    {{-- Tabs Buttons (Pill / Rectangle Shape) --}}
                    <div class="flex gap-2 p-1 bg-teal-800/30 rounded-xl mt-6">
                        <button @click="activeTab = 'profile'" 
                            :class="activeTab === 'profile' ? 'bg-white text-teal-700 shadow-sm' : 'text-teal-100 hover:bg-white/10 hover:text-white'"
                            class="flex-1 py-2 text-sm font-bold rounded-lg transition-all duration-300 ease-out focus:outline-none">
                            Profil
                        </button>
                        <button @click="activeTab = 'security'" 
                            :class="activeTab === 'security' ? 'bg-white text-orange-600 shadow-sm' : 'text-teal-100 hover:bg-white/10 hover:text-white'"
                            class="flex-1 py-2 text-sm font-bold rounded-lg transition-all duration-300 ease-out focus:outline-none">
                            Password
                        </button>
                    </div>
                </div>

                {{-- Content Area --}}
                <div class="bg-white min-h-[340px] relative overflow-hidden">
                    
                    {{-- Tab: Profile --}}
                    <div x-show="activeTab === 'profile'" 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-x-8 scale-95" 
                         x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                         x-transition:leave="transition ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-x-0 scale-100" 
                         x-transition:leave-end="opacity-0 -translate-x-8 scale-95"
                         class="absolute inset-0 p-6 overflow-y-auto custom-scrollbar">
                        
                        <div class="h-full flex flex-col">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Ganti Nama</label>
                            <form wire:submit="updateName" class="space-y-4">
                                <div>
                                    <input type="text" wire:model="name" placeholder="Nama Lengkap"
                                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all font-medium text-gray-800 placeholder-gray-400">
                                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <button type="submit" wire:loading.attr="disabled" wire:target="updateName" 
                                    class="w-full py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-lg shadow-teal-200 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                                    <span wire:loading.remove wire:target="updateName">Simpan Nama</span>
                                    <span wire:loading wire:target="updateName">Menyimpan...</span>
                                </button>
                            </form>

                             {{-- Footer within Tab for better layout balance --}}
                            <div class="mt-auto pt-6 border-t border-gray-50">
                                 <button wire:click="logout" class="w-full py-3 text-xs font-bold text-gray-400 hover:text-red-500 uppercase tracking-widest transition-colors flex items-center justify-center gap-2 group bg-gray-50 hover:bg-red-50 rounded-xl">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar Aplikasi
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Security --}}
                    <div x-show="activeTab === 'security'" style="display: none;"
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 translate-x-8 scale-95" 
                         x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                         x-transition:leave="transition ease-in duration-200" 
                         x-transition:leave-start="opacity-100 translate-x-0 scale-100" 
                         x-transition:leave-end="opacity-0 -translate-x-8 scale-95"
                         class="absolute inset-0 p-6 overflow-y-auto custom-scrollbar">
                        
                         {{-- Inline Success Alert --}}
                         @if($passwordChanged)
                            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3 animate-browse-in">
                                <div class="bg-green-100 p-1.5 rounded-full text-green-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-green-700">Berhasil!</h4>
                                    <p class="text-xs text-green-600">Password telah diperbarui.</p>
                                </div>
                            </div>
                         @endif

                         <form wire:submit="updatePassword" class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Password Lama</label>
                                <input type="password" wire:model="currentPassword"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all text-sm">
                                @error('currentPassword') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Password Baru</label>
                                <input type="password" wire:model="newPassword"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all text-sm">
                                @error('newPassword') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Konfirmasi Password</label>
                                <input type="password" wire:model="newPasswordConfirmation"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all text-sm">
                                @error('newPasswordConfirmation') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <button type="submit" wire:click="updatePassword" wire:loading.attr="disabled" wire:target="updatePassword"
                                class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-lg shadow-orange-200 transition-all transform active:scale-95 mt-2">
                                <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                                <span wire:loading wire:target="updatePassword">Memproses...</span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Toast Notification --}}
                <div class="fixed top-6 left-1/2 transform -translate-x-1/2 w-full max-w-xs space-y-2 pointer-events-none z-[110]">
                    @if($nameSaved)
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                             class="bg-teal-600 text-white text-sm py-2 px-4 rounded-lg shadow-xl text-center flex items-center justify-center gap-2 animate-bounce-in">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Nama tersimpan!</span>
                        </div>
                    @endif
                    @if($passwordChanged)
                         <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                             class="bg-orange-600 text-white text-sm py-2 px-4 rounded-lg shadow-xl text-center flex items-center justify-center gap-2 animate-bounce-in">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Password berhasil diubah!</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    @endif
</div>
