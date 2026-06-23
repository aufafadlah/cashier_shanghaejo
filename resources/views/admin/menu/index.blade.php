@extends('layouts.admin')
@section('title', 'Kelola Menu')
@section('content')
<div class="container-fluid p-4" x-data="{}"> 
    {{-- 1. HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Menu</h1>
            <p class="text-xs text-gray-400 mt-1">Atur daftar menu makanan & minuman Anda (Many to Many)</p>
        </div>
        <a href="{{ route('admin.menu.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-green-700 transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Menu
        </a>
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
            <td class="px-6 py-4 text-gray-500 text-sm font-medium">{{ $index + 1 }}</td>
      
            <td class="px-6 py-4">
                <div class="font-bold text-gray-800 text-sm">{{ $menu->nama_menu }}</div>
                <span class="inline-block mt-1 px-2 py-0.5 bg-gray-50 text-gray-400 text-[10px] font-bold rounded-md border border-gray-100 uppercase">
                    Menu POS
                </span>
            </td>
            
            {{-- Distribusi Cabang & Harga Menggunakan Badge Mewah --}}
            <td class="px-6 py-4">
                <div class="flex flex-wrap gap-2">
                    @forelse($menu->cabangs as $cabang)
                        <div class="inline-flex items-center gap-1.5 bg-white border border-gray-200 rounded-lg p-1.5 pr-2.5 shadow-sm">
                            {{-- Indikator Status Aktif Menu di Cabang --}}
                            <span class="w-2 h-2 rounded-full {{ $cabang->pivot->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                            
                            {{-- Nama Cabang --}}
                            <span class="text-xs font-semibold text-gray-700">{{ $cabang->nama_cabang }}:</span>
                            
                            {{-- Harga Spesifik Cabang --}}
                            <span class="text-xs font-bold text-gray-900">Rp{{ number_format($cabang->pivot->harga_menu, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <span class="text-xs italic text-gray-400">Belum didistribusikan ke cabang manapun.</span>
                    @endforelse
                </div>
            </td>
            
            {{-- Aksi --}}
            <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                    {{-- Edit --}}
                    <a href="{{ route('admin.menu.edit', $menu->id_menu) }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-600 text-xs font-bold rounded-lg hover:bg-amber-100 transition border border-amber-100">
                        Edit
                    </a>
                    
                    {{-- Hapus --}}
                    <button
                        type="button"
                        @click="$dispatch('open-modal-hapus-{{ $menu->id_menu }}')"
                        class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 text-xs font-bold rounded-lg hover:bg-red-100 transition border border-red-100">
                        Hapus
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center py-12 text-gray-400 font-medium text-sm">
                Belum ada data menu tersedia.
            </td>
        </tr>
        @endforelse
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
<script>
    // Pastikan DOM siap, lalu panggil dengan objek penanda window. agar terhubung ke file toast global
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('success'))
            if (typeof window.showToast === 'function') {
                window.showToast("{{ session('success') }}", 'success');
            }
        @endif
        
        @if(session('error'))
            if (typeof window.showToast === 'function') {
                window.showToast("{{ session('error') }}", 'error');
            }
        @endif
    });
</script>
@endpush
@endsection