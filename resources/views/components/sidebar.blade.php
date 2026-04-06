{{-- @extends('layouts.app') --}}
<aside class="fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-100 flex flex-col shadow-sm">
    {{-- Header Logo --}}
    <div class="p-6 flex items-center gap-3 border-b border-gray-50">
        <img src="{{ asset('img/shanghaejo.jpg') }}" class="w-10 h-10 rounded-full shadow-sm" alt="Logo">
        <span class="text-xl font-black italic text-teal-800 tracking-tight">ShangHaejo</span>
    </div>

    {{-- Menu List --}}
    <div class="flex-1 overflow-y-auto py-4 px-3">
        <ul class="space-y-1">
            
            {{-- Dashboard --}}
            <li>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 rounded-xl transition-all {{ request()->is('admin.dashboard.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-house w-5 text-center"></i>
                    <span class="ms-3 font-semibold text-sm">Dashboard</span>
                </a>
            </li>

            {{-- Kelola Cabang --}}
            <li>
                <a href="{{ route('admin.cabang.index') }}" class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('admin.cabang.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-store w-5 text-center"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola Cabang</span>
                </a>
            </li>

            {{-- Kelola User --}}
            <li>
                <a href="{{ route('admin.user.index') }}" class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('admin.user.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-users w-5 text-center"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola User</span>
                </a>
            </li>

            {{-- Kelola Menu --}}
            <li>
                <a href="{{ route('admin.menu.index') }}" class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('admin.menu.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-utensils w-5 text-center"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola Menu</span>
                </a>
            </li>

            {{-- Kelola Topping (Sesuai dengan route yang kamu punya) --}}
            <li>
                <a href="{{ route('admin.toping.index') }}" class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('admin.toping.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }} group">
                    <i class="fa-solid fa-bowl-food w-5 text-center"></i>
                    <span class="ms-3 font-semibold text-sm">Kelola Topping</span>
                </a>
            </li>

            <hr class="my-4 border-gray-100 mx-2">

            {{-- Dropdown Laporan --}}
            <li x-data="{ open: {{ request()->is('laporan*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center w-full p-3 text-gray-600 rounded-xl hover:bg-teal-50 hover:text-teal-700 transition-all group">
                    <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
                    <span class="flex-1 ms-3 text-left font-semibold text-sm">Laporan Penjualan</span>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <ul x-show="open" x-cloak class="py-2 space-y-1 px-8">
                    <li><a href="#" class="block p-2 text-xs font-medium text-gray-500 hover:text-teal-700">> Harian</a></li>
                    <li><a href="#" class="block p-2 text-xs font-medium text-gray-500 hover:text-teal-700">> Bulanan</a></li>
                </ul>
            </li>
        </ul>
    </div>
</aside>