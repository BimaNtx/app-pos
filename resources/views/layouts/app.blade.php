<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $restaurantName }} - @yield('title', 'POS')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }

            #print-receipt,
            #print-receipt * {
                visibility: visible;
            }

            #print-receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }

        /* Animation */
        @keyframes bounce-in {
            0% {
                transform: scale(0.9);
                opacity: 0;
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .animate-bounce-in {
            animation: bounce-in 0.3s ease-out;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100 h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
    <div class="h-screen flex overflow-hidden">
        {{-- Mobile Header --}}
        <div
            class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-teal-700 text-white px-4 py-3 flex items-center justify-between">
            <button x-on:click="sidebarOpen = !sidebarOpen" class="p-2 hover:bg-teal-600 rounded-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <h1 class="text-lg font-semibold">{{ $restaurantName }}</h1>
            <div class="w-10"></div>
        </div>

        {{-- Mobile Overlay --}}
        <div x-show="sidebarOpen" x-on:click="sidebarOpen = false" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="lg:hidden fixed inset-0 bg-black/50 z-40" x-cloak></div>

        {{-- Left Sidebar --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-teal-700 to-teal-800 lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col h-full flex-shrink-0">
            {{-- Logo --}}
            <div class="px-4 py-3 flex items-center gap-3 border-b border-teal-600/50 flex-shrink-0">
                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-teal-700 font-bold text-lg">K</span>
                </div>
                <div>
                    <h1 class="text-white font-bold">{{ $restaurantName }}</h1>
                    <p class="text-teal-300 text-xs">Restaurant POS</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 p-2 space-y-1 overflow-y-auto">
                {{-- SEGMEN OPERASIONAL --}}
                <div>
                    <p class="px-3 py-1 text-xs font-semibold text-teal-300 uppercase tracking-wider">Operasional</p>
                    <div class="space-y-0.5">
                        {{-- POS / Kasir --}}
                        <a href="{{ route('pos') }}"
                            class="flex items-center gap-3 px-3 py-1.5 rounded-lg transition-all {{ request()->routeIs('pos') ? 'bg-white/20 text-white' : 'text-teal-100 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">Kasir / POS</span>
                        </a>

                        {{-- Dashboard --}}
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center gap-3 px-3 py-1.5 rounded-lg transition-all {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'text-teal-100 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span class="font-medium">Dashboard</span>
                        </a>
                    </div>
                </div>

                {{-- SEGMEN MANAJEMEN --}}
                <div>
                    <p class="px-3 py-1 text-xs font-semibold text-teal-300 uppercase tracking-wider">Manajemen</p>
                    <div class="space-y-0.5">
                        {{-- Menu --}}
                        <a href="{{ route('products') }}"
                            class="flex items-center gap-3 px-3 py-1.5 rounded-lg transition-all {{ request()->routeIs('products') ? 'bg-white/20 text-white' : 'text-teal-100 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span class="font-medium">Menu</span>
                        </a>

                        {{-- Kategori --}}
                        <a href="{{ route('categories') }}"
                            class="flex items-center gap-3 px-3 py-1.5 rounded-lg transition-all {{ request()->routeIs('categories') ? 'bg-white/20 text-white' : 'text-teal-100 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span class="font-medium">Kategori</span>
                        </a>

                        {{-- Logistics / Bahan --}}
                        <a href="{{ route('logistics') }}"
                            class="flex items-center gap-3 px-3 py-1.5 rounded-lg transition-all {{ request()->routeIs('logistics') ? 'bg-white/20 text-white' : 'text-teal-100 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="font-medium">Logistics / Bahan</span>
                        </a>
                    </div>
                </div>

                {{-- SEGMEN KEUANGAN --}}
                <div>
                    <p class="px-3 py-1 text-xs font-semibold text-teal-300 uppercase tracking-wider">Keuangan</p>
                    <div class="space-y-0.5">
                        {{-- Riwayat Transaksi --}}
                        <a href="{{ route('transactions') }}"
                            class="flex items-center gap-3 px-3 py-1.5 rounded-lg transition-all {{ request()->routeIs('transactions') ? 'bg-white/20 text-white' : 'text-teal-100 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium">Riwayat Transaksi</span>
                        </a>

                        {{-- Laporan --}}
                        <a href="{{ route('reports') }}"
                            class="flex items-center gap-3 px-3 py-1.5 rounded-lg transition-all {{ request()->routeIs('reports') ? 'bg-white/20 text-white' : 'text-teal-100 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="font-medium">Laporan</span>
                        </a>

                        {{-- Pengeluaran --}}
                        <a href="{{ route('expenses') }}"
                            class="flex items-center gap-3 px-3 py-1.5 rounded-lg transition-all {{ request()->routeIs('expenses') ? 'bg-white/20 text-white' : 'text-teal-100 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="font-medium">Pengeluaran</span>
                        </a>
                    </div>
                </div>

                {{-- SEGMEN PENGATURAN --}}
                <div>
                    <p class="px-3 py-1 text-xs font-semibold text-teal-300 uppercase tracking-wider">Pengaturan</p>
                    <div class="space-y-0.5">
                        {{-- Karyawan --}}
                        @if(Auth::user()->is_admin ?? false)
                            <a href="{{ route('employees') }}"
                                class="flex items-center gap-3 px-3 py-1.5 rounded-lg transition-all {{ request()->routeIs('employees') ? 'bg-white/20 text-white' : 'text-teal-100 hover:bg-white/10 hover:text-white' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span class="font-medium">Karyawan</span>
                            </a>
                        @endif

                        {{-- Settings --}}
                        <a href="{{ route('settings') }}"
                            class="flex items-center gap-3 px-3 py-1.5 rounded-lg transition-all {{ request()->routeIs('settings') ? 'bg-white/20 text-white' : 'text-teal-100 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="font-medium">Pengaturan</span>
                        </a>
                    </div>
                </div>
            </nav>

            {{-- User Section --}}
            <div class="p-2.5 border-t border-teal-600/50 flex-shrink-0">
                <div class="flex items-center gap-3 px-2 py-1.5 text-teal-100 rounded-xl">
                    <div
                        class="w-9 h-9 bg-teal-500 rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0">
                        {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-medium text-sm truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="text-teal-300 text-xs truncate">{{ Auth::user()->email ?? 'admin@kasir.app' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 rounded-lg hover:bg-white/10 transition-colors" title="Keluar">
                            <svg class="w-5 h-5 text-teal-300 hover:text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col h-full overflow-hidden pt-14 lg:pt-0">
            {{ $slot }}
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Livewire Scripts -->
    @livewireScripts

    @stack('scripts')
</body>

</html>