@extends('layouts.app')

@section('content')

{{-- Overlay --}}
<div class="fixed inset-0 z-[9999] flex items-center justify-center">
{{-- <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"> --}}

    {{-- Card --}}
    <div class="bg-white w-[380px] rounded-xl shadow-lg p-6 relative">

        <h2 class="text-lg font-semibold mb-4 px-6 py-4 border-b-2 border-green-500 text-center">Add New Cabang</h2>

        <form action="{{ route('cabang.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Nama Cabang --}}
            <div>
                <label class="text-sm font-medium">Nama Cabang</label>
                <input type="text"
                       name="nama_cabang"
                       value="{{ old('nama_cabang') }}"
                       placeholder="Masukan nama cabang"
                       class="w-full mt-1 px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-green-200">
            </div>

            {{-- Alamat --}}
            <div>
                <label class="text-sm font-medium">Alamat</label>
                <textarea name="alamat"
                          rows="3"
                          placeholder="Masukan alamat cabang"
                          class="w-full mt-1 px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-green-200">{{ old('alamat') }}</textarea>
            </div>

            {{-- Status --}}
            <div>
                <label class="text-sm font-medium">Status</label>
                <select name="is_active"
                        class="w-full mt-1 px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-green-200">
                    <option value="">-- Pilih Status --</option>
                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Non Aktif</option>
                </select>
            </div>


            {{-- Button --}}
            <div class="flex justify-end gap-2 pt-4">
                <button type="submit"
                        class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
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

@endsection