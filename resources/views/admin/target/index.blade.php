@extends('layouts.app')
@section('title', 'Kelola Target')
@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold mb-6">Kelola Target Harian</h2>

        <form action="{{ route('admin.target.store') }}" method="POST">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-md">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 text-left border-b">Cabang</th>
                            <th class="p-3 text-left border-b">Target (Porsi)</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach($cabangs as $c)
<tr class="hover:bg-gray-50">
    <td class="p-3 border-b">{{ $c->nama_cabang }}</td>
    <td class="p-3 border-b">
        <input type="number" 
               name="stok_target[{{ $c->id_cabang }}]" 
               value="{{ $c->stok_target }}" {{-- Tambahkan ini agar data muncul setelah refresh --}}
               data-default="{{ $c->stok_target }}" 
               placeholder="100 pcs" 
               class="w-full border-gray-300 rounded-md px-2 py-1 focus:ring-1 focus:ring-green-500">
    </td>
</tr>
@endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex justify-between">
                <button type="button" class="bg-gray-200 px-4 py-2 rounded text-sm text-gray-700 hover:bg-gray-300" onclick="setAllTargets()">
                    [Set 100 Porsi ke Semua Cabang]
                </button>
                <button type="button" onclick="simpanData()" class="bg-green-500 text-white px-6 py-2 rounded shadow hover:bg-green-600">
                    Simpan & Update
                </button>
            </div>
        </form>
    </div>

    {{-- Progress Card Tetap Sama --}}
    <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 mt-10">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-800 text-lg">Target Penjualan Hari Ini</h3>
            <select class="border-gray-300 rounded-md text-sm"><option>Pilih Cabang..</option></select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
                <p class="text-gray-700">Target : <span class="font-bold">100 Porsi</span></p>
                <p class="text-gray-700">Terjual : <span class="font-bold text-green-600">65 Porsi</span></p>
                <p class="text-gray-700">Tersisa : <span class="font-bold text-red-600">35 Porsi</span></p>
            </div>
            <div class="flex flex-col justify-center items-center">
                <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                    <div class="bg-green-500 h-4 rounded-full" style="width: 65%"></div>
                </div>
                <span class="text-sm font-bold text-gray-600">65%</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const inputs = document.querySelectorAll('input[name^="stok_target"]');
    const savedData = JSON.parse(localStorage.getItem("draft_target")) || {};

    inputs.forEach(input => {
        const key = input.name;
        const dbValue = input.dataset.default; // Data dari database

        // LOGIKA PRIORITAS:
        // 1. Jika di database SUDAH ADA isi (bukan 0 atau null), tampilkan data DB.
        // 2. Jika di database KOSONG, baru cek apakah ada draf di localStorage.
        if (dbValue && dbValue != "0" && dbValue != "") {
            input.value = dbValue;
            // Hapus draf untuk input ini saja karena DB sudah terisi
            delete savedData[key];
        } else if (savedData[key] !== undefined) {
            input.value = savedData[key];
        }

        input.addEventListener("input", function() {
            let currentDraft = JSON.parse(localStorage.getItem("draft_target")) || {};
            currentDraft[this.name] = this.value;
            localStorage.setItem("draft_target", JSON.stringify(currentDraft));
        });
    });
    
    // Simpan kembali draf yang sudah dibersihkan dari data yang sudah ada di DB
    localStorage.setItem("draft_target", JSON.stringify(savedData));
});

function simpanData() {
    const form = document.querySelector('form');
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // BERHASIL: Bersihkan localStorage agar saat refresh dia baca data terbaru DB
        localStorage.removeItem('draft_target');
        
        showToast("Data Berhasil Disimpan!", "success");

        // Update tampilan secara instan tanpa refresh total
        document.querySelectorAll('input[name^="stok_target"]').forEach(input => {
            input.dataset.default = input.value;
        });
    })
    .catch(error => {
        showToast("Gagal menyimpan data!", "error");
    });
}

function setAllTargets() {
    document.querySelectorAll('input[name^="stok_target"]').forEach(input => {
        input.value = 100;
        // Trigger event input agar tersimpan ke draf
        input.dispatchEvent(new Event('input'));
    });
}

</script>
@endpush