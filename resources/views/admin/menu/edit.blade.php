@extends('layouts.app')

@section('content')
<x-modal-form 
    title="Edit Data Menu" 
    :action="route('admin.menu.update', $menu->id_menu)" 
    method="PUT" 
    :cancelUrl="route('admin.menu.index')"
>
    {{-- Nama Menu --}}
    <div>
        <label class="text-sm font-medium text-gray-700">Nama Menu</label>
        <input type="text" name="nama_menu" 
            value="{{ old('nama_menu', $menu->nama_menu) }}"
            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all"
            placeholder="Contoh: Es Pisang Ijo"
            required>
    </div>

    {{-- Harga Menu --}}
    <div>
        <label class="text-sm font-medium text-gray-700">Harga Menu (Rp)</label>
        <input type="number" name="harga_menu" 
            value="{{ old('harga_menu', $menu->harga_menu) }}"
            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all"
            placeholder="0"
            required>
    </div>

    {{-- Status Menu --}}
    <div>
        <label class="text-sm font-medium text-gray-700">Status Ketersediaan</label>
        <select name="is_active" 
            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">
            <option value="1" {{ old('is_active', $menu->is_active) == 1 ? 'selected' : '' }}>Tersedia (Aktif)</option>
            <option value="0" {{ old('is_active', $menu->is_active) == 0 ? 'selected' : '' }}>Habis (Non-Aktif)</option>
        </select>
    </div>
</x-modal-form>
@endsection