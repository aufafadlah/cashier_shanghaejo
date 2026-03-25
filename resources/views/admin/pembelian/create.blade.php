@extends('layouts.app')

@section('content')
<x-modal-form
    title="Tambah Pengeluaran Baru"
    :action="route('admin.pembelian.store')"
    method="POST"
    :cancelUrl="route('admin.pembelian.index')"
>
    <div class="space-y-4">
        {{-- 1. Input Cabang --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cabang</label>
            <select name="id_cabang" required   
            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">
                @foreach($cabang as $item)
                    <option value="{{ $item->id_cabang }}">{{ $item->nama_cabang }}</option>
                @endforeach
            </select>
        </div>

        {{-- 2. Input Bahan --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bahan / Barang</label>
            <input type="text" name="nama_barang" placeholder="Contoh: Plastik, Es Batu, Gula" required 
            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">

        </div>

        {{-- 3. Input Supplier --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
            <input type="text" name="supplier" placeholder="Nama toko / vendor" required 
            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">

        </div>

        <div class="grid grid-cols-2 gap-4">
            {{-- 4. Harga Satuan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                <input type="number" name="harga_satuan" id="harga_satuan" placeholder="Rp" required 
                class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">

            </div>

            {{-- 5. Qty --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Qty)</label>
                <input type="number" name="qty" id="qty" placeholder="0" required 
                class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">


            </div>
        </div>

        {{-- 6. Tanggal --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pembelian</label>
            <input type="date" name="tgl" value="{{ date('Y-m-d') }}" required 
            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">


        </div>

        {{-- 7. Keterangan --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
            <textarea name="keterangan" rows="2" placeholder="Catatan tambahan (opsional)" 
            class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-100 focus:border-green-400 outline-none transition-all">


        </div>
    </div>
</x-modal-form>

{{-- Script Hitung Otomatis (Opsional tapi sangat membantu) --}}
<script>
    const hargaInput = document.getElementById('harga_satuan');
    const qtyInput = document.getElementById('qty');

    // Kamu bisa tambahkan input hidden untuk harga_total jika di Controller tidak dihitung manual
</script>
@endsection