@extends('layouts.admin')

@section('content')

<x-modal-form
    title="Edit User: {{ $user->name }}"
    action="{{ route('admin.user.update', $user->id_user) }}"
    method="POST"
    cancelUrl="{{ route('admin.user.index') }}"
>
    @method('PUT') {{-- Penting untuk proses update di Laravel --}}

    {{-- Nama Lengkap --}}
    <div>
        <label class="text-sm font-medium text-gray-700">Nama Lengkap</label>
        <input type="text" 
               name="name" 
               value="{{ old('name', $user->name) }}" 
               required 
               class="w-full mt-1 px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-200 outline-none transition">
        @error('name')
            <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        {{-- Username --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Username</label>
            <input type="text" 
                   name="username" 
                   value="{{ old('username', $user->username) }}" 
                   required 
                   class="w-full mt-1 px-3 py-2 border bg-gray-50 rounded-md focus:ring-2 focus:ring-green-200 outline-none transition">
            @error('username')
                <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password with Toggle --}}
        <div x-data="{ showPass: false }">
            <label class="text-sm font-medium text-gray-700">Password Baru</label>
            <div class="relative">
                <input :type="showPass ? 'text' : 'password'" 
                       name="password" 
                       placeholder="Kosongkan jika tidak diganti"
                       class="w-full mt-1 px-3 py-2 pr-10 border rounded-md focus:ring-2 focus:ring-green-200 outline-none transition">
                <button type="button" @click="showPass = !showPass" tabindex="-1" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-600 transition-colors">
                    <i class="fa-solid" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
            <p class="text-[9px] text-gray-400 mt-1 italic leading-tight">*Isi hanya jika ingin mengubah password</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        {{-- Role --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Role</label>
            <select name="role" required class="w-full mt-1 px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-200 outline-none transition">
                <option value="kasir" {{ old('role', $user->role) == 'kasir' ? 'selected' : '' }}>Kasir</option>
                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        {{-- Status --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Status Akun</label>
            <select name="is_active" required class="w-full mt-1 px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-200 outline-none transition">
                <option value="1" {{ old('is_active', $user->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ old('is_active', $user->is_active) == '0' ? 'selected' : '' }}>Non-Aktif</option>
            </select>
        </div>
    </div>

    {{-- Penempatan Cabang --}}
    <div>
        <label class="text-sm font-medium text-gray-700">Penempatan Cabang</label>
        <select name="id_cabang" class="w-full mt-1 px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-200 outline-none transition">
            <option value="">-- Pilih Cabang (Pusat) --</option>
            @foreach($cabangs as $cabang)
                <option value="{{ $cabang->id_cabang }}" {{ old('id_cabang', $user->id_cabang) == $cabang->id_cabang ? 'selected' : '' }}>
                    {{ $cabang->nama_cabang }}
                </option>
            @endforeach
        </select>
        <p class="text-[10px] text-gray-400 mt-1 italic">*Kosongkan jika user memiliki akses ke semua cabang (Admin Pusat).</p>
    </div>

</x-modal-form>

@endsection