@extends('layouts.admin')
@section('title', 'Kelola Menu')
@section('content')
<div class="container-fluid p-4" x-data="{}"> 
    {{-- 1. HEADER & SEARCH --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Menu</h1>
            <p class="text-xs text-gray-400 mt-1">Atur daftar menu makanan & minuman Anda</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <form action="{{ route('admin.menu.index') }}" method="GET" class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari menu..." 
                    class="bg-white border border-gray-200 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 rounded-xl pl-9 pr-8 py-2 text-xs outline-none transition shadow-sm w-56 sm:w-64">
                @if(request('search'))
                    <a href="{{ route('admin.menu.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs" title="Reset">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
            <a href="{{ route('admin.menu.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-green-700 transition-all active:scale-95">
                <i class="fa-solid fa-plus mr-2 text-xs"></i>
                Add Menu
            </a>
        </div>
    </div>

    {{-- 2. STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-card title="Total Induk Menu" value="{{ $totalMenu }}" type="total" />
        <x-card title="Status Aktif" value="{{ $aktif }}" type="aktif" />
    </div>

    {{-- 3. TABLE MENU --}}
    <x-table>
        <x-slot name="header">
            <tr>
                <th class="px-6 py-4 text-left w-16">No</th>
                <th class="px-6 py-4 text-left w-1/4">Nama & Kategori</th>
                <th class="px-6 py-4 text-left">Distribusi Cabang & Harga</th>
                <th class="px-6 py-4 text-center w-48">Aksi</th>
            </tr>
        </x-slot>

        @forelse($menus as $index => $menu)
        <tr class="hover:bg-gray-50/80 transition border-b border-gray-100">
            <td class="px-6 py-4 text-gray-500 text-sm font-medium">{{ $menus->firstItem() ? ($menus->firstItem() + $index) : ($index + 1) }}</td>
      
            <td class="px-6 py-4">
                <div class="font-bold text-gray-800 text-sm">{{ $menu->nama_menu }}</div>
                <span class="inline-block mt-1 px-2 py-0.5 bg-gray-50 text-gray-400 text-[10px] font-bold rounded-md border border-gray-100 uppercase">
                    Menu POS
                </span>
            </td>
            
            {{-- Distribusi Cabang & Harga --}}
            <td class="px-6 py-4">
                <div class="flex flex-wrap gap-2">
                    @php
                        $assignedBranchIds = $menu->cabangs->where('pivot.is_active', true)->pluck('id_cabang')->toArray();
                        $hasUnassigned = $branches->whereNotIn('id_cabang', $assignedBranchIds)->count() > 0;
                    @endphp
                    @foreach($branches as $cabang)
                        @php
                            $pivotRow = $menu->cabangs->where('id_cabang', $cabang->id_cabang)->first();
                            $isActive = $pivotRow && $pivotRow->pivot->is_active;
                            $harga = $pivotRow ? $pivotRow->pivot->harga_menu : null;
                        @endphp
                        <div class="inline-flex items-center gap-1.5 rounded-lg p-1.5 pr-2.5 border shadow-sm
                            {{ $isActive ? 'bg-white border-gray-200' : 'bg-red-50 border-red-200' }}">
                            <span class="w-2 h-2 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-red-400' }}"></span>
                            <span class="text-xs font-semibold {{ $isActive ? 'text-gray-700' : 'text-red-600' }}">
                                {{ $cabang->nama_cabang }}:
                            </span>
                            @if($isActive)
                                <span class="text-xs font-bold text-gray-900">Rp{{ number_format($harga, 0, ',', '.') }}</span>
                            @else
                                <span class="text-[10px] font-bold text-red-400 uppercase">Belum Aktif</span>
                            @endif
                        </div>
                    @endforeach
                    @if($branches->isEmpty())
                        <span class="text-xs italic text-gray-400">Belum ada cabang terdaftar.</span>
                    @endif
                </div>
            </td>
            
            {{-- Aksi --}}
            <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                    {{-- Edit --}}
                    <a href="{{ route('admin.menu.edit', $menu->id_menu) }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-600 text-xs font-bold rounded-lg hover:bg-amber-100 transition border border-amber-100">
                        <i class="fa-solid fa-pen-to-square mr-1.5"></i> Edit
                    </a>
                    
                    {{-- Hapus --}}
                    <button
                        type="button"
                        @click="$dispatch('open-modal-hapus-{{ $menu->id_menu }}')"
                        class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 text-xs font-bold rounded-lg hover:bg-red-100 transition border border-red-100">
                        <i class="fa-solid fa-trash-can mr-1.5"></i> Hapus
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center py-12 text-gray-400 font-medium text-sm">
                <i class="fa-solid fa-utensils text-3xl mb-2 text-gray-300 block"></i>
                Belum ada data menu tersedia.
            </td>
        </tr>
        @endforelse

        @if($menus->hasPages())
            <x-slot name="pagination">
                {{ $menus->links() }}
            </x-slot>
        @endif
    </x-table>
</div>

{{-- 4. MODAL HAPUS --}}
@foreach($menus as $menu)
    <x-alert 
        id="{{ $menu->id_menu }}" 
        name="{{ $menu->nama_menu }}" 
        action="{{ route('admin.menu.destroy', $menu->id_menu) }}" 
    />
@endforeach

@push('scripts')

@endpush
@endsection