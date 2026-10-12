@extends('layouts.admin')

@section('content')
<x-modal-form 
    title="Edit Data Pengeluaran" 
    :action="route('admin.pembelian.update', $pengeluaran->id_pengeluaran)" 
    method="PUT" 
    :cancelUrl="route('admin.pembelian.index')"
>
    <div class="space-y-4">
        {{-- 1. Input Cabang --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Cabang</label>
            <select name="id_cabang" required class="w-full mt-1 px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-100 focus:border-[#7AB465] outline-none transition-all">
                @foreach($cabang as $c)
                    <option value="{{ $c->id_cabang }}" {{ old('id_cabang', $pengeluaran->id_cabang) == $c->id_cabang ? 'selected' : '' }}>
                        {{ $c->nama_cabang }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- 2. Nama Barang --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Bahan / Barang</label>
            <input type="text" name="nama_barang" 
                value="{{ old('nama_barang', $pengeluaran->nama_barang) }}"
                class="w-full mt-1 px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-100 focus:border-[#7AB465] outline-none"
                required>
        </div>

        <div class="grid grid-cols-2 gap-4">
            {{-- 3. Harga Satuan --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Harga Satuan (Rp)</label>
                <div class="relative mt-1">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-semibold">Rp</span>
                    <input type="text" inputmode="numeric" name="harga_satuan" 
                        value="{{ old('harga_satuan', (int) $pengeluaran->harga_satuan) }}"
                        class="input-currency w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-100 focus:border-[#7AB465] outline-none"
                        required>
                </div>
            </div>

            {{-- 4. Qty --}}
            <div>
                <label class="text-sm font-medium text-gray-700">Jumlah (Qty)</label>
                <input type="number" name="qty" 
                    value="{{ old('qty', $pengeluaran->qty) }}"
                    class="w-full mt-1 px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-100 focus:border-[#7AB465] outline-none"
                    required>
            </div>
        </div>

        {{-- 5. Supplier & Tanggal --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-700">Supplier</label>
                <input type="text" name="supplier" 
                    value="{{ old('supplier', $pengeluaran->supplier) }}"
                    class="w-full mt-1 px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-100 focus:border-[#7AB465] outline-none">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" name="tgl" 
                    value="{{ old('tgl', $pengeluaran->tgl) }}"
                    class="w-full mt-1 px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-100 focus:border-[#7AB465] outline-none"
                    required>
            </div>
        </div>

        {{-- 6. Keterangan --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Keterangan</label>
            <textarea name="keterangan" rows="2" 
                class="w-full mt-1 px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-100 focus:border-[#7AB465] outline-none">{{ old('keterangan', $pengeluaran->keterangan) }}</textarea>
        </div>
    </div>
</x-modal-form>
@endsection