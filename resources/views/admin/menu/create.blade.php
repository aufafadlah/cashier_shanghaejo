@extends('layouts.app')

@section('content')

{{-- Overlay --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/20">

    {{-- Card --}}
    <div class="bg-white w-[380px] rounded-xl shadow-lg p-6 relative">

        <h2 class="text-lg font-semibold mb-4 px-6 py-4 border-b-2 border-green-500 text-center">Add New Menu</h2>

        {{-- Menampilkan Error Validasi jika ada --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative mb-4 text-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('menu.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Nama Menu --}}
            <div>
                <label class="text-sm font-medium">Nama Menu</label>
                <input type="text"
                       name="nama_menu"
                       placeholder="Contoh: Pisang Ijo ChocoCruncy"
                       value="{{ old('nama_menu') }}"
                       class="w-full mt-1 px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-green-200"
                       required>
            </div>

            {{-- Harga Menu --}}
            <div>
                <label class="text-sm font-medium">Harga Menu (Rp)</label>
                <input type="number"
                       name="harga_menu"
                       placeholder="Contoh: 15000"
                       value="{{ old('harga_menu') }}"
                       class="w-full mt-1 px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-green-200"
                       required>
            </div>

            {{-- Status (Ganti ke is_active) --}}
            <div>
                <label class="text-sm font-medium">Status Ketersediaan</label>
                <select name="is_active"
                        class="w-full mt-1 px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-green-200">
                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Tersedia (Aktif)</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Habis (Non Aktif)</option>
                </select>
            </div>

            {{-- Button --}}
            <div class="flex justify-end gap-2 pt-4 border-t mt-4">
                <a href="{{ route('menu.index') }}"
                   class="bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500 transition">
                    Batal
                </a>
                <button type="submit"
                        class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">
                    Simpan Menu
                </button>
            </div>
        </form>

    </div>
</div>

@endsection