<header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 sm:px-8 fixed top-0 right-0 left-0 lg:left-64 z-30 shadow-sm transition-all">
    <div class="flex items-center gap-2">
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-600 focus:outline-none p-1 mr-1 hover:text-teal-600 transition-colors">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <nav class="flex items-center text-sm font-medium text-gray-400">
            <span class="hover:text-teal-600">Kasir</span>
            <span class="mx-2 text-gray-300">/</span>
            <span class="text-gray-800 font-bold capitalize">@yield('title', 'Dashboard')</span>
        </nav>
    </div>

    <div class="flex items-center gap-4">
        {{-- Info Cabang Kasir Aktif --}}
        <div class="flex items-center gap-2 px-3 py-1.5 bg-teal-50 border border-teal-200 rounded-xl text-teal-800 shadow-xs">
            <i class="fa-solid fa-store text-teal-600 text-xs"></i>
            <div class="text-left">
                <span class="text-[9px] uppercase tracking-wider block text-teal-600 font-bold leading-none">Cabang</span>
                <span class="text-xs font-black text-teal-900 leading-tight">{{ Auth::user()->cabang->nama_cabang ?? 'Pusat' }}</span>
            </div>
        </div>

        <div class="hidden md:flex flex-col items-end border-r pr-4 border-gray-100">
            <span class="text-[10px] text-teal-600 font-bold uppercase tracking-widest">{{ now()->translatedFormat('l, d M Y') }}</span>
        </div>

        <div x-data="{ open: false }" class="relative ml-2">
            <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-gray-800 group-hover:text-teal-600 transition-colors">{{ Auth::user()->name ?? 'Kasir' }}</p>
                    <p class="text-[10px] text-teal-600 font-bold uppercase">{{ Auth::user()->cabang->nama_cabang ?? 'Kasir' }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-700 font-bold shadow-sm">
                    {{ substr(Auth::user()->name ?? 'K', 0, 1) }}
                </div>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-3 w-52 bg-white border border-gray-100 rounded-2xl shadow-xl py-2 z-50">
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase">Cabang Bertugas</p>
                    <p class="text-xs font-bold text-teal-700">{{ Auth::user()->cabang->nama_cabang ?? 'Pusat' }}</p>
                </div>
                <a href="{{ route('kasir.profile.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-teal-50">Profil Saya</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 font-bold">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>