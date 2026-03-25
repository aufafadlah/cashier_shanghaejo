@extends('layouts.app')

@section('content')
<div class="container-fluid p-4" x-data="{}"> 
    {{-- 1. HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Users</h1>
            <p class="text-xs text-gray-400 mt-1">Kelola Karyawan Bisnis Anda</p>
        </div>
        <a href="{{ route('admin.user.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-green-700 transition-all active:scale-95">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Add User
        </a>
    </div>

    {{-- 2. STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-card title="Total User" value="{{ $totalUsers }}" type="total" />
        <x-card title="Aktif" value="{{ $aktif }}" type="aktif" />
        <x-card title="Non Aktif" value="{{ $nonAktif }}" type="non-aktif" />
    </div>

    {{-- 3. TABEL --}}
    <x-table>
        <x-slot name="header">
            <tr>
                <th class="px-6 py-4 text-left">No</th>
                <th class="px-6 py-4 text-left">Nama Lengkap</th>
                <th class="px-6 py-4 text-left">Username</th>
                <th class="px-6 py-4 text-left">Role</th>
                <th class="px-6 py-4 text-left">Penempatan</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-center">Aksi</th>
            </tr>
        </x-slot>

        @forelse($users as $index => $user)
            <tr class="hover:bg-gray-50 border-b border-gray-100 transition-all">
                <td class="px-6 py-4 text-gray-500">{{ $index + 1 }}</td>
                <td class="px-6 py-4 font-bold text-gray-800">{{ $user->name }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $user->username }}</td>
                <td class="px-6 py-4 capitalize">
                    <x-status-badge :role="$user->role" />
                    
                </td>
                <td class="px-6 py-4 text-gray-700 font-medium">
                    {{-- Pastikan di Model User sudah ada relasi 'cabang' --}}
                    {{ $user->cabang->nama_cabang ?? 'pusat' }}
                </td>
                <td class="px-6 py-4 text-center">
                    {{-- Menggunakan komponen badge status jika ada --}}
                    <x-status-badge :active="$user->is_active" />
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        {{-- Edit --}}
                        <a href="{{ route('admin.user.edit', $user->id_user) }}" 
                           class="inline-flex items-center px-3 py-2 bg-amber-50 text-amber-600 text-xs font-bold rounded-lg hover:bg-amber-100 transition border border-amber-100">
                           ✏️ Edit
                        </a>
                        
                        {{-- Hapus --}}
                        <button type="button"
                            @click="$dispatch('open-modal-hapus-{{ $user->id_user }}')"
                            class="inline-flex items-center px-3 py-2 bg-red-50 text-red-600 text-xs font-bold rounded-lg hover:bg-red-100 transition border border-red-100">
                            <span class="mr-1">🗑️</span> Hapus
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-12 text-center text-gray-400 font-medium">
                    Tidak ada data user yang ditemukan.
                </td>
            </tr>
        @endforelse
    </x-table>
</div>

{{-- 4. MODAL HAPUS (Menggunakan komponen alert) --}}
@foreach($users as $user)
    <x-alert 
        id="{{ $user->id_user }}" 
        name="{{ $user->name }}" 
        action="{{ route('admin.user.destroy', $user->id_user) }}" 
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