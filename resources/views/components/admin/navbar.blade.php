<header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-8 fixed top-0 right-0 left-0 md:left-64 z-30 shadow-sm transition-all">
    <div class="flex items-center gap-4">
        <nav class="flex items-center text-sm font-medium text-gray-400">
            <span class="hover:text-teal-600">Admin</span>
            <span class="mx-2 text-gray-300">/</span>
            <span class="text-gray-800 font-bold capitalize">@yield('title', 'Dashboard')</span>
        </nav>
    </div>

    <div class="flex items-center gap-4">
        {{-- 1. Info Tanggal Live --}}
        <div class="hidden md:flex flex-col items-end border-r pr-4 border-gray-100">
            <span class="text-[10px] text-teal-600 font-bold uppercase tracking-widest">{{ now()->translatedFormat('l, d M Y') }}</span>
        </div>

        {{-- 2. FILTER CABANG (TAMBAHAN BARU DI SINI) --}}
{{-- 2. FILTER CABANG --}}

<div class="flex items-center">
    @if(auth()->user()->role === 'admin')
        {{-- Cek apakah halaman saat ini adalah halaman Cabang atau Topping --}}
    @php
    $isDisabled = request()->routeIs('admin.cabang.*') || 
                  request()->routeIs('admin.toping.*');
    @endphp

        <form action="{{ url()->current() }}" method="GET" id="filterFormNavbar">
            <select name="id_cabang" 
                    onchange="this.form.submit()" 
                    {{ $isDisabled ? 'disabled' : '' }}
                    class="bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-500 {{ $isDisabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                
                <option value="">Semua Cabang (Gabungan)</option>
                
                @foreach($daftarCabang ?? [] as $cabang)
                    <option value="{{ $cabang->id_cabang }}" {{ request('id_cabang') == $cabang->id_cabang ? 'selected' : '' }}>
                        {{ $cabang->nama_cabang }}
                    </option>
                @endforeach
            </select>
        </form>
    @else
        <div class="bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100 text-xs font-bold text-gray-500">
            Cabang: {{ auth()->user()->cabang->nama_cabang ?? 'Cabang Aktif' }}
        </div>
    @endif
</div>

        {{-- 3. Dropdown Menu Profil User --}}
        <div x-data="{ open: false }" class="relative ml-2">
            <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-gray-800 group-hover:text-teal-600 transition-colors">{{ Auth::user()->name ?? 'Farida' }}</p>
                    <p class="text-[10px] text-gray-400 uppercase">{{ Auth::user()->role ?? 'Admin' }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-700 font-bold shadow-sm">
                    {{ substr(Auth::user()->name ?? 'F', 0, 1) }}
                </div>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-3 w-48 bg-white border border-gray-100 rounded-2xl shadow-xl py-2 z-50">
                <a href="{{ route('admin.profile.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-600 hover:bg-teal-50">Profil Saya</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 font-bold">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>