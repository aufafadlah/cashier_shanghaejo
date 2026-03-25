@extends('layouts.app')

@section('content')
<x-modal-form 
    title="Edit Data Topping" 
    :action="route('admin.toping.update', $toping->id_toping)" 
    method="PUT" 
    :cancelUrl="route('admin.toping.index')"
>
    {{-- Nama --}}
    <div>
        <label class="text-sm font-medium text-gray-700">Nama Topping</label>
        <input type="text" name="nama_toping" 
            value="{{ old('nama_toping', $toping->nama_toping) }}"
            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all"
            placeholder="Contoh "
            required>
    </div>

    {{-- Harga --}}
    <div>
        <label class="text-sm font-medium text-gray-700">Harga Topping (Rp)</label>
        <input type="number" name="harga_toping" 
            value="{{ old('harga_menu', $toping->harga_toping) }}"
            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all"
            placeholder="0"
            required>
    </div>

    {{-- Status --}}
    <div>
        <label class="text-sm font-medium text-gray-700">Status Ketersediaan</label>
        <select name="is_active" 
            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">
            <option value="1" {{ old('is_active', $toping->is_active) == 1 ? 'selected' : '' }}>Tersedia (Aktif)</option>
            <option value="0" {{ old('is_active', $toping->is_active) == 0 ? 'selected' : '' }}>Habis (Non-Aktif)</option>
        </select>
    </div>
</x-modal-form>
@endsection