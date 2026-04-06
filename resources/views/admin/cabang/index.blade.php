@extends('layouts.app')
@section('title', 'Kelola Cabang')

@section('content')
<div class="container-fluid p-4" x-data="{}"> 
    {{-- 1. HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Cabang</h1>
            <p class="text-xs text-gray-400 mt-1">Kelola Cabang Bisnis Anda</p>
        </div>
        <a href="{{ route('admin.cabang.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-green-700 transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Cabang
        </a>
    </div>

    {{-- 2. STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-card title="Total Cabang" value="{{ $totalCabang }}" type="total" />
        <x-card title="Aktif" value="{{ $aktif }}" type="aktif" />
        <x-card title="Non Aktif" value="{{ $nonAktif }}" type="non-aktif" />
    </div>

    {{-- 3. TABEL --}}
    <x-table>
        <x-slot name="header">
            <tr>
                <th class="px-6 py-4 text-left">No</th>
                <th class="px-6 py-4 text-left">Nama Cabang</th>
                <th class="px-6 py-4 text-left">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </x-slot>

        @forelse($cabangs as $cabang)
        <tr class="hover:bg-gray-50 transition border-b border-gray-100">
            <td class="px-6 py-4 text-gray-500">{{ $loop->iteration }}</td>
            <td class="px-6 py-4">
                <div class="font-bold text-gray-800">{{ $cabang->nama_cabang }}</div>
                <div class="text-[10px] text-gray-400 uppercase">{{ $cabang->alamat }}</div>
            </td>
            <td class="px-6 py-4">
                <x-status-badge :active="$cabang->is_active" />
            </td>
            <td class="px-6 py-4 text-center">
                {{-- Gunakan $event.currentTarget agar koordinat akurat --}}
                <button 
                    type="button"
                    @click="$dispatch('open-menu', { 
                        id: {{ $cabang->id_cabang }}, 
                        el: $event.currentTarget 
                    })"
                    class="p-2 w-10 h-10 rounded-lg hover:bg-gray-100 transition-colors font-bold">
                    ⋮
                </button>
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center py-12 text-gray-400">Data tidak ditemukan.</td></tr>
        @endforelse
    </x-table>
</div> {{-- Tutup container-fluid --}}

{{-- DROPDOWN MENU --}}
<div 
    x-data="dropdownMenu()"
    @open-menu.window="openMenu($event.detail)"
    @click.away="open = false"
    x-show="open"
    x-cloak
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    :style="`position: fixed; top: ${top}px; left: ${left}px;`"
    class="w-44 bg-white border border-gray-100 rounded-2xl shadow-2xl z-[9999] py-2 overflow-hidden"
>
    <a :href="'/admin/cabang/'+id" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition">
        <span class="mr-3 text-lg">👁️</span> Detail Cabang
    </a>
    <a :href="'/admin/cabang/'+id+'/edit'" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition">
        <span class="mr-3 text-lg">✏️</span> Edit Data
    </a>
    <div class="border-t border-gray-50 my-1"></div>
    <button
        @click="$dispatch('open-modal-hapus-'+id); open=false"
        class="flex items-center w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition font-semibold">
        <span class="mr-3 text-lg">🗑️</span> Hapus Cabang
    </button>
</div>

{{-- 4. MODAL DI LUAR --}}
@foreach($cabangs as $cabang)
    <x-alert
        id="{{ $cabang->id_cabang }}"
        name="{{ $cabang->nama_cabang }}"
        action="{{ route('admin.cabang.destroy', $cabang->id_cabang) }}"
    />
@endforeach

@push('scripts')
<script>
    // 1. MESIN NOTIFIKASI (TOAST)
    @if(session('success'))
        showToast("{{ session('success') }}", "success");
    @endif

    @if(session('error'))
        showToast("{{ session('error') }}", "error");
    @endif

    // 2. LOGIKA DROPDOWN TITIK 3
    function dropdownMenu(){
        return {
            open: false,
            id: null,
            top: 0,
            left: 0,

            openMenu(data){
                this.id = data.id;
                this.open = false; // Reset status agar transisi muncul lagi
                
                // Beri jeda sedikit agar koordinat tombol dihitung dengan benar
                this.$nextTick(() => {
                    const rect = data.el.getBoundingClientRect();
                    const dropdownHeight = 160; 
                    const windowHeight = window.innerHeight;

                    // Hitung posisi vertikal (biar nggak kepotong layar bawah)
                    if (rect.bottom + dropdownHeight > windowHeight) {
                        this.top = rect.top - dropdownHeight;
                    } else {
                        this.top = rect.bottom + 5;
                    }

                    // Posisi horizontal (geser ke kiri sedikit agar sejajar tombol)
                    this.left = rect.left - 140; 
                    
                    this.open = true;
                });
            }
        }
    }
</script>
@endpush
@endsection