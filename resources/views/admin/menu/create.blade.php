@extends('layouts.admin')

@section('content')

<x-modal-form
    title="Add New Menu"
    action="{{ route('admin.menu.store') }}"
    method="POST"
    cancelUrl="{{ route('admin.menu.index') }}"
>

    {{-- Nama Menu --}}
    <div class="mb-4">
        <label class="text-sm font-medium text-gray-700">Nama Menu</label>
        <input type="text"
               name="nama_menu"
               value="{{ old('nama_menu') }}"
               placeholder="Contoh: Pisang Ijo Original"
               class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">

        @error('nama_menu')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Kategori Menu: Sinkronisasi value dengan string nama kategori yang disimpan --}}
    <div class="mb-4">
       
        @error('kategori_menu')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- KUNCI UTAMA: Input Harga Tunggal Dihapus! 
         Digantikan dengan Input Harga Spesifik di Samping Nama Cabang --}}
    <div class="mb-4">
        <label class="text-sm font-medium block mb-2 text-gray-700">Pilih Cabang & Atur Harga Khusus</label>
        <div class="space-y-3 p-3 border border-gray-100 rounded-lg bg-gray-50/50">
            @foreach($branches as $branch)
                <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-gray-100 shadow-sm">
                    
                    {{-- Checkbox Cabang --}}
                    <label class="flex items-center space-x-3 cursor-pointer flex-1">
                        <input type="checkbox" 
                               name="branch_ids[]" 
                               value="{{ $branch->id_cabang }}"
                               {{ is_array(old('branch_ids')) && in_array($branch->id_cabang, old('branch_ids')) ? 'checked' : '' }}
                               class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700 font-medium">{{ $branch->nama_cabang }}</span>
                    </label>

                    {{-- Input Harga Spesifik untuk Cabang Ini --}}
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-400">Rp</span>
                        <input type="text" 
                               inputmode="numeric"
                               name="harga_cabang[{{ $branch->id_cabang }}]" 
                               value="{{ old('harga_cabang.' . $branch->id_cabang) }}" 
                               placeholder="0"
                               class="input-currency w-40 sm:w-44 px-2 py-1 text-sm border border-gray-200 rounded-md focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none">
                    </div>
                </div>
            @endforeach
        </div>

        @error('branch_ids')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

</x-modal-form>

@endsection