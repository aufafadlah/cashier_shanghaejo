@extends('layouts.admin')
@section('title', 'Kelola Pembelian')
@section('content')
<div class="container-fluid p-4" x-data="{}"> 
    {{-- 1. HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Pengeluaran (Pembelian Bahan)</h1>
            <p class="text-xs text-gray-400 mt-1">Atur daftar pengeluaran Bahan yang di Beli Anda</p>
        </div>
        <a href="{{ route('admin.pembelian.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-green-700 transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Pengeluaran
        </a>
    </div>

    {{-- TABLE SECTION --}}
    <x-table>
        <x-slot name="header">
            <tr>
                    <th class="px-6 py-4 text-left">No</th>
                    <th class="px-6 py-4 text-left">Tgl</th>
                    <th class="px-6 py-4 text-left">Cabang</th> {{-- Kolom Cabang Baru --}}
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
                    <td class="px-6 py-5 text-center text-gray-600">{{ $key + 1 }}.</td>
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
                       class="inline-flex items-center px-3 py-2 bg-amber-50 text-amber-600 text-xs font-bold rounded-lg hover:bg-amber-100 transition border border-amber-100">
                       ✏️ Edit
                    </a>
                    
                    {{-- Hapus (Panggil Modal) --}}
                    <button type="button"
                        @click="$dispatch('open-modal-hapus-{{ $item->id_pengeluaran }}')"
                        class="inline-flex items-center px-3 py-2 bg-red-50 text-red-600 text-xs font-bold rounded-lg hover:bg-red-100 transition border border-red-100">
                        <span class="mr-1">🗑️</span> Hapus
                    </button>
                </div>
            </td>
        </tr>
        @empty
                <tr>
                    <td colspan="9" class="py-20 text-center text-gray-400">
                        Belum ada data pengeluaran.
                    </td>
                </tr>
                @endforelse
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
<script>
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
</script>
@endpush
@endsection