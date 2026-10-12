@extends('layouts.admin')

@section('content')

<x-modal-form
    title="Add New Topping"
    action="{{ route('admin.toping.store') }}"
    method="POST"
    cancelUrl="{{ route('admin.toping.index') }}"
>

    {{-- Nama --}}
    <div>
        <label class="text-sm font-medium">Nama Topping</label>
        <input type="text"
               name="nama_toping"
               value="{{ old('nama_toping') }}"
               placeholder="Masukan nama topping"
               class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">

        @error('nama_toping')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Harga --}}
    <div>
        <label class="text-sm font-medium">Harga Toping</label>
        <div class="relative mt-1">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-semibold">Rp</span>
            <input type="text"
                   inputmode="numeric"
                   name="harga_toping"
                   value="{{ old('harga_toping') }}"
                   placeholder="0"
                   class="input-currency w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">
        </div>

        @error('harga_toping')
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

    {{-- Bisa Gratis --}}
    <div>
        <label class="text-sm font-medium flex items-center gap-1">
            Bisa Masuk Promo Gratis
            <span class="text-[10px] font-normal text-gray-400 ml-1">(2 topping gratis pertama)</span>
        </label>
        <select name="bisa_gratis"
        class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">
            <option value="1" {{ old('bisa_gratis', '1') == '1' ? 'selected' : '' }}>Bisa Gratis (Masuk Promo)</option>
            <option value="0" {{ old('bisa_gratis') == '0' ? 'selected' : '' }}>Selalu Berbayar (Tidak Masuk Promo)</option>
        </select>
        <p class="text-[10px] text-gray-400 mt-1">Jika "Selalu Berbayar", topping ini tidak akan digratiskan meskipun ada promo.</p>
    </div>

</x-modal-form>

@endsection