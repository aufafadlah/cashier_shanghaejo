@extends('layouts.admin')

@section('content')

<x-modal-form
    title="Add New Cabang"
    action="{{ route('admin.cabang.store') }}"
    method="POST"
    cancelUrl="{{ route('admin.cabang.index') }}"
>

    {{-- Nama Cabang --}}
    <div>
        <label for="nama_cabang" class="text-sm font-medium">Nama Cabang</label>
        <input type="text"
               id="nama_cabang"
               name="nama_cabang"
               value="{{ old('nama_cabang') }}"
               placeholder="Masukan nama cabang"
               class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">

        @error('nama_cabang')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Alamat --}}
    <div>
        <label for="alamat" class="text-sm font-medium">Alamat</label>
        <textarea id="alamat"
                  name="alamat"
                  rows="3"
                  placeholder="Masukan alamat cabang"
                  class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">{{ old('alamat') }}</textarea>
        @error('alamat')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Status --}}
    <div>
        <label for="is_active" class="text-sm font-medium">Status</label>
        <select id="is_active"
                name="is_active"
                class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">
            <option value="">-- Pilih Status --</option>
            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Non Aktif</option>
        </select>
    </div>

</x-modal-form>

@endsection