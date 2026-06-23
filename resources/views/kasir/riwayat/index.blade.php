@extends('layouts.kasir')
@section('title', 'Riwayat Pesanan')
@section('content')
<div class="h-screen bg-[#f1f5f9] p-3 font-sans overflow-hidden" x-data="{ deleteId: null }" x-cloak>

    <div class="max-w-[1600px] mx-auto h-[calc(100vh-140px)] flex flex-col">

        {{-- Header --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-4 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-slate-800 uppercase tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-teal-600"></i>
                    Riwayat Pesanan Hari Ini
                </h2>
                <p class="text-xs text-slate-400 mt-1 font-medium">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[10px] bg-teal-50 text-teal-700 px-3 py-1.5 rounded-lg font-bold uppercase tracking-wide">
                    <i class="fa-solid fa-receipt mr-1"></i> {{ count($orders) }} Pesanan
                </span>
                @php
                    $totalHariIni = $orders->sum('total_bayar');
                @endphp
                <span class="text-[10px] bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg font-bold uppercase tracking-wide">
                    <i class="fa-solid fa-coins mr-1"></i> Rp {{ number_format($totalHariIni) }}
                </span>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 flex-1 flex flex-col overflow-hidden">

            @if(session('success'))
            <div class="mx-5 mt-4 p-3 bg-green-50 border border-green-200 rounded-xl text-xs font-bold text-green-700 flex items-center gap-2">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mx-5 mt-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs font-bold text-red-700 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
            @endif

            @if(count($orders) > 0)
            {{-- Table Header --}}
            <div class="grid grid-cols-12 gap-3 px-5 py-3 border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold uppercase text-slate-400 tracking-wider">
                <div class="col-span-1 text-center">No</div>
                <div class="col-span-3">No. Struk</div>
                <div class="col-span-2">Pelanggan</div>
                <div class="col-span-1 text-center">Metode</div>
                <div class="col-span-2 text-right">Total</div>
                <div class="col-span-1 text-center">Jam</div>
                <div class="col-span-2 text-center">Aksi</div>
            </div>

            {{-- Table Body --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                @foreach($orders as $order)
                <div class="grid grid-cols-12 gap-3 px-5 py-3 border-b border-slate-50 items-center hover:bg-slate-50/50 transition-colors group">
                    {{-- No Urut --}}
                    <div class="col-span-1 text-center">
                        <span class="inline-flex items-center justify-center w-7 h-7 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-lg group-hover:bg-teal-50 group-hover:text-teal-700 transition-colors">
                            {{ $order->no_urut }}
                        </span>
                    </div>

                    {{-- No Struk --}}
                    <div class="col-span-3">
                        <span class="text-xs font-bold text-slate-800 tracking-wide font-mono">
                            SHJ-{{ date('dmy', strtotime($order->created_at)) }}-{{ str_pad($order->no_urut, 3, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    {{-- Pelanggan --}}
                    <div class="col-span-2">
                        <span class="text-xs font-semibold text-slate-700 uppercase truncate block">{{ $order->nama_pelanggan }}</span>
                        <span class="text-[9px] text-slate-400">Kasir: {{ $order->nama_kasir ?? '-' }}</span>
                    </div>

                    {{-- Metode --}}
                    <div class="col-span-1 text-center">
                        @if(strtolower($order->metode_pembayaran) === 'qris')
                        <span class="inline-block px-2 py-0.5 bg-purple-50 text-purple-600 text-[9px] font-bold rounded-md uppercase">QRIS</span>
                        @else
                        <span class="inline-block px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[9px] font-bold rounded-md uppercase">CASH</span>
                        @endif
                    </div>

                    {{-- Total --}}
                    <div class="col-span-2 text-right">
                        <span class="text-sm font-bold text-slate-800">Rp {{ number_format($order->total_bayar) }}</span>
                    </div>

                    {{-- Jam --}}
                    <div class="col-span-1 text-center">
                        <span class="text-[10px] font-semibold text-slate-500">{{ date('H:i', strtotime($order->created_at)) }}</span>
                    </div>

                    {{-- Aksi --}}
                    <div class="col-span-2 flex items-center justify-center gap-2">
                        <a href="{{ route('kasir.pesan.print', $order->id_penjualan) }}" target="_blank"
                           class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-bold hover:bg-blue-100 transition-colors uppercase">
                            <i class="fa-solid fa-print text-[9px]"></i> Cetak
                        </a>
                        <button type="button"
                                @click="deleteId = {{ $order->id_penjualan }}"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-50 text-red-500 rounded-lg text-[10px] font-bold hover:bg-red-100 transition-colors uppercase">
                            <i class="fa-solid fa-trash-can text-[9px]"></i> Hapus
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Footer Summary --}}
            <div class="px-5 py-3 border-t border-slate-200 bg-slate-50/50 flex justify-between items-center">
                <span class="text-[10px] text-slate-400 font-bold uppercase">Total Pesanan Hari Ini: {{ count($orders) }}</span>
                <span class="text-sm font-bold text-slate-800">Grand Total: Rp {{ number_format($totalHariIni) }}</span>
            </div>

            @else
            {{-- Empty State --}}
            <div class="flex-1 flex flex-col items-center justify-center opacity-40">
                <i class="fa-solid fa-box-open text-5xl text-slate-300 mb-4"></i>
                <p class="text-sm font-bold uppercase text-slate-400">Belum Ada Pesanan Hari Ini</p>
                <p class="text-[10px] text-slate-400 mt-1">Pesanan yang sudah checkout akan muncul di sini</p>
                <a href="{{ route('kasir.pesan.index') }}" class="mt-4 px-4 py-2 bg-teal-600 text-white text-xs font-bold rounded-lg hover:bg-teal-700 transition-colors uppercase">
                    <i class="fa-solid fa-store mr-1"></i> Buat Pesanan
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <template x-if="deleteId !== null">
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-transition>
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px]" @click="deleteId = null"></div>
            <div class="relative bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 text-center">
                <div class="w-14 h-14 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-red-500"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Hapus Pesanan?</h3>
                <p class="text-sm text-slate-500 mb-6">Pesanan yang dihapus tidak bisa dikembalikan lagi.</p>
                <div class="flex gap-3">
                    <button @click="deleteId = null" class="flex-1 py-2.5 border border-slate-200 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <form :action="'/kasir/riwayat/' + deleteId" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2.5 bg-red-500 rounded-xl text-sm font-bold text-white hover:bg-red-600 transition-colors">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
    body { overflow: hidden !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    [x-cloak] { display: none !important; }
</style>
@endsection