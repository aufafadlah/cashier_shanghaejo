<aside class="fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-100 flex flex-col shadow-sm">
    {{-- Header Logo --}}
    <div class="p-6 flex items-center gap-3 border-b border-gray-50">
        <img src="{{ asset('img/shanghaejo.jpg') }}" class="w-10 h-10 rounded-full shadow-sm" alt="Logo">
        <span class="text-xl font-black italic text-teal-800 tracking-tight">ShangHaejo</span>
    </div>

    {{-- Menu List --}}
    <div class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1">
            
            {{-- Dashboard --}}
            <li>
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-house w-5 text-center"></i>
                    <span class="ms-3 font-semibold text-sm">Dashboard</span>
                </a>
            </li>

            {{-- Kelola Cabang --}}
            <li>
                <a href="{{ route('admin.cabang.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all {{ request()->routeIs('admin.cabang.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-store w-5 text-center"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola Cabang</span>
                </a>
            </li>

            {{-- Kelola User --}}
            <li>
                <a href="{{ route('admin.user.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all {{ request()->routeIs('admin.user.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-users w-5 text-center"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola User</span>
                </a>
            </li>

            {{-- Kelola Menu --}}
            <li>
                <a href="{{ route('admin.menu.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all {{ request()->routeIs('admin.menu.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-utensils w-5 text-center"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola Menu</span>
                </a>
            </li>

            {{-- Kelola Topping --}}
            <li>
                <a href="{{ route('admin.toping.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all {{ request()->routeIs('admin.toping.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-bowl-food w-5 text-center"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola Topping</span>
                </a>
            </li>


            {{-- Kelola Pengeluaran (Fixed route typo 'pemelian' to 'pembelian') --}}
            <li>
                <a href="{{ route('admin.pembelian.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all {{ request()->routeIs('admin.pembelian.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-cart-shopping w-5 text-center"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola Pengeluaran</span>
                </a>
            </li>
            
            <hr class="my-4 border-gray-100 mx-4">

            {{-- Laporan Penjualan (gabung 1 page) --}}
            <li>
                <a href="{{ route('admin.lapora.index') }}" 
                   class="flex items-center p-3 mx-2 rounded-xl transition-all {{ request()->routeIs('admin.laporan.index') || request()->routeIs('admin.laporan.data') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
                    <span class="ms-3 font-semibold text-sm">Laporan Penjualan</span>
                </a>
            </li>



            {{-- Laporan Pengeluaran (Commented out cleanly) --}}
            {{-- <li>
                <a href="{{ route('admin.laporanpembelian.index') }}" class="flex items-center p-3 mx-2 rounded-xl text-gray-600 hover:bg-teal-50 hover:text-teal-700 group">
                    <i class="fa-solid fa-file-export w-5 text-center"></i>
                    <span class="ms-3 font-semibold text-sm">Laporan Pengeluaran</span>
                </a>
            </li> --}}
        </ul>
    </div>
</aside>