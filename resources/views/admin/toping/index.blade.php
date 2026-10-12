@extends('layouts.admin')
@section('title', 'Kelola Toping')
@section('content')
<div class="container-fluid p-4" x-data="{}"> 
    {{-- 1. HEADER & SEARCH --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Toping</h1>
            <p class="text-xs text-gray-400 mt-1">Atur daftar Toping Anda</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <form action="{{ route('admin.toping.index') }}" method="GET" class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari topping..." 
                    class="bg-white border border-gray-200 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 rounded-xl pl-9 pr-8 py-2 text-xs outline-none transition shadow-sm w-56 sm:w-64">
                @if(request('search'))
                    <a href="{{ route('admin.toping.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs" title="Reset">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
            <a href="{{ route('admin.toping.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-green-700 transition-all active:scale-95">
                <i class="fa-solid fa-plus mr-2 text-xs"></i>
                Add Topping
            </a>
        </div>
    </div>

    {{-- 2. STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-card title="Total Toping" value="{{ $totalToping }}" type="total" />
        <x-card title="Aktif" value="{{ $aktif }}" type="aktif" />
        <x-card title="Non Aktif" value="{{ $nonAktif }}" type="non-aktif" />
    </div>

    {{-- 3. TABLE MENU --}}
    <x-table>
        <x-slot name="header">
            <tr>
                <th class="px-6 py-4 text-left">No</th>
                <th class="px-6 py-4 text-left">Nama</th>
                <th class="px-6 py-4 text-left">Harga</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-center">Promo Gratis</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </x-slot>

        @forelse($topings as $index => $toping)
        <tr class="hover:bg-gray-50 transition border-b border-gray-100">
            <td class="px-6 py-4 text-gray-500">{{ $topings->firstItem() ? ($topings->firstItem() + $index) : ($index + 1) }}</td>
            <td class="px-6 py-4">
                <div class="font-bold text-gray-800">{{ $toping->nama_toping }}</div>
            </td>
            <td class="px-6 py-4 text-gray-600 font-semibold">
                Rp {{ number_format($toping->harga_toping, 0, ',', '.') }}
            </td>
            <td class="px-6 py-4 text-center">
                <x-status-badge :active="$toping->is_active" />
            </td>
            <td class="px-6 py-4 text-center">
                @if($toping->bisa_gratis ?? true)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-green-50 text-green-700 text-[10px] font-bold rounded-md border border-green-100">
                        <i class="fa-solid fa-circle-check text-green-600 text-[10px]"></i> Bisa Gratis
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-orange-50 text-orange-700 text-[10px] font-bold rounded-md border border-orange-100">
                        <i class="fa-solid fa-circle-xmark text-orange-600 text-[10px]"></i> Selalu Bayar
                    </span>
                @endif
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                    {{-- Edit --}}
                    <a href="{{ route('admin.toping.edit', $toping->id_toping) }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-600 text-xs font-bold rounded-lg hover:bg-amber-100 transition border border-amber-100">
                        <i class="fa-solid fa-pen-to-square mr-1.5"></i> Edit
                    </a>
                    {{-- Hapus --}}
                    <button
                        type="button"
                        @click="$dispatch('open-modal-hapus-{{ $toping->id_toping }}')"
                        class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 text-xs font-bold rounded-lg hover:bg-red-100 transition border border-red-100">
                        <i class="fa-solid fa-trash-can mr-1.5"></i> Hapus
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-12 text-gray-400 font-medium">
                <i class="fa-solid fa-bowl-food text-3xl mb-2 text-gray-300 block"></i>
                Belum ada data topping tersedia.
            </td>
        </tr>
        @endforelse

        @if($topings->hasPages())
            <x-slot name="pagination">
                {{ $topings->links() }}
            </x-slot>
        @endif
    </x-table>
</div>

{{-- 4. MODAL HAPUS --}}
@foreach($topings as $toping)
    <x-alert 
        id="{{ $toping->id_toping }}" 
        name="{{ $toping->nama_toping }}" 
        action="{{ route('admin.toping.destroy', $toping->id_toping) }}" 
    />
@endforeach

@push('scripts')

@endpush
@endsection