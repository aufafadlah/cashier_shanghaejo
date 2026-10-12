@extends('layouts.admin')
@section('title', 'Kelola Pembelian')
@section('content')
<div class="container-fluid p-4" x-data="{}"> 
    {{-- 1. HEADER & SEARCH --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Pengeluaran (Pembelian Bahan)</h1>
            <p class="text-xs text-gray-400 mt-1">Atur daftar pengeluaran Bahan yang di Beli Anda</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <form action="{{ route('admin.pembelian.index') }}" method="GET" class="relative">
                @if(request('id_cabang'))
                    <input type="hidden" name="id_cabang" value="{{ request('id_cabang') }}">
                @endif
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang / supplier..." 
                    class="bg-white border border-gray-200 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 rounded-xl pl-9 pr-8 py-2 text-xs outline-none transition shadow-sm w-56 sm:w-64">
                @if(request('search'))
                    <a href="{{ route('admin.pembelian.index', request()->except('search')) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs" title="Reset">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
            <a href="{{ route('admin.pembelian.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-green-700 transition-all active:scale-95">
                <i class="fa-solid fa-plus mr-2 text-xs"></i>
                Add Pengeluaran
            </a>
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <x-table>
        <x-slot name="header">
            <tr>
                    <th class="px-6 py-4 text-left">No</th>
                    <th class="px-6 py-4 text-left">Tgl</th>
                    <th class="px-6 py-4 text-left">Cabang</th>
                    <th class="px-6 py-4 text-left">Bahan / Barang</th>
                    <th class="px-6 py-4 text-left">Supplier</th>
                    <th class="px-6 py-4 text-left">Harga Satuan</th>
                    <th class="px-6 py-4 text-left">Total</th>
                    <th class="px-6 py-4 text-left">Ket</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
        </x-slot>

                @forelse($pengeluaran as $key => $item)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-5 text-center text-gray-600">{{ $pengeluaran->firstItem() ? ($pengeluaran->firstItem() + $key) : ($key + 1) }}.</td>
                    {{-- Format Tanggal --}}
                    <td class="px-6 py-5 text-gray-700">{{ \Carbon\Carbon::parse($item->tgl)->format('Y/m/d') }}</td>
                    
                    {{-- Nama Cabang dari Relasi --}}
                    <td class="px-6 py-5 text-gray-700 font-medium">
                        {{ $item->cabang->nama_cabang ?? 'N/A' }}
                    </td>

                    <td class="px-6 py-5 font-medium text-gray-800">{{ $item->nama_barang }}</td>
                    <td class="px-6 py-5 text-gray-600">{{ $item->supplier }}</td>
                    
                    {{-- Harga Satuan Format Rupiah --}}
                    <td class="px-6 py-5 text-gray-700">Rp. {{ number_format($item->harga_satuan) }}</td>
                    
                    {{-- Qty dan Harga Total --}}
                    <td class="px-6 py-5 font-bold text-gray-800">
                        {{ $item->qty }} Unit / Rp. {{ number_format($item->harga_total) }}
                    </td>

                    <td class="px-6 py-5 text-gray-400 italic text-sm">{{ $item->keterangan ?? 'Tidak Ada' }}</td>
                    
                    <td class="px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-2">
                    {{-- Edit --}}
                    <a href="{{ route('admin.pembelian.edit', $item->id_pengeluaran) }}" 
                       class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-600 text-xs font-bold rounded-lg hover:bg-amber-100 transition border border-amber-100">
                       <i class="fa-solid fa-pen-to-square mr-1.5"></i> Edit
                    </a>
                    
                    {{-- Hapus (Panggil Modal) --}}
                    <button type="button"
                        @click="$dispatch('open-modal-hapus-{{ $item->id_pengeluaran }}')"
                        class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 text-xs font-bold rounded-lg hover:bg-red-100 transition border border-red-100">
                        <i class="fa-solid fa-trash-can mr-1.5"></i> Hapus
                    </button>
                </div>
            </td>
        </tr>
        @empty
                <tr>
                    <td colspan="9" class="py-20 text-center text-gray-400">
                        <i class="fa-solid fa-receipt text-3xl mb-2 text-gray-300 block"></i>
                        Belum ada data pengeluaran.
                    </td>
                </tr>
                @endforelse

                @if($pengeluaran->hasPages())
                    <x-slot name="pagination">
                        {{ $pengeluaran->links() }}
                    </x-slot>
                @endif
        </x-table>
</div>

{{-- 4. MODAL HAPUS (Looping di luar tabel) --}}
@foreach($pengeluaran as $item)
    <x-alert 
        id="{{ $item->id_pengeluaran }}" 
        name="Pengeluaran {{ $item->nama_barang }}" 
        action="{{ route('admin.pembelian.destroy', $item->id_pengeluaran) }}" 
    />
@endforeach

@push('scripts')

@endpush
@endsection