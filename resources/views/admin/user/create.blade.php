@extends('layouts.app')

@section('content')

{{-- Overlay --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center">
{{-- <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"> --}}

    {{-- Card --}}
    <div class="bg-white w-[380px] rounded-xl shadow-lg p-6 relative">

        <h2 class="text-lg font-semibold mb-4 px-6 py-4 border-b-2 border-green-500 text-center">Add New User</h2>

<<form action="{{ route('user.store') }}" method="POST" class="space-y-4">
    @csrf

    {{-- Nama --}}
    <div>
        <label class="text-sm font-medium">Name</label>
        <input type="text" name="name" required class="w-full mt-1 px-3 py-2 border rounded-md">
    </div>

    {{-- Username (TAMBAHKAN INI) --}}
    <div>
        <label class="text-sm font-medium">Username</label>
        <input type="text" name="username" required class="w-full mt-1 px-3 py-2 border rounded-md">
    </div>

    {{-- Password --}}
    <div>
        <label class="text-sm font-medium">Password</label>
        <input type="password" name="password" required class="w-full mt-1 px-3 py-2 border rounded-md">
    </div>

    {{-- Role --}}
    <div>
        <label class="text-sm font-medium">Role</label>
        <select name="role" required class="w-full mt-1 px-3 py-2 border rounded-md">
            <option value="admin">Admin</option>
            <option value="kasir">Kasir</option>
        </select>
    </div>

   {{-- Status --}}
<div class="mb-4">
    <label class="text-sm font-medium text-gray-700">Status</label>
    <select name="is_active" class="w-full mt-1 px-3 py-2 border rounded-md" required>
        <option value="1">Aktif</option>
        <option value="0">Non-Aktif</option>
    </select>
</div>

    {{-- Button --}}
    <div class="flex justify-end gap-2 pt-4">
        <a href="{{ route('user.index') }}" class="bg-red-500 text-white px-4 py-2 rounded-md">Batal</a>
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md">Simpan</button>
    </div>
</form>

    </div>
</div>

@endsection