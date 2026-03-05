@extends('layouts.app')

@section('content')
<div class="container-fluid p-6 bg-[#F8FAFC] min-h-screen flex justify-center items-center">
    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 w-full max-w-lg">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Add Pengeluaran</h1>
        </div>

        {{-- Form action harus mengarah ke pembelian.store sesuai routes --}}
        <form action="{{ route('pembelian.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                
                {{-- 1. Input Cabang --}}
                <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Cabang</label>
    <select name="id_cabang" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-100 focus:border-[#7AB465] outline-none transition-all">
        <option value="">-- Pilih Cabang --</option>
        @foreach($cabang as $item)
            <option value="{{ $item->id_cabang }}">{{ $item->nama_cabang }}</option>
        @endforeach
    </select>
</div>

                {{-- 2. Input Bahan (nama_barang di DB) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bahan</label>
                    <input type="text" name="nama_barang" placeholder="Masukkan nama bahan" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl outline-none focus:border-[#7AB465]">
                </div>

                {{-- 3. Input Supplier --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <input type="text" name="supplier" placeholder="Masukkan nama supplier" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl outline-none focus:border-[#7AB465]">
                </div>

                {{-- 4. Input Harga Satuan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                    <input type="number" name="harga_satuan" id="harga_satuan" placeholder="Rp" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl outline-none focus:border-[#7AB465]">
                </div>

                {{-- 5. Input Qty (Total di Figma) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total (Qty)</label>
                    <input type="number" name="qty" id="qty" placeholder="Jumlah barang" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl outline-none focus:border-[#7AB465]">
                </div>

                {{-- 6. Input Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tgl" value="{{ date('Y-m-d') }}" required 
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl outline-none focus:border-[#7AB465]">
                </div>

                {{-- 7. Input Keterangan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="2" placeholder="Catatan tambahan" 
                              class="w-full px-4 py-2 border border-gray-200 rounded-xl outline-none focus:border-[#7AB465]"></textarea>
                </div>

            </div>

            {{-- Tombol Aksi Sesuai Figma --}}
            <div class="flex gap-4 mt-8">
                <a href="{{ route('pembelian.index') }}" 
                   class="flex-1 py-3 bg-[#E15B5B] text-white text-center font-bold rounded-xl hover:bg-red-600 transition-all">
                   Batal
                </a>
                <button type="submit" 
                        class="flex-1 py-3 bg-[#7AB465] text-white font-bold rounded-xl hover:bg-green-600 transition-all">
                   Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection