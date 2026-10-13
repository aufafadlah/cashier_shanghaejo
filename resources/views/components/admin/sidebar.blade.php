<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-slate-900/30 backdrop-blur-[1px] md:hidden" x-cloak></div>
<aside class="fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-100 flex flex-col shadow-sm transition-transform duration-300 transform -translate-x-full md:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
    {{-- Header Logo --}}
    <div class="p-6 flex items-center gap-3 border-b border-gray-50">
        <img src="{{ asset('img/shanghaejo.jpg') }}" class="w-10 h-10 rounded-full shadow-sm shrink-0" alt="Logo">
        <span class="text-xl font-black italic text-teal-800 tracking-tight">ShangHaejo</span>
    </div>

    {{-- Menu List --}}
    <div class="flex-1 overflow-y-auto py-4 px-2">
        <ul class="space-y-1">
            
            {{-- Dashboard --}}
            <li>
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all active:scale-[0.97] active:opacity-70 {{ request()->routeIs('admin.dashboard') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-house w-5 text-center text-base"></i>
                    <span class="ms-3 font-semibold text-sm">Dashboard</span>
                </a>
            </li>

            {{-- Kelola Cabang --}}
            <li>
                <a href="{{ route('admin.cabang.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all active:scale-[0.97] active:opacity-70 {{ request()->routeIs('admin.cabang.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-store w-5 text-center text-base"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola Cabang</span>
                </a>
            </li>

            {{-- Kelola User --}}
            <li>
                <a href="{{ route('admin.user.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all active:scale-[0.97] active:opacity-70 {{ request()->routeIs('admin.user.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-users w-5 text-center text-base"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola User</span>
                </a>
            </li>

            {{-- Kelola Menu --}}
            <li>
                <a href="{{ route('admin.menu.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all active:scale-[0.97] active:opacity-70 {{ request()->routeIs('admin.menu.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-utensils w-5 text-center text-base"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola Menu</span>
                </a>
            </li>

            {{-- Kelola Topping --}}
            <li>
                <a href="{{ route('admin.toping.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all active:scale-[0.97] active:opacity-70 {{ request()->routeIs('admin.toping.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-bowl-food w-5 text-center text-base"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola Topping</span>
                </a>
            </li>

            {{-- Kelola Pengeluaran --}}
            <li>
                <a href="{{ route('admin.pembelian.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all active:scale-[0.97] active:opacity-70 {{ request()->routeIs('admin.pembelian.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-cart-shopping w-5 text-center text-base"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola Pengeluaran</span>
                </a>
            </li>
            
            <hr class="my-3 border-gray-100 mx-4">

            {{-- Laporan Penjualan (gabung 1 page) --}}
            <li>
                <a href="{{ route('admin.laporan.index') }}"
                   class="flex items-center p-3 mx-2 rounded-xl transition-all active:scale-[0.97] active:opacity-70 {{ request()->routeIs('admin.laporan.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-base"></i>
                    <span class="ms-3 font-semibold text-sm">Laporan Penjualan</span>
                </a>
            </li>

            {{-- Laporan Pengeluaran --}}
            <li>
                <a href="{{ route('admin.laporanpembelian.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all active:scale-[0.97] active:opacity-70 {{ request()->routeIs('admin.laporanpembelian.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-file-export w-5 text-center text-base"></i>
                    <span class="ms-3 font-semibold text-sm">Laporan Pengeluaran</span>
                </a>
            </li>

            {{-- Riwayat Penghapusan (Audit Log Kasir) --}}
            <li>
                <a href="{{ route('admin.deletion-log.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all active:scale-[0.97] active:opacity-70 {{ request()->routeIs('admin.deletion-log.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-trash-can w-5 text-center text-base"></i>
                    <span class="ms-3 font-semibold text-sm">Riwayat Penghapusan</span>
                </a>
            </li>

            <hr class="my-3 border-gray-100 mx-4">

            {{-- Pengaturan Struk --}}
            <li>
                <a href="{{ route('admin.setting.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all active:scale-[0.97] active:opacity-70 {{ request()->routeIs('admin.setting.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-sliders w-5 text-center text-base"></i>
                    <span class="ms-3 font-semibold text-sm">Pengaturan Struk</span>
                </a>
            </li>
        </ul>
    </div>
</aside>