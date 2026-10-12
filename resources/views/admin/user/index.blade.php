@extends('layouts.admin')
@section('title', 'Kelola Users')
@section('content')
<div class="container-fluid p-4" x-data="{}"> 
    {{-- 1. HEADER & SEARCH --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Users</h1>
            <p class="text-xs text-gray-400 mt-1">Kelola Karyawan Bisnis Anda</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <form action="{{ route('admin.user.index') }}" method="GET" class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / username..." 
                    class="bg-white border border-gray-200 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 rounded-xl pl-9 pr-8 py-2 text-xs outline-none transition shadow-sm w-56 sm:w-64">
                @if(request('search'))
                    <a href="{{ route('admin.user.index') }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-xs" title="Reset">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
            <a href="{{ route('admin.user.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-xl shadow-lg hover:bg-green-700 transition-all active:scale-95">
                <i class="fa-solid fa-user-plus mr-2 text-xs"></i>
                Add User
            </a>
        </div>
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
                <td class="px-6 py-4 text-gray-500">{{ $users->firstItem() ? ($users->firstItem() + $index) : ($index + 1) }}</td>
                <td class="px-6 py-4 font-bold text-gray-800">{{ $user->name }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $user->username }}</td>
                <td class="px-6 py-4 capitalize">
                    <x-status-badge :role="$user->role" />
                </td>
                <td class="px-6 py-4 text-gray-700 font-medium">
                    {{ $user->cabang ? $user->cabang->nama_cabang : 'Pusat' }}
                </td>
                <td class="px-6 py-4 text-center">
                    <x-status-badge :active="$user->is_active" />
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        {{-- Edit --}}
                        <a href="{{ route('admin.user.edit', $user->id_user) }}" 
                           class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-600 text-xs font-bold rounded-lg hover:bg-amber-100 transition border border-amber-100">
                           <i class="fa-solid fa-pen-to-square mr-1.5"></i> Edit
                        </a>
                        
                        {{-- Hapus --}}
                        <button type="button"
                            @click="$dispatch('open-modal-hapus-{{ $user->id_user }}')"
                            class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 text-xs font-bold rounded-lg hover:bg-red-100 transition border border-red-100">
                            <i class="fa-solid fa-trash-can mr-1.5"></i> Hapus
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="py-12 text-center text-gray-400 font-medium">
                    <i class="fa-solid fa-users text-3xl mb-2 text-gray-300 block"></i>
                    Tidak ada data user yang ditemukan.
                </td>
            </tr>
        @endforelse

        @if($users->hasPages())
            <x-slot name="pagination">
                {{ $users->links() }}
            </x-slot>
        @endif
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

@endpush
@endsection