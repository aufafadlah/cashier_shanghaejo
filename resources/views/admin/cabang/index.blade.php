@extends('layouts.admin')
@section('title', 'Kelola Cabang')

@section('content')
<div class="container-fluid p-4" x-data="{}"> 
    {{-- 1. HEADER & SEARCH --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Cabang</h1>
            <p class="text-xs text-gray-400 mt-1">Kelola Cabang Bisnis Anda</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <form action="{{ route('admin.cabang.index') }}" method="GET" class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari cabang / alamat..." 
                    class="bg-white border border-gray-200 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 rounded-xl pl-9 pr-8 py-2 text-xs outline-none transition shadow-sm w-56 sm:w-64">
                @if(request('search'))
                    <a href="{{ route('admin.cabang.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs" title="Reset">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
            <a href="{{ route('admin.cabang.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-green-700 transition-all active:scale-95">
                <i class="fa-solid fa-plus mr-2 text-xs"></i>
                Add Cabang
            </a>
        </div>
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

        @forelse($cabangs as $index => $cabang)
        <tr class="hover:bg-gray-50 transition border-b border-gray-100">
            <td class="px-6 py-4 text-gray-500">{{ $cabangs->firstItem() ? ($cabangs->firstItem() + $index) : ($index + 1) }}</td>
            <td class="px-6 py-4">
                <div class="font-bold text-gray-800">{{ $cabang->nama_cabang }}</div>
                <div class="text-[10px] text-gray-400 uppercase">{{ $cabang->alamat }}</div>
            </td>
            <td class="px-6 py-4">
                <x-status-badge :active="$cabang->is_active" />
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                    {{-- Detail --}}
                    <a href="{{ route('admin.cabang.show', $cabang->id_cabang) }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-600 text-xs font-bold rounded-lg hover:bg-blue-100 transition border border-blue-100">
                       <i class="fa-solid fa-eye mr-1.5"></i> Detail
                    </a>
                    
                    {{-- Edit --}}
                    <a href="{{ route('admin.cabang.edit', $cabang->id_cabang) }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-600 text-xs font-bold rounded-lg hover:bg-amber-100 transition border border-amber-100">
                       <i class="fa-solid fa-pen-to-square mr-1.5"></i> Edit
                    </a>
                    
                    {{-- Hapus --}}
                    <button type="button"
                        @click="$dispatch('open-modal-hapus-{{ $cabang->id_cabang }}')"
                        class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 text-xs font-bold rounded-lg hover:bg-red-100 transition border border-red-100">
                        <i class="fa-solid fa-trash-can mr-1.5"></i> Hapus
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center py-12 text-gray-400">
                <i class="fa-solid fa-store text-3xl mb-2 text-gray-300 block"></i>
                Data tidak ditemukan.
            </td>
        </tr>
        @endforelse

        @if($cabangs->hasPages())
            <x-slot name="pagination">
                {{ $cabangs->links() }}
            </x-slot>
        @endif
    </x-table>
</div> {{-- Tutup container-fluid --}}



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

    // 2. LOGIKA DROPDOWN TITIK 3
    function dropdownMenu(){
        return {
            open: false,
            id: null,
            top: 0,
            left: 0,

openMenu(data){
    console.log("Membuka menu untuk ID:", data.id); // <--- TAMBAHKAN INI
    this.id = data.id;
    this.open = false; 
    
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