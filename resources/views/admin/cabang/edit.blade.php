@extends('layouts.admin')

@section('content')

<x-modal-form
    title="Update Cabang"
    action="{{ route('admin.cabang.update', $cabang->id_cabang) }}"
    method="PUT"
    cancelUrl="{{ route('admin.cabang.index') }}"
>

    {{-- Nama Cabang --}}
    <div>
        <label class="text-sm font-medium">Nama Cabang</label>
        <input type="text"
               name="nama_cabang"
               value="{{ old('nama_cabang', $cabang->nama_cabang) }}"
               class="w-full mt-1 px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-200">
    </div>

    {{-- Alamat --}}
    <div>
        <label class="text-sm font-medium">Alamat</label>
        <textarea name="alamat"
                  rows="3"
                  class="w-full mt-1 px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-200">{{ old('alamat', $cabang->alamat) }}</textarea>
    </div>

    {{-- Status --}}
    <div>
        <label class="text-sm font-medium">Status</label>
        <select name="is_active"
                class="w-full mt-1 px-3 py-2 border rounded-md focus:ring-2 focus:ring-green-200">

            <option value="1" {{ old('is_active', $cabang->is_active) == 1 ? 'selected' : '' }}>
                Aktif
            </option>

            <option value="0" {{ old('is_active', $cabang->is_active) == 0 ? 'selected' : '' }}>
                Non Aktif
            </option>

        </select>
    </div>

</x-modal-form>

@endsection