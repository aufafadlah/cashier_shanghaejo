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
                Update Cabang
            </h2>
        </div>

        {{-- BODY --}}
        <div class="p-6">
        <form action="{{ route('cabang.update', $cabang->id_cabang) }}"
              method="POST"
              class="space-y-4">
            @csrf
            @method('PUT')

                <div>
                    <label class="text-sm font-medium">Nama Cabang</label>
                    <input type="text" name="nama_cabang"
                        value="{{ old('nama_cabang', $cabang->nama_cabang) }}"
                        placeholder="Ubah nama cabang"
                        class="w-full mt-1 px-3 py-2 border rounded-md focus:ring focus:ring-green-200">
                </div>

                <div>
                    <label class="text-sm font-medium">Alamat</label>
                    <textarea name="alamat" rows="3"
                    placeholder="Ubah alamat cabang"
                        class="w-full mt-1 px-3 py-2 border rounded-md focus:ring focus:ring-green-200">{{ old('alamat', $cabang->alamat) }}</textarea>
                </div>

                <select name="is_active" class="w-full border rounded p-2">
                    <option value="1" {{ old('is_active', $cabang->is_active) == 1 ? 'selected' : '' }}>
                        Aktif
                    </option>
                    <option value="0" {{ old('is_active', $cabang->is_active) == 0 ? 'selected' : '' }}>
                        Non Aktif
                    </option>
                </select>

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