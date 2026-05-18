@extends('layouts.admin')

@section('content')
<div class="p-8 bg-[#F8F9FA] min-h-screen">
    <div class="mb-8">
        
        <h2 class="text-2xl font-black text-gray-800">Laporan Target Penjualan</h2>
        <p class="text-xs text-gray-500 italic mt-1">Monitoring pencapaian berdasarkan periode Bulan & Tahun</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-card title="Total Rencana Stok" :value="number_format($summary['total_stok'])" type="total" />
        <x-card title="Total Over Target" :value="'+' . number_format($summary['total_over'])" type="aktif" />
        <x-card title="Rata-rata Capaian" :value="round($summary['persentase'], 1) . '%'" type="total" />
    </div>

<div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm mb-6">
    <form action="{{ route('admin.laporantarget.index') }}" method="GET" class="flex flex-wrap items-end gap-5">
        
        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Pilih Cabang</label>
            <select name="id_cabang" class="border border-gray-200 rounded-lg px-4 py-2 text-sm bg-gray-50 focus:border-[#7AB465] outline-none">
                <option value="">Semua Cabang</option>
                @foreach($cabangs as $c)
                    <option value="{{ $c->id_cabang }}" {{ request('id_cabang') == $c->id_cabang ? 'selected' : '' }}>
                        {{ $c->nama_cabang }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="h-10 w-[1px] bg-gray-200 hidden md:block mb-1"></div>

        <div class="flex flex-col">
            <label class="text-[10px] font-bold text-[#7AB465] uppercase mb-1 ml-1">Filter Per Hari</label>
            <input type="date" name="tanggal" id="filter_tanggal" value="{{ request('tanggal') }}" 
                   class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-green-100 focus:border-[#7AB465] outline-none transition-all">
        </div>

        <span class="text-gray-300 pb-2 text-xs font-bold uppercase">Atau</span>

        <div class="flex items-end gap-2">
            <div class="flex flex-col">
                <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Bulan</label>
                <select name="bulan" id="filter_bulan" class="border border-gray-200 rounded-lg px-4 py-2 text-sm bg-gray-50 focus:border-[#7AB465] outline-none disabled:bg-gray-100 disabled:cursor-not-allowed">
                    <option value="">-- Pilih Bulan --</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                    
                </select>
            </div>

            <div class="flex flex-col">
                <label class="text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Tahun</label>
                <select name="tahun" id="filter_tahun" class="border border-gray-200 rounded-lg px-4 py-2 text-sm bg-gray-50 focus:border-[#7AB465] outline-none disabled:bg-gray-100 disabled:cursor-not-allowed">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="bg-[#7AB465] text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-[#69a054] shadow-sm transition-all">
                Tampilkan
            </button>
            <button onclick="printLaporan()" 
    class="bg-gray-800 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-black transition-all">
    Print
</button>
            <a href="{{ route('admin.laporantarget.index') }}" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Reset Filter">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
            </a>
        </div>
        
        
    </form>
</div>

<script>
  
    function printLaporan() {
        window.print();
    }
 
    // Logika agar tidak bingung: Kalau isi Tanggal, Bulan/Tahun mati. Begitu juga sebaliknya.
    const inputTanggal = document.getElementById('filter_tanggal');
    const selectBulan = document.getElementById('filter_bulan');
    const selectTahun = document.getElementById('filter_tahun');

    function toggleFilters() {
        if (inputTanggal.value) {
            selectBulan.disabled = true;
            selectTahun.disabled = true;
            selectBulan.value = "";
        } else {
            selectBulan.disabled = false;
            selectTahun.disabled = false;
        }
    }

    inputTanggal.addEventListener('input', toggleFilters);
    window.addEventListener('load', toggleFilters); // Jalankan saat halaman load (biar awet pas filter aktif)
</script>
    <div id="area-print">

    <x-table >
        <x-slot name="header">
            <tr>
                <th class="p-4 text-center">No</th>
                <th class="p-4 text-left">Tanggal</th> {{-- Kolom Tanggal --}}
                <th class="p-4 text-left">Menu & Cabang</th>
                <th class="p-4 text-center">Target</th>
                <th class="p-4 text-center">Over</th>
                <th class="p-4 text-center">Total</th>
                <th class="p-4 text-center">Capaian</th>
            </tr>
        </x-slot>


        @forelse($laporanTarget as $key => $item)

            <tr class="hover:bg-gray-50/50">
                <td class="p-4 text-center text-gray-400">{{ $key + 1 }}</td>
                <td class="p-4 text-sm font-medium text-gray-700">
                    {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
                </td>
                <td class="p-4 text-left">
                    <div class="font-bold text-gray-800">{{ $item->nama_menu }}</div>
                    <div class="text-[10px] text-gray-400 uppercase">{{ $item->nama_cabang }}</div>
                </td>
                <td class="p-4 text-center text-gray-600">{{ $item->stok_target }}</td>
                <td class="p-4 text-center text-green-600 font-bold">+{{ $item->over_target }}</td>
                <td class="p-4 text-center font-black text-gray-800"> {{ $item->total_penjualan }} </td>
                <td class="p-4 text-center">
                    <span class="px-2 py-1 rounded text-[10px] font-bold 
    {{ $item->persen >= 100 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
    {{ $item->persen }}%
</span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="p-12 text-center text-gray-400 italic">Tidak ada data target pada periode ini.</td>
            </tr>
        @endforelse
    </x-table>
</div>
</div>
<style>
@media print {
    body * {
        visibility: hidden;
    }

    #area-print, #area-print * {
        visibility: visible;
    }

    #area-print {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    button, form {
        display: none !important;
    }
}
</style>

@endsection