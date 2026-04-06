@extends('layouts.app')
@section('title', 'Kelola Toping')
@section('content')
<div class="container-fluid p-4" x-data="{}"> 
    {{-- 1. HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Toping</h1>
            <p class="text-xs text-gray-400 mt-1">Atur daftar Toping Anda</p>
        </div>
        <a href="{{ route('admin.toping.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-green-700 transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Topping
        </a>
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
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </x-slot>

        @forelse($topings as $index => $toping)
        <tr class="hover:bg-gray-50 transition border-b border-gray-100">
            <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
            <td class="px-6 py-4">
                <div class="font-bold text-gray-800">{{ $toping->nama_toping }}</div>
            </td>
            <td class="px-6 py-4 text-gray-600 font-semibold">
                Rp {{ number_format($toping->harga_toping, 0, ',', '.') }}
            </td>
            <td class="px-6 py-4 text-center">
                <x-status-badge :active="$toping->is_active" />
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                    {{-- Edit --}}
                    <a href="{{ route('admin.toping.edit', $toping->id_toping) }}" 
                       class="inline-flex items-center px-3 py-2 bg-amber-50 text-amber-600 text-xs font-bold rounded-lg hover:bg-amber-100 transition border border-amber-100">
                        ✏️ Edit
                    </a>
                    {{-- Hapus --}}
                    <button
                        type="button"
                        @click="$dispatch('open-modal-hapus-{{ $toping->id_toping }}')"
                        class="inline-flex items-center px-3 py-2 bg-red-50 text-red-600 text-xs font-bold rounded-lg hover:bg-red-100 transition border border-red-100">
                        <span class="mr-1">🗑️</span> Hapus
                    </button>
                        </div>
                    </td>
                        </tr>
                        @empty
        <tr>
            <td colspan="6" class="text-center py-12 text-gray-400 font-medium">
                Belum ada data menu tersedia.
            </td>
        </tr>
        @endforelse
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
<script>
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
</script>
@endpush
@endsection