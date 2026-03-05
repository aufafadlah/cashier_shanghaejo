@extends('layouts.app')

@section('content')

<div class="fixed inset-0 z-[9999] flex items-center justify-center">

    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/30"></div>

    {{-- Card --}}
    <div class="bg-white w-[380px] rounded-xl shadow-lg p-6 relative">

        {{-- HEADER --}}
        <div class="h-14 flex items-center justify-center border-b-2 border-green-500">
            <h2 class="text-lg font-semibold">
                Update User
            </h2>
        </div>

        {{-- BODY --}}
        <div class="p-6">
        <form action="{{ route('user.update', $user->id_user) }}"
              method="POST"
              class="space-y-4">
            @csrf
            @method('PUT')

                <div>
                    <label class="text-sm font-medium">Nama User</label>
                    <input type="text" name="name"
                        value="{{ old('name', $user->name) }}"
                        class="w-full mt-1 px-3 py-2 border rounded-md focus:ring focus:ring-green-200">
                </div>

                <div>
        <label class="text-sm font-medium">Username</label>
        <input type="text" name="username" required class="w-full mt-1 px-3 py-2 border rounded-md">
                </div>

                <div>
        <label class="text-sm font-medium">Role</label>
        <select name="role" required class="w-full mt-1 px-3 py-2 border rounded-md">
            <option value="admin">Admin</option>
            <option value="kasir">Kasir</option>
        </select>
    </div>

                <div class="mb-4">
    <label class="text-sm font-medium text-gray-700">Status</label>
    <select name="is_active" class="w-full mt-1 px-3 py-2 border rounded-md" required>
        <option value="1">Aktif</option>
        <option value="0">Non-Aktif</option>
    </select>
</div>

                <div class="flex justify-end gap-2 pt-4">
                    <button class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                        Simpan
                    </button>
                    <a href="{{ route('cabang.index') }}"
                       class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection