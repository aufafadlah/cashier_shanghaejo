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
                Edit Data Menu
            </h2>
        </div>

        {{-- BODY --}}
        <div class="p-6">
            <form action="{{ route('menu.update', $menu->id_menu) }}" 
                  method="POST" 
                  class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Nama Menu --}}
                <div>
                    <label class="text-sm font-medium">Nama Menu</label>
                    <input type="text" name="nama_menu" 
                        value="{{ old('nama_menu', $menu->nama_menu) }}"
                        class="w-full mt-1 px-3 py-2 border rounded-md focus:ring focus:ring-green-200"
                        required>
                </div>

                {{-- Harga Menu --}}
                <div>
                    <label class="text-sm font-medium">Harga Menu (Rp)</label>
                    <input type="number" name="harga_menu" 
                        value="{{ old('harga_menu', $menu->harga_menu) }}"
                        class="w-full mt-1 px-3 py-2 border rounded-md focus:ring focus:ring-green-200"
                        required>
                </div>

                {{-- Status Menu (is_active) --}}
                <div>
                    <label class="text-sm font-medium">Status Ketersediaan</label>
                    <select name="is_active" class="w-full mt-1 border rounded-md p-2 focus:ring focus:ring-green-200">
                        <option value="1" {{ old('is_active', $menu->is_active) == 1 ? 'selected' : '' }}>
                            Tersedia (Aktif)
                        </option>
                        <option value="0" {{ old('is_active', $menu->is_active) == 0 ? 'selected' : '' }}>
                            Habis (Non Aktif)
                        </option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="flex justify-end gap-2 pt-4 border-t">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('menu.index') }}" 
                       class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection