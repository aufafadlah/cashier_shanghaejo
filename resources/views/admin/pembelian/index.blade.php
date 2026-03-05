@extends('layouts.app')

@section('content')
<div class="container-fluid p-6 bg-[#F8FAFC] min-h-screen">
    {{-- TITLE & ADD BUTTON --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Pengeluaran</h1>
            <p class="text-sm text-gray-400 mt-1">Kelola Pembelian Bahan atau Pengeluaran Anda</p>
        </div>
        {{-- Link ke halaman create yang sudah kita buat --}}
        <a href="{{ route('pembelian.create') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl shadow-xl hover:bg-gray-50 transition-all">
            <span class="text-[#76e15b] mr-2 text-xl">+</span> Add Pengeluaran
        </a>
    </div>

    {{-- TABLE SECTION --}}
    <div class="bg-white rounded-[2rem] shadow-xl border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50/50">
                <tr class="text-left text-gray-500 font-semibold text-sm">
                    <th class="px-6 py-4 text-center w-16">No</th>
                    <th class="px-6 py-4">Tgl</th>
                    <th class="px-6 py-4">Cabang</th> {{-- Kolom Cabang Baru --}}
                    <th class="px-6 py-4">Bahan / Barang</th>
                    <th class="px-6 py-4">Supplier</th>
                    <th class="px-6 py-4">Harga Satuan</th>
                    <th class="px-6 py-4">Total</th>
                    <th class="px-6 py-4">Ket</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pengeluaran as $key => $item)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-5 text-center text-gray-600">{{ $key + 1 }}.</td>
                    {{-- Format Tanggal --}}
                    <td class="px-6 py-5 text-gray-700">{{ \Carbon\Carbon::parse($item->tgl)->format('Y/m/d') }}</td>
                    
                    {{-- Nama Cabang dari Relasi --}}
                    <td class="px-6 py-5 text-gray-700 font-medium">
                        {{ $item->cabang->nama_cabang ?? 'N/A' }}
                    </td>

                    <td class="px-6 py-5 font-medium text-gray-800">{{ $item->nama_barang }}</td>
                    <td class="px-6 py-5 text-gray-600">{{ $item->supplier }}</td>
                    
                    {{-- Harga Satuan Format Rupiah --}}
                    <td class="px-6 py-5 text-gray-700">Rp. {{ number_format($item->harga_satuan) }}</td>
                    
                    {{-- Qty dan Harga Total --}}
                    <td class="px-6 py-5 font-bold text-gray-800">
                        {{ $item->qty }} Unit / Rp. {{ number_format($item->harga_total) }}
                    </td>

                    <td class="px-6 py-5 text-gray-400 italic text-sm">{{ $item->keterangan ?? 'Tidak Ada' }}</td>
                    
                    {{-- Tombol Aksi Titik Tiga --}}
                    <td class="px-6 py-5 text-center relative">
                        <button onclick="toggleMenu('{{ $item->id_pengeluaran }}')" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                            ⋮
                        </button>
                        
                        {{-- Dropdown Menu Hapus --}}
                        <div id="menu-{{ $item->id_pengeluaran }}" class="hidden absolute right-12 top-8 w-32 bg-white border border-gray-100 rounded-xl shadow-xl z-50 overflow-hidden">
                            <form action="{{ route('pembelian.destroy', $item->id_pengeluaran) }}" method="POST">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-20 text-center text-gray-400">
                        Belum ada data pengeluaran.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- SCRIPT DROPDOWN --}}
<script>
    function toggleMenu(id) {
        // Tutup semua menu yang mungkin sedang terbuka
        document.querySelectorAll('[id^="menu-"]').forEach(menu => {
            if (menu.id !== 'menu-' + id) {
                menu.classList.add('hidden');
            }
        });
        
        // Toggle menu yang dipilih
        const targetMenu = document.getElementById('menu-' + id);
        targetMenu.classList.toggle('hidden');
    }

    // Tutup menu jika klik di luar area tabel
    window.onclick = function(event) {
        if (!event.target.matches('button') && !event.target.closest('button')) {
            document.querySelectorAll('[id^="menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    }
</script>
@endsection