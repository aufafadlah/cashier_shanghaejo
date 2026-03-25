@extends('layouts.app')

@section('content')

<x-modal-form
    title="Add New User"
    action="{{ route('admin.user.store') }}"
    method="POST"
    cancelUrl="{{ route('admin.user.index') }}"
>
    {{-- HAPUS @method('PUT') karena ini form CREATE --}}

    {{-- Nama Lengkap --}}
    <div>
        <label class="text-sm font-medium">Nama Lengkap</label>
        <input type="text"
               name="name"
               value="{{ old('name') }}"
               placeholder="Nama karyawan"
               required
               class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">

        @error('name')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        {{-- Username --}}
        <div>
            <label class="text-sm font-medium">Username</label>
            <input type="text"
                   name="username"
                   value="{{ old('username') }}"
                   placeholder="username"
                   required
                   class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">

            @error('username')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label class="text-sm font-medium">Password</label>
            <input type="password" 
                   name="password" 
                   placeholder="******"
                   required {{-- WAJIB diisi saat buat user baru --}}
                   class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">

            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        {{-- Role --}}
        <div>
            <label class="text-sm font-medium">Role</label>
            <select name="role" required class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">

                <option value="kasir" {{ old('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        {{-- Status --}}
        <div>
            <label class="text-sm font-medium">Status Akun</label>
            <select name="is_active" required class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">

                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Non-Aktif</option>
            </select>
        </div>
    </div>

    {{-- Penempatan Cabang --}}
    <div>
        <label class="text-sm font-medium">Penempatan Cabang</label>
        <select name="id_cabang"  class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">
            <option value="">-- Pilih Cabang (Pusat) --</option>
            @foreach($cabangs as $cabang)
                <option value="{{ $cabang->id_cabang }}" {{ old('id_cabang') == $cabang->id_cabang ? 'selected' : '' }}>
                    {{ $cabang->nama_cabang }}
                </option>
            @endforeach
        </select>
        <p class="text-[10px] text-gray-400 mt-1 italic leading-tight">*Pilih cabang tempat kasir bertugas.</p>
    </div>

</x-modal-form>

@endsection