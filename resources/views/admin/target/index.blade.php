@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    {{-- notif --}}
<div id="notif-container" class="fixed top-5 right-5 z-[100] flex flex-col gap-3"></div>
    <!-- Card: Kelola Target Harian -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold mb-6">Kelola Target Harian</h2>

        <form action="{{ route('admin.target.store') }}" method="POST">
            @csrf

            <!-- Pilih Cabang -->
            {{-- <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Cabang</label>
                <select name="cabang_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-1 focus:ring-green-500 focus:border-green-500">
                    @foreach($cabangs as $c)
                        <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div> --}}

            <!-- Tabel Target -->
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
                                <input type="number" value="{{ $c->stok_target }}" name="stok_target[{{ $c->id_cabang }}]" placeholder="100 pcs" class="w-full border-gray-300 rounded-md px-2 py-1 focus:ring-1 focus:ring-green-500 focus:border-green-500">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Tombol -->
            <div class="mt-4 flex justify-between">
    <button type="button" class="bg-gray-200 px-4 py-2 rounded text-sm text-gray-700 hover:bg-gray-300" onclick="setAllTargets()">
        [Set 100 Porsi ke Semua Cabang]
    </button>
    
    {{-- Ubah ke submit agar form terkirim --}}
    <button type="button"  onclick="simpanData()" class="bg-green-500 text-white px-6 py-2 rounded shadow hover:bg-green-600">
        Simpan & Update
    </button>
</div>

    <!-- Card: Target Hari Ini -->
    <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 mt-10">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-800 text-lg">Target Penjualan Hari Ini</h3>
            <select class="border-gray-300 rounded-md text-sm">
                <option>Pilih Cabang..</option>
            </select>
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
        <p class="mt-4 text-xs text-gray-400 italic font-medium">Update Terakhir : 14.00 Wib</p>
    </div>

</div>
{{-- target all 100 --}}
<script>
    function setAllTargets() {
        document.querySelectorAll('input[name^="stok_target"]').forEach(input => {
            input.value = 100;
        });
    }
</script>

<!-- Script untuk tombol set semua target -->
<script>
function simpanData() {
    const form = document.querySelector('form');
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        // Panggil fungsi toast Tailwind
        showToast("Data Berhasil Diupdate!", "success");
    })
    .catch(error => {
        console.error('Error:', error);
        showToast("Gagal menyimpan data!", "error");
    });
}

// Fungsi untuk membuat elemen Toast Tailwind secara dinamis
function showToast(message, type) {
    const container = document.getElementById('notif-container');
    const id = Date.now(); // ID unik untuk setiap toast

    const bgColor = type === 'success' ? 'bg-green-50' : 'bg-red-50';
    const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
    const borderColor = type === 'success' ? 'border-green-100' : 'border-red-100';
    const iconColor = type === 'success' ? 'text-green-500' : 'text-red-500';

    const toastHtml = `
        <div id="toast-${id}" class="flex items-center p-4 w-80 ${textColor} rounded-2xl ${bgColor} border ${borderColor} shadow-lg transition-all duration-500 transform translate-x-full opacity-0" role="alert">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 ${iconColor} bg-white rounded-lg shadow-sm">
                ${type === 'success' 
                    ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/></svg>'
                    : '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793L12.793 10l3.207-3.207a1 1 0 0 0-1.414-1.414L11.379 8.586 8.172 5.379a1 1 0 0 0-1.414 1.414L9.966 10l-3.207 3.207a1 1 0 0 0 1.414 1.414l3.207-3.207 3.207 3.207a1 1 0 0 0 1.414-1.414L11.379 11.414l3.207-3.207Z"/></svg>'
                }
            </div>
            <div class="ms-3 text-sm font-bold">${message}</div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 p-1.5 inline-flex items-center justify-center h-8 w-8 text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300" onclick="removeToast(${id})">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
            </button>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', toastHtml);

    // Animasi Masuk (Slide in dari kanan)
    setTimeout(() => {
        const toast = document.getElementById(`toast-${id}`);
        if(toast) {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        }
    }, 100);

    // Otomatis Hilang setelah 4 detik
    setTimeout(() => removeToast(id), 4000);
}

function removeToast(id) {
    const toast = document.getElementById(`toast-${id}`);
    if(toast) {
        toast.classList.add('opacity-0', 'scale-90');
        setTimeout(() => toast.remove(), 500);
    }
}
</script>
</script>
@endsection