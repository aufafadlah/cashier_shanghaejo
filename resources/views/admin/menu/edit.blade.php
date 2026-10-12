@extends('layouts.admin')

@section('content')
<x-modal-form 
    title="Edit Data Menu" 
    :action="route('admin.menu.update', $menu->id_menu)" 
    method="PUT" 
    :cancelUrl="route('admin.menu.index')"
>
    {{-- Nama Menu --}}
    <div class="mb-4">
        <label class="text-sm font-medium text-gray-700">Nama Menu</label>
        <input type="text" name="nama_menu" 
            value="{{ old('nama_menu', $menu->nama_menu) }}"
            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all"
            placeholder="Contoh: Es Pisang Ijo"
            required>
        @error('nama_menu')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Pilihan Cabang & Harga Khusus Dinamis --}}
    <div class="mb-4">
        <label class="text-sm font-medium block mb-2 text-gray-700">Pilih Cabang & Atur Harga Khusus</label>
        <div class="space-y-3 p-3 border border-gray-100 rounded-lg bg-gray-50/50">
            @foreach($branches as $branch)
                @php
                    // Cari tahu apakah menu ini sudah terdaftar di cabang saat ini
                    $pivotData = $menu->cabangs->where('id_cabang', $branch->id_cabang)->first();
                    // Checkbox hanya centang jika pivot ada DAN is_active = true
                    $hasBranch = $pivotData && $pivotData->pivot->is_active;
                    // Ambil harga lama dari tabel jembatan jika ada, jika tidak kosongkan
                    $oldPrice = ($pivotData && $pivotData->pivot->harga_menu !== null) ? (int) $pivotData->pivot->harga_menu : '';
                @endphp

                <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-gray-100 shadow-sm">
                    {{-- Checkbox Cabang --}}
                    <label class="flex items-center space-x-3 cursor-pointer flex-1">
                        <input type="checkbox" 
                               name="branch_ids[]" 
                               value="{{ $branch->id_cabang }}"
                               {{ $hasBranch ? 'checked' : '' }}
                               class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <span class="text-sm text-gray-700 font-medium">{{ $branch->nama_cabang }}</span>
                    </label>

                    {{-- Input Harga Spesifik Cabang --}}
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-400">Rp</span>
                        <input type="text" 
                               inputmode="numeric"
                               name="harga_cabang[{{ $branch->id_cabang }}]" 
                               value="{{ old('harga_cabang.' . $branch->id_cabang, $oldPrice) }}"
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