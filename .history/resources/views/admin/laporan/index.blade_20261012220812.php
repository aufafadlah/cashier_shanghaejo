@extends('layouts.admin')
@section('title', 'Laporan Penjualan')

@section('content')
@php
    // ── Harian helpers ─────────────────────────────────────────────
    $weekStart  = $harian['weekStart'];
    $weekEnd    = $harian['weekEnd'];
    $isThisWeek = $weekStart->isSameWeek(now(), Carbon\Carbon::MONDAY);

    // ── Bulanan helpers ─────────────────────────────────────────────
    $tahunBulanan  = $bulanan['tahun'];
    $isThisYear    = $tahunBulanan === intval(now()->format('Y'));

    // ── Tahunan helpers ─────────────────────────────────────────────
    $tahunMulai    = $tahunan['tahunMulai'];
    $tahunSekarang = $tahunan['tahunSekarang'];

    // ── Initial tab helper ──────────────────────────────────────────
    // ── Initial tab helper ──────────────────────────────────────────
    $initialTab = request('tab', 'harian');
    if (!request()->has('tab')) {
        if (request()->has('tanggal'))           $initialTab = 'mingguan';
        elseif (request()->has('tahun_bulanan')) $initialTab = 'bulanan';
        elseif (request()->has('tahunan'))       $initialTab = 'tahunan';
        elseif (request()->has('single_date'))   $initialTab = 'harian';
    } elseif (request('tab') === 'single') {
        $initialTab = 'harian';
    } elseif (request('tab') === 'harian' && request()->has('tanggal') && !request()->has('single_date')) {
        $initialTab = 'mingguan';
    }
@endphp

<div class="p-2" x-data="{ tab: '{{ $initialTab }}' }">

    {{-- PAGE TITLE & EXPORT BUTTONS --}}
    <div class="mb-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Laporan Penjualan</h1>
            <p class="text-xs text-gray-400 mt-0.5">Analisis performa transaksi harian, mingguan, bulanan &amp; tahunan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a :href="`{{ route('admin.laporan.export.excel') }}?tab=${tab}&tanggal={{ $harian['tanggal'] }}&single_date={{ $harianSingle['tanggal'] }}&tahun_bulanan={{ $bulanan['tahun'] }}&id_cabang={{ $id_cabang }}`"
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>
            <a :href="`{{ route('admin.laporan.export.pdf') }}?tab=${tab}&tanggal={{ $harian['tanggal'] }}&single_date={{ $harianSingle['tanggal'] }}&tahun_bulanan={{ $bulanan['tahun'] }}&id_cabang={{ $id_cabang }}`"
               target="_blank"
               class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    {{-- TAB NAVIGATION (Harian, Mingguan, Bulanan, Tahunan) --}}
    <div class="flex gap-1 mb-6 bg-gray-100 p-1 rounded-2xl w-fit shadow-inner">
        <button @click="tab='harian'"
            :class="tab==='harian' ? 'bg-white text-teal-700 shadow' : 'text-gray-500 hover:text-gray-700'"
            class="px-5 py-2 text-sm font-bold rounded-xl transition-all duration-200 flex items-center gap-2">
            <i class="fa-solid fa-calendar-day text-xs"></i> Harian
        </button>
        <button @click="tab='mingguan'"
            :class="tab==='mingguan' ? 'bg-white text-teal-700 shadow' : 'text-gray-500 hover:text-gray-700'"
            class="px-5 py-2 text-sm font-bold rounded-xl transition-all duration-200 flex items-center gap-2">
            <i class="fa-solid fa-calendar-week text-xs"></i> Mingguan
        </button>
        <button @click="tab='bulanan'"
            :class="tab==='bulanan' ? 'bg-white text-teal-700 shadow' : 'text-gray-500 hover:text-gray-700'"
            class="px-5 py-2 text-sm font-bold rounded-xl transition-all duration-200 flex items-center gap-2">
            <i class="fa-solid fa-calendar text-xs"></i> Bulanan
        </button>
        <button @click="tab='tahunan'"
            :class="tab==='tahunan' ? 'bg-white text-teal-700 shadow' : 'text-gray-500 hover:text-gray-700'"
            class="px-5 py-2 text-sm font-bold rounded-xl transition-all duration-200 flex items-center gap-2">
            <i class="fa-solid fa-chart-line text-xs"></i> Tahunan
        </button>
    </div>

    {{-- ================================================================
         TAB HARIAN (Harian: Per Hari Spesifik)
    ================================================================ --}}
    <div x-show="tab==='harian'" x-cloak>
        {{-- Filter Harian --}}
        <form method="GET" action="{{ route('admin.laporan.index') }}"
              class="flex flex-wrap items-center gap-2 mb-5 bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-sm">
            <input type="hidden" name="tab" value="harian">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mr-2">Pilih Tanggal:</span>
            <input type="date" name="single_date" value="{{ $harianSingle['tanggal'] }}" max="{{ now()->toDateString() }}"
                   onchange="this.form.submit()"
                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 cursor-pointer">
        </form>

        {{-- Stats Harian --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <x-card title="Total Transaksi"  value="{{ number_format($harianSingle['totalPenjualan']) }}" type="total" />
            <x-card title="Total Pendapatan" value="Rp {{ number_format($harianSingle['totalPendapatan'], 0, ',', '.') }}" type="aktif" />
            <x-card title="Total Pelanggan"  value="{{ number_format($harianSingle['totalPelanggan']) }}" type="total" />
        </div>

        {{-- Table Harian --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="p-5 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest">Transaksi Harian — {{ \Carbon\Carbon::parse($harianSingle['tanggal'])->translatedFormat('d F Y') }}</h3>
            </div>
            <x-table>
                <x-slot name="header">
                    <tr>
                        <th class="p-4 text-center w-10 text-xs">No</th>
                        <th class="p-4 text-left text-xs">Hari & Waktu</th>
                        <th class="p-4 text-left text-xs">Pelanggan</th>
                        <th class="p-4 text-left text-xs">Total</th>
                        <th class="p-4 text-center text-xs">Metode</th>
                        <th class="p-4 text-center text-xs">Status</th>
                        <th class="p-4 text-left text-xs">Kasir Cabang</th>
                    </tr>
                </x-slot>
               @forelse($harianSingle['recentTransaksi'] as $i => $trx)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="p-4 text-center text-gray-400 text-xs">{{ $harianSingle['recentTransaksi']->firstItem() + $i }}</td>
                    <td class="p-4 text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($trx->created_at)->translatedFormat('D, d M H:i') }}</td>
                    <td class="p-4 text-sm text-gray-800">{{ $trx->nama_pelanggan ?? '-' }}</td>
                    <td class="p-4 text-sm font-black text-gray-800">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                    <td class="p-4 text-center">
                        @if(strtolower($trx->metode_pembayaran ?? '') === 'qris')
                            <span class="px-2 py-1 rounded-lg bg-teal-50 text-teal-700 text-[10px] font-bold border border-teal-100">QRIS</span>
                        @else
                            <span class="px-2 py-1 rounded-lg bg-yellow-50 text-yellow-700 text-[10px] font-bold border border-yellow-100">Cash</span>
                        @endif
                    </td>
                    <td class="p-4 text-center"><x-status-badge :active="($trx->status_transaksi ?? '') === 'berhasil'" /></td>
                    <td class="p-4 text-xs font-semibold text-gray-700">
                        <span class="font-bold text-gray-800">{{ $trx->nama_kasir ?? 'Kasir' }}</span>
                        <span class="text-teal-700 block text-[10px]">({{ $trx->nama_cabang ?? 'Pusat' }})</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-12 text-center text-sm text-gray-400"><i class="fa-solid fa-inbox text-2xl mb-2 block"></i>Belum ada transaksi pada tanggal ini.</td></tr>
                @endforelse
            </x-table>
            @if($harianSingle['recentTransaksi']->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $harianSingle['recentTransaksi']->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ================================================================
         TAB MINGGUAN (Mingguan: per hari dalam satu minggu)
    ================================================================ --}}
    <div x-show="tab==='mingguan'" x-cloak>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.laporan.index') }}"
              class="flex flex-wrap items-center gap-2 mb-5 bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-sm">
            <input type="hidden" name="tab" value="mingguan">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mr-2">Filter Minggu:</span>
            <a href="{{ route('admin.laporan.index', ['tab' => 'mingguan', 'tanggal' => $weekStart->copy()->subWeek()->toDateString()]) }}"
               class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-600 transition">← Minggu Lalu</a>
            <input type="date" name="tanggal" value="{{ $harian['tanggal'] }}" max="{{ now()->toDateString() }}"
                   onchange="this.form.submit()"
                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 cursor-pointer">
            @if(!$isThisWeek)
                <a href="{{ route('admin.laporan.index', ['tab' => 'mingguan', 'tanggal' => $weekStart->copy()->addWeek()->toDateString()]) }}"
                   class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-600 transition">Minggu Depan →</a>
            @endif
        </form>

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <x-card title="Total Transaksi"  value="{{ number_format($harian['totalPenjualan']) }}"                          type="total" />
            <x-card title="Total Pendapatan" value="Rp {{ number_format($harian['totalPendapatan'], 0, ',', '.') }}"         type="aktif" />
            <x-card title="Total Pelanggan"  value="{{ number_format($harian['totalPelanggan']) }}"                          type="total" />
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start pb-3 mb-3 border-b border-gray-100">
                    <div>
                        <p class="text-lg font-bold text-gray-800">Rp {{ number_format($harian['totalPendapatan'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $weekStart->translatedFormat('d M') }} – {{ $weekEnd->translatedFormat('d M Y') }}
                        </p>
                    </div>
                    <div class="flex gap-3 text-[10px] text-gray-400">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-teal-700 inline-block"></span>Pendapatan</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full border-2 border-teal-400 inline-block"></span>Transaksi</span>
                    </div>
                </div>
                <div id="chart-harian" class="min-h-[200px]"></div>
                <p class="text-[10px] text-gray-400 mt-2 pt-2 border-t border-gray-100">Grafik Per Hari — {{ $weekStart->translatedFormat('d M') }} s/d {{ $weekEnd->translatedFormat('d M Y') }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest mb-0.5">Metode Pembayaran</h3>
                <p class="text-[10px] text-gray-400 mb-3">{{ $weekStart->translatedFormat('d M') }} – {{ $weekEnd->translatedFormat('d M Y') }}</p>
                <div id="donut-harian"></div>
                @php $qH = $harian['metodePembayaran']['QRIS'] ?? 0; $cH = $harian['metodePembayaran']['Cash'] ?? 0; @endphp
                <div class="space-y-2 mt-auto">
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-teal-50/50 border border-teal-100/50">
                        <span class="flex items-center gap-2 text-xs font-bold text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-teal-700"></span>QRIS</span>
                        <span class="text-xs font-black text-gray-800">{{ $harian['qrisPct'] }}% <span class="font-normal text-gray-400">({{ $qH }})</span></span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-green-50/50 border border-green-100/50">
                        <span class="flex items-center gap-2 text-xs font-bold text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-teal-400"></span>Cash</span>
                        <span class="text-xs font-black text-gray-800">{{ $harian['cashPct'] }}% <span class="font-normal text-gray-400">({{ $cH }})</span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest">Transaksi Terbaru Minggu Ini</h3>
                <span class="text-[10px] text-gray-400">{{ $weekStart->translatedFormat('d M') }} – {{ $weekEnd->translatedFormat('d M Y') }}</span>
            </div>
            <x-table>
                <x-slot name="header">
                    <tr>
                        <th class="p-4 text-center w-10 text-xs">No</th>
                        <th class="p-4 text-left text-xs">Hari & Waktu</th>
                        <th class="p-4 text-left text-xs">Pelanggan</th>
                        <th class="p-4 text-left text-xs">Total</th>
                        <th class="p-4 text-center text-xs">Metode</th>
                        <th class="p-4 text-center text-xs">Status</th>
                        <th class="p-4 text-left text-xs">Kasir Cabang</th>
                    </tr>
                </x-slot>
                @forelse($harian['recentTransaksi'] as  $i => $trx)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="p-4 text-center text-gray-400 text-xs">{{ $harian['recentTransaksi']->firstItem() + $i }}</td>
                    <td class="p-4 text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($trx->created_at)->translatedFormat('D, d M H:i') }}</td>
                    <td class="p-4 text-sm text-gray-800">{{ $trx->nama_pelanggan ?? '-' }}</td>
                    <td class="p-4 text-sm font-black text-gray-800">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                    <td class="p-4 text-center">
                        @if(strtolower($trx->metode_pembayaran ?? '') === 'qris')
                            <span class="px-2 py-1 rounded-lg bg-teal-50 text-teal-700 text-[10px] font-bold border border-teal-100">QRIS</span>
                        @else
                            <span class="px-2 py-1 rounded-lg bg-yellow-50 text-yellow-700 text-[10px] font-bold border border-yellow-100">Cash</span>
                        @endif
                    </td>
                    <td class="p-4 text-center"><x-status-badge :active="($trx->status_transaksi ?? '') === 'berhasil'" /></td>
                    <td class="p-4 text-xs font-semibold text-gray-700">
                        <span class="font-bold text-gray-800">{{ $trx->nama_kasir ?? 'Kasir' }}</span>
                        <span class="text-teal-700 block text-[10px]">({{ $trx->nama_cabang ?? 'Pusat' }})</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-12 text-center text-sm text-gray-400"><i class="fa-solid fa-inbox text-2xl mb-2 block"></i>Belum ada transaksi minggu ini.</td></tr>
                @endforelse
            </x-table>
            @if($harian['recentTransaksi']->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $harian['recentTransaksi']->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ================================================================
         TAB BULANAN (Per Tahun: dikelompokkan per bulan Jan-Des)
    ================================================================ --}}
    <div x-show="tab==='bulanan'" x-cloak>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.laporan.index') }}"
              class="flex flex-wrap items-center gap-2 mb-5 bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-sm">
            <input type="hidden" name="tab" value="bulanan">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mr-2">Filter Tahun:</span>
            <a href="{{ route('admin.laporan.index', ['tab' => 'bulanan', 'tahun_bulanan' => $tahunBulanan - 1]) }}"
               class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-600 transition">← Tahun Lalu</a>
            <select name="tahun_bulanan" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 cursor-pointer">
                @for($y = intval(now()->format('Y')); $y >= intval(now()->format('Y')) - 5; $y--)
                    <option value="{{ $y }}" {{ $tahunBulanan === $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endfor
            </select>
            @if(!$isThisYear)
                <a href="{{ route('admin.laporan.index', ['tab' => 'bulanan', 'tahun_bulanan' => $tahunBulanan + 1]) }}"
                   class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-600 transition">Tahun Depan →</a>
            @endif
        </form>

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <x-card title="Total Transaksi"  value="{{ number_format($bulanan['totalPenjualan']) }}"                          type="total" />
            <x-card title="Total Pendapatan" value="Rp {{ number_format($bulanan['totalPendapatan'], 0, ',', '.') }}"         type="aktif" />
            <x-card title="Total Pelanggan"  value="{{ number_format($bulanan['totalPelanggan']) }}"                          type="total" />
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start pb-3 mb-3 border-b border-gray-100">
                    <div>
                        <p class="text-lg font-bold text-gray-800">Rp {{ number_format($bulanan['totalPendapatan'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Tahun {{ $tahunBulanan }}</p>
                    </div>
                    <div class="flex gap-3 text-[10px] text-gray-400">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-teal-700 inline-block"></span>Pendapatan</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full border-2 border-teal-400 inline-block"></span>Transaksi</span>
                    </div>
                </div>
                <div id="chart-bulanan" class="min-h-[200px]"></div>
                <p class="text-[10px] text-gray-400 mt-2 pt-2 border-t border-gray-100">Grafik Per Bulan — Tahun {{ $tahunBulanan }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest mb-0.5">Metode Pembayaran</h3>
                <p class="text-[10px] text-gray-400 mb-3">Tahun {{ $tahunBulanan }}</p>
                <div id="donut-bulanan"></div>
                @php $qB = $bulanan['metodePembayaran']['QRIS'] ?? 0; $cB = $bulanan['metodePembayaran']['Cash'] ?? 0; @endphp
                <div class="space-y-2 mt-auto">
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-teal-50/50 border border-teal-100/50">
                        <span class="flex items-center gap-2 text-xs font-bold text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-teal-700"></span>QRIS</span>
                        <span class="text-xs font-black text-gray-800">{{ $bulanan['qrisPct'] }}% <span class="font-normal text-gray-400">({{ $qB }})</span></span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-green-50/50 border border-green-100/50">
                        <span class="flex items-center gap-2 text-xs font-bold text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-teal-400"></span>Cash</span>
                        <span class="text-xs font-black text-gray-800">{{ $bulanan['cashPct'] }}% <span class="font-normal text-gray-400">({{ $cB }})</span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest">Transaksi Terbaru</h3>
                <span class="text-[10px] text-gray-400">Tahun {{ $tahunBulanan }}</span>
            </div>
            <x-table>
                <x-slot name="header">
                    <tr>
                        <th class="p-4 text-center w-10 text-xs">No</th>
                        <th class="p-4 text-left text-xs">Tanggal & Waktu</th>
                        <th class="p-4 text-left text-xs">Pelanggan</th>
                        <th class="p-4 text-left text-xs">Total</th>
                        <th class="p-4 text-center text-xs">Metode</th>
                        <th class="p-4 text-center text-xs">Status</th>
                        <th class="p-4 text-left text-xs">Kasir Cabang</th>
                    </tr>
                </x-slot>
                @forelse($bulanan['recentTransaksi'] as + $i => $trx)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="p-4 text-center text-gray-400 text-xs">{{ $bulanan['recentTransaksi']->firstItem() + $i }}</td>
                    <td class="p-4 text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($trx->created_at)->translatedFormat('d M Y, H:i') }}</td>
                    <td class="p-4 text-sm text-gray-800">{{ $trx->nama_pelanggan ?? '-' }}</td>
                    <td class="p-4 text-sm font-black text-gray-800">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                    <td class="p-4 text-center">
                        @if(strtolower($trx->metode_pembayaran ?? '') === 'qris')
                            <span class="px-2 py-1 rounded-lg bg-teal-50 text-teal-700 text-[10px] font-bold border border-teal-100">QRIS</span>
                        @else
                            <span class="px-2 py-1 rounded-lg bg-yellow-50 text-yellow-700 text-[10px] font-bold border border-yellow-100">Cash</span>
                        @endif
                    </td>
                    <td class="p-4 text-center"><x-status-badge :active="($trx->status_transaksi ?? '') === 'berhasil'" /></td>
                    <td class="p-4 text-xs font-semibold text-gray-700">
                        <span class="font-bold text-gray-800">{{ $trx->nama_kasir ?? 'Kasir' }}</span>
                        <span class="text-teal-700 block text-[10px]">({{ $trx->nama_cabang ?? 'Pusat' }})</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-12 text-center text-sm text-gray-400"><i class="fa-solid fa-inbox text-2xl mb-2 block"></i>Belum ada transaksi tahun {{ $tahunBulanan }}.</td></tr>
                @endforelse
            </x-table>
            @if($bulanan['recentTransaksi']->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $bulanan['recentTransaksi']->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ================================================================
         TAB TAHUNAN (Lintas tahun: per tahun untuk 5 tahun terakhir)
    ================================================================ --}}
    <div x-show="tab==='tahunan'" x-cloak>

        {{-- Info --}}
        <div class="flex flex-wrap items-center gap-2 mb-5 bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-sm">
            <i class="fa-solid fa-chart-line text-teal-600 text-sm"></i>
            <span class="text-xs font-bold text-gray-500">Menampilkan data <span class="text-teal-700">{{ $tahunMulai }} – {{ $tahunSekarang }}</span> ({{ $tahunSekarang - $tahunMulai + 1 }} tahun terakhir)</span>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <x-card title="Total Transaksi"  value="{{ number_format($tahunan['totalPenjualan']) }}"                          type="total" />
            <x-card title="Total Pendapatan" value="Rp {{ number_format($tahunan['totalPendapatan'], 0, ',', '.') }}"         type="aktif" />
            <x-card title="Total Pelanggan"  value="{{ number_format($tahunan['totalPelanggan']) }}"                          type="total" />
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start pb-3 mb-3 border-b border-gray-100">
                    <div>
                        <p class="text-lg font-bold text-gray-800">Rp {{ number_format($tahunan['totalPendapatan'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $tahunMulai }} – {{ $tahunSekarang }}</p>
                    </div>
                    <div class="flex gap-3 text-[10px] text-gray-400">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-teal-700 inline-block"></span>Pendapatan</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full border-2 border-teal-400 inline-block"></span>Transaksi</span>
                    </div>
                </div>
                <div id="chart-tahunan" class="min-h-[200px]"></div>
                <p class="text-[10px] text-gray-400 mt-2 pt-2 border-t border-gray-100">Grafik Per Tahun — {{ $tahunMulai }} s/d {{ $tahunSekarang }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest mb-0.5">Metode Pembayaran</h3>
                <p class="text-[10px] text-gray-400 mb-3">{{ $tahunMulai }} – {{ $tahunSekarang }}</p>
                <div id="donut-tahunan"></div>
                @php $qT = $tahunan['metodePembayaran']['QRIS'] ?? 0; $cT = $tahunan['metodePembayaran']['Cash'] ?? 0; @endphp
                <div class="space-y-2 mt-auto">
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-teal-50/50 border border-teal-100/50">
                        <span class="flex items-center gap-2 text-xs font-bold text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-teal-700"></span>QRIS</span>
                        <span class="text-xs font-black text-gray-800">{{ $tahunan['qrisPct'] }}% <span class="font-normal text-gray-400">({{ $qT }})</span></span>
                    </div>
                    <div class="flex justify-between items-center p-2.5 rounded-xl bg-green-50/50 border border-green-100/50">
                        <span class="flex items-center gap-2 text-xs font-bold text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-teal-400"></span>Cash</span>
                        <span class="text-xs font-black text-gray-800">{{ $tahunan['cashPct'] }}% <span class="font-normal text-gray-400">({{ $cT }})</span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest">Transaksi Terbaru</h3>
                <span class="text-[10px] text-gray-400">{{ $tahunMulai }} – {{ $tahunSekarang }}</span>
            </div>
            <x-table>
                <x-slot name="header">
                    <tr>
                        <th class="p-4 text-center w-10 text-xs">No</th>
                        <th class="p-4 text-left text-xs">Tanggal & Waktu</th>
                        <th class="p-4 text-left text-xs">Pelanggan</th>
                        <th class="p-4 text-left text-xs">Total</th>
                        <th class="p-4 text-center text-xs">Metode</th>
                        <th class="p-4 text-center text-xs">Status</th>
                        <th class="p-4 text-left text-xs">Kasir Cabang</th>
                    </tr>
                </x-slot>
                @forelse($tahunan['recentTransaksi'] as $i => $trx)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="p-4 text-center text-gray-400 text-xs">{{ $tahunan['recentTransaksi']_>firstItem()+ $1 }}</td>
                    <td class="p-4 text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($trx->created_at)->translatedFormat('d M Y, H:i') }}</td>
                    <td class="p-4 text-sm text-gray-800">{{ $trx->nama_pelanggan ?? '-' }}</td>
                    <td class="p-4 text-sm font-black text-gray-800">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                    <td class="p-4 text-center">
                        @if(strtolower($trx->metode_pembayaran ?? '') === 'qris')
                            <span class="px-2 py-1 rounded-lg bg-teal-50 text-teal-700 text-[10px] font-bold border border-teal-100">QRIS</span>
                        @else
                            <span class="px-2 py-1 rounded-lg bg-yellow-50 text-yellow-700 text-[10px] font-bold border border-yellow-100">Cash</span>
                        @endif
                    </td>
                    <td class="p-4 text-center"><x-status-badge :active="($trx->status_transaksi ?? '') === 'berhasil'" /></td>
                    <td class="p-4 text-xs font-semibold text-gray-700">
                        <span class="font-bold text-gray-800">{{ $trx->nama_kasir ?? 'Kasir' }}</span>
                        <span class="text-teal-700 block text-[10px]">({{ $trx->nama_cabang ?? 'Pusat' }})</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-12 text-center text-sm text-gray-400"><i class="fa-solid fa-inbox text-2xl mb-2 block"></i>Belum ada transaksi dalam 5 tahun terakhir.</td></tr>
                @endforelse
            </x-table>
            @if($tahunan['recentTransaksi']->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $tahunan['recentTransaksi']->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ================================================================
     APEXCHARTS
================================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function () {
    const COLORS = { bar: '#0d946095', line: '#118674fd', donut1: '#0d9488', donut2: '#5eead4' };

    function barLine(elId, cats, barData, lineData, xTitle) {
        var el = document.getElementById(elId);
        if (!el) return;
        new ApexCharts(el, {
            series: [
                { name: 'Pendapatan', type: 'bar',  data: barData  },
                { name: 'Transaksi',  type: 'line', data: lineData },
            ],
            chart: { height: 220, type: 'line', toolbar: { show: false }, fontFamily: 'inherit',
                     animations: { enabled: true, speed: 400 } },
            colors: [COLORS.bar, COLORS.line],
            stroke: { width: [0, 2], curve: 'smooth' },
            markers: { size: [0, 3], colors: ['#fff'], strokeColors: COLORS.line, strokeWidth: 2 },
            plotOptions: { bar: { columnWidth: '55%', borderRadius: 3, borderRadiusApplication: 'end' } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: cats,
                title: { text: xTitle, style: { fontSize: '10px', color: '#9CA3AF', fontWeight: 500 } },
                labels: { style: { fontSize: '10px', colors: '#9CA3AF' } },
                axisBorder: { show: false }, axisTicks: { show: false },
            },
            yaxis: [
                { labels: { formatter: function(v){ return v>=1e6?'Rp'+(v/1e6).toFixed(1)+'M':v>=1e3?'Rp'+(v/1e3).toFixed(0)+'K':'Rp'+v; }, style:{fontSize:'10px',colors:'#9CA3AF'} } },
                { opposite: true, min: 0, forceNiceScale: true,
                  labels: { formatter: function(v){ return Number.isInteger(v)?v+'x':''; }, style:{fontSize:'10px',colors:'#9CA3AF'} } },
            ],
            tooltip: { shared: true, intersect: false,
                x: { formatter: function(val){ return xTitle + ': ' + val; } },
                y: [{ formatter: function(v){ return 'Rp '+Number(v).toLocaleString('id-ID'); } },
                    { formatter: function(v){ return v+' transaksi'; } }] },
            legend: { show: false },
            grid: { borderColor: '#F3F4F6', strokeDashArray: 4, padding: { left: 4, right: 4 } },
        }).render();
    }

    function donut(elId, qrisPct, cashPct, qrisCount, cashCount) {
        var el = document.getElementById(elId);
        if (!el) return;
        var total = qrisCount + cashCount;
        new ApexCharts(el, {
            series: total === 0 ? [1] : [qrisPct || 0.01, cashPct || 0.01],
            chart: { type: 'donut', height: 180, toolbar: { show: false }, fontFamily: 'inherit' },
            colors: total === 0 ? ['#E5E7EB'] : [COLORS.donut1, COLORS.donut2],
            labels: total === 0 ? ['Belum ada data'] : ['QRIS', 'Cash'],
            plotOptions: { pie: { donut: { size: '68%', labels: { show: true,
                name:  { show: true, fontSize: '10px', color: '#9CA3AF' },
                value: { show: true, fontSize: '18px', fontWeight: 700, color: '#111827',
                         formatter: function(v){ return total===0?'0':Math.round(v)+'%'; } },
                total: { show: true, label: 'Transaksi', fontSize: '10px', color: '#9CA3AF',
                         formatter: function(){ return total; } },
            }}}},
            dataLabels: { enabled: false },
            legend: { show: false },
            stroke: { width: 2, colors: ['#fff'] },
            tooltip: { enabled: total > 0,
                y: { formatter: function(v, opts){
                    return [qrisCount, cashCount][opts.seriesIndex]+' transaksi ('+Math.round(v)+'%)';
                }}},
        }).render();
    }

    // ── HARIAN (Mingguan): X-axis = Nama Hari (Sen, Sel, ..., Min) ───────
    // MySQL DAYOFWEEK: 1=Minggu, 2=Senin, 3=Selasa, 4=Rabu, 5=Kamis, 6=Jumat, 7=Sabtu
    // Kita mapping ke index 0=Senin..6=Minggu
    var rawH = @json($harian['grafikData']);
    var mapH = {};
    rawH.forEach(function(d){
        // dow: 2=Sen,3=Sel,4=Rab,5=Kam,6=Jum,7=Sab,1=Min → index 0..6
        var idx = d.dow === 1 ? 6 : d.dow - 2;
        mapH[idx] = d;
    });
    var dayLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    barLine('chart-harian',
        dayLabels,
        dayLabels.map(function(_, i){ return parseFloat((mapH[i]||{}).total_pendapatan||0); }),
        dayLabels.map(function(_, i){ return parseInt((mapH[i]||{}).total_transaksi||0); }),
        'Hari'
    );
    donut('donut-harian',
        {{ $harian['qrisPct'] }}, {{ $harian['cashPct'] }},
        {{ $harian['metodePembayaran']['QRIS'] ?? 0 }},
        {{ $harian['metodePembayaran']['Cash'] ?? 0 }}
    );

    // ── BULANAN: X-axis = Nama Bulan (Jan, Feb, ..., Des) ────────────────
    var rawB = @json($bulanan['grafikData']);
    var mapB = {};
    rawB.forEach(function(d){ mapB[parseInt(d.bulan)] = d; });
    var monthLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    var monthsArr   = Array.from({length:12}, function(_,i){ return i+1; });
    barLine('chart-bulanan',
        monthLabels,
        monthsArr.map(function(m){ return parseFloat((mapB[m]||{}).total_pendapatan||0); }),
        monthsArr.map(function(m){ return parseInt((mapB[m]||{}).total_transaksi||0); }),
        'Bulan'
    );
    donut('donut-bulanan',
        {{ $bulanan['qrisPct'] }}, {{ $bulanan['cashPct'] }},
        {{ $bulanan['metodePembayaran']['QRIS'] ?? 0 }},
        {{ $bulanan['metodePembayaran']['Cash'] ?? 0 }}
    );

    // ── TAHUNAN: X-axis = Tahun (2021, 2022, ..., 2025) ─────────────────
    var rawT = @json($tahunan['grafikData']);
    var mapT = {};
    rawT.forEach(function(d){ mapT[parseInt(d.tahun)] = d; });
    var tahunMulai = {{ $tahunMulai }};
    var tahunAkhir = {{ $tahunSekarang }};
    var yearLabels = [];
    for(var y = tahunMulai; y <= tahunAkhir; y++) yearLabels.push(String(y));
    barLine('chart-tahunan',
        yearLabels,
        yearLabels.map(function(y){ return parseFloat((mapT[parseInt(y)]||{}).total_pendapatan||0); }),
        yearLabels.map(function(y){ return parseInt((mapT[parseInt(y)]||{}).total_transaksi||0); }),
        'Tahun'
    );
    donut('donut-tahunan',
        {{ $tahunan['qrisPct'] }}, {{ $tahunan['cashPct'] }},
        {{ $tahunan['metodePembayaran']['QRIS'] ?? 0 }},
        {{ $tahunan['metodePembayaran']['Cash'] ?? 0 }}
    );
})();
</script>
@endsection