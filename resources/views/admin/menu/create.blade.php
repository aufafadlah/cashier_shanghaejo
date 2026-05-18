@extends('layouts.admin')

@section('content')

<x-modal-form
    title="Add New Menu"
    action="{{ route('admin.menu.store') }}"
    method="POST"
    cancelUrl="{{ route('admin.menu.index') }}"
>

    {{-- Nama Menu --}}
    <div>
        <label class="text-sm font-medium">Nama Menu</label>
        <input type="text"
               name="nama_menu"
               value="{{ old('nama_menu') }}"
               placeholder="Masukan nama menu"
               class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">


        @error('nama_menu')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Harga Menu --}}
    <div>
        <label class="text-sm font-medium">Harga Menu</label>
        <input type="number"
               name="harga_menu"
               value="{{ old('harga_menu') }}"
               placeholder="Masukan harga menu"
               class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">


        @error('harga_menu')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status --}}
    <div>
        <label class="text-sm font-medium">Status</label>
        <select name="is_active"
        class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">


            <option value="">-- Pilih Status --</option>
            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Tersedia</option>
            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Habis</option>

        </select>
    </div>

</x-modal-form>

@endsection