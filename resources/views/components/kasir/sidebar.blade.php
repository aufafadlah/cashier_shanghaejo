{{-- Backdrop for mobile (< 768px) --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-slate-900/30 backdrop-blur-[1px] md:hidden" x-cloak></div>

{{-- Sidebar: mobile (-translate-x-full), tablet & laptop (w-64 translate-x-0) --}}
<aside class="fixed top-0 left-0 z-40 w-64 h-screen bg-white border-r border-gray-100 flex flex-col shadow-sm transition-transform duration-300 transform -translate-x-full md:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
    {{-- Header Logo --}}
    <div class="p-6 flex items-center gap-3 border-b border-gray-50">
        <img src="{{ asset('img/shanghaejo.jpg') }}" class="w-10 h-10 rounded-full shadow-sm shrink-0" alt="Logo">
        <span class="text-xl font-black italic text-teal-800 tracking-tight">ShangHaejo</span>
    </div>

    {{-- Menu List --}}
    <div class="flex-1 overflow-y-auto py-4 px-3">
        <ul class="space-y-1.5">
            @php $userRole = strtolower(auth()->user()->role ?? ''); @endphp

            @if($userRole == 'kasir')
                <li>
                    <a href="{{ route('kasir.pesan.index') }}" 
                       class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('kasir.pesan.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }}">
                        <i class="fa-solid fa-store w-5 text-center text-base"></i>
                        <span class="ms-3 font-semibold text-sm">POS</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('kasir.riwayat.index') }}" 
                       class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('kasir.riwayat.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-600 hover:bg-teal-50 hover:text-teal-700' }}">
                        <i class="fa-solid fa-clock-rotate-left w-5 text-center text-base"></i>
                        <span class="ms-3 font-semibold text-sm">RIWAYAT</span>
                    </a>
                </li>
            @endif

            @if($userRole == 'admin')
                <li>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center p-3 rounded-xl transition-all text-gray-600 hover:bg-teal-50 hover:text-teal-700">
                        <i class="fa-solid fa-chart-line w-5 text-center text-base"></i>
                        <span class="ms-3 font-semibold text-sm">Dashboard</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</aside>