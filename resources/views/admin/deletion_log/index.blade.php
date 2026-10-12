@extends('layouts.admin')
@section('title', 'Riwayat Penghapusan')

@section('content')
<div class="container-fluid p-4" x-data="{}">

    {{-- 1. HEADER & SEARCH --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Riwayat Penghapusan</h1>
            <p class="text-xs text-gray-400 mt-1">Audit log pesanan yang dibatalkan / dihapus oleh Kasir</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <form action="{{ route('admin.deletion-log.index') }}" method="GET" class="relative">
                @if(request('id_cabang'))
                    <input type="hidden" name="id_cabang" value="{{ request('id_cabang') }}">
                @endif
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari struk / pelanggan / kasir..." 
                    class="bg-white border border-gray-200 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 rounded-xl pl-9 pr-8 py-2 text-xs outline-none transition shadow-sm w-56 sm:w-64">
                @if(request('search'))
                    <a href="{{ route('admin.deletion-log.index', request()->except('search')) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs" title="Reset">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
            <a href="{{ route('admin.deletion-log.export.excel', request()->all()) }}" 
               class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-emerald-700 transition-all active:scale-95">
                <i class="fa-solid fa-file-excel mr-2 text-xs"></i>
                Export Excel
            </a>
        </div>
    </div>

    {{-- 2. STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <x-card title="Total Transaksi Dibatalkan" value="{{ number_format($totalHapus) }} Pesanan" type="total" />
        <x-card title="Total Nominal Pembatalan" value="Rp {{ number_format($totalNominal, 0, ',', '.') }}" type="non-aktif" />
    </div>

    {{-- 3. TABEL AUDIT --}}
    <x-table>
        <x-slot name="header">
            <tr>
                <th class="px-6 py-4 text-left w-16">No</th>
                <th class="px-6 py-4 text-left">Waktu Dibatalkan</th>
                <th class="px-6 py-4 text-left">No. Struk</th>
                <th class="px-6 py-4 text-left">Pelanggan</th>
                <th class="px-6 py-4 text-left">Total Nilai</th>
                <th class="px-6 py-4 text-left">Kasir</th>
                <th class="px-6 py-4 text-left">Cabang</th>
                <th class="px-6 py-4 text-left">Alasan Pembatalan</th>
            </tr>
        </x-slot>

        @forelse($logs as $index => $log)
        <tr class="hover:bg-gray-50 transition border-b border-gray-100">
            <td class="px-6 py-4 text-gray-500 text-xs">{{ $logs->firstItem() ? ($logs->firstItem() + $index) : ($index + 1) }}</td>
            <td class="px-6 py-4">
                <div class="font-bold text-gray-800 text-xs">{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y') }}</div>
                <div class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }} WIB</div>
            </td>
            <td class="px-6 py-4">
                <span class="font-mono text-xs font-bold text-teal-700">{{ $log->no_struk ?? '-' }}</span>
            </td>
            <td class="px-6 py-4">
                <span class="font-semibold text-gray-800 text-xs">{{ $log->nama_pelanggan ?? 'Umum' }}</span>
            </td>
            <td class="px-6 py-4">
                <span class="font-bold text-red-600 text-xs">Rp {{ number_format($log->total_bayar, 0, ',', '.') }}</span>
            </td>
            <td class="px-6 py-4 text-xs text-gray-600 font-medium">
                {{ $log->nama_kasir ?? '-' }}
            </td>
            <td class="px-6 py-4 text-xs text-gray-600 font-medium">
                {{ $log->nama_cabang ?? 'Pusat' }}
            </td>
            <td class="px-6 py-4 text-xs text-gray-600">
                <span class="inline-block px-2.5 py-0.5 rounded-md bg-red-50 text-red-700 text-[11px] font-medium border border-red-100">
                    {{ $log->alasan ?? 'Dibatalkan oleh Kasir' }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center py-12 text-gray-400 font-medium text-sm">
                Belum ada data riwayat penghapusan.
            </td>
        </tr>
        @endforelse

        @if($logs->hasPages())
            <x-slot name="pagination">
                {{ $logs->links() }}
            </x-slot>
        @endif
    </x-table>

</div>
@endsection
