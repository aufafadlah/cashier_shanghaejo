@extends('layouts.admin')
@section('title', 'Laporan Pengeluaran')

@section('content')
@php
    // ── Harian helpers ──────────────────────────────────────────────
    $tglC       = \Carbon\Carbon::parse($harian['tanggal']);
    $isToday    = $harian['tanggal'] === now()->toDateString();

    // ── Bulanan helpers ─────────────────────────────────────────────
    $mc         = $bulanan['monthCarbon'];
    $isThisMonth = $bulanan['bulanStr'] === now()->format('Y-m');

    // ── Tahunan helpers ─────────────────────────────────────────────
    $tahun      = $tahunan['tahun'];
    $isThisYear = $tahun === intval(now()->format('Y'));

    // ── Query-string helpers ─────────────────────────────────────────
    $qsC = $id_cabang ? ['id_cabang' => $id_cabang] : [];

    // ── Initial tab helper ──────────────────────────────────────────
    $initialTab = request('tab', 'harian');
    if (!request()->has('tab')) {
        if (request()->has('bulan')) $initialTab = 'bulanan';
        elseif (request()->has('tahun')) $initialTab = 'tahunan';
    }
@endphp

<div class="p-2" x-data="{ tab: '{{ $initialTab }}' }">

    {{-- PAGE TITLE --}}
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Laporan Pengeluaran</h1>
            <p class="text-xs text-gray-400 mt-0.5">Analisis performa pengeluaran bahan harian, bulanan &amp; tahunan.</p>
        </div>
    </div>

    {{-- TAB NAVIGATION --}}
    <div class="flex gap-1 mb-6 bg-gray-100 p-1 rounded-2xl w-fit shadow-inner">
        <button @click="tab='harian'"
            :class="tab==='harian' ? 'bg-white text-teal-700 shadow' : 'text-gray-500 hover:text-gray-700'"
            class="px-5 py-2 text-sm font-bold rounded-xl transition-all duration-200 flex items-center gap-2">
            <i class="fa-solid fa-calendar-day text-xs"></i> Harian
        </button>
        <button @click="tab='bulanan'"
            :class="tab==='bulanan' ? 'bg-white text-teal-700 shadow' : 'text-gray-500 hover:text-gray-700'"
            class="px-5 py-2 text-sm font-bold rounded-xl transition-all duration-200 flex items-center gap-2">
            <i class="fa-solid fa-calendar-week text-xs"></i> Bulanan
        </button>
        <button @click="tab='tahunan'"
            :class="tab==='tahunan' ? 'bg-white text-teal-700 shadow' : 'text-gray-500 hover:text-gray-700'"
            class="px-5 py-2 text-sm font-bold rounded-xl transition-all duration-200 flex items-center gap-2">
            <i class="fa-solid fa-calendar text-xs"></i> Tahunan
        </button>
    </div>

    {{-- ================================================================
         TAB HARIAN
    ================================================================ --}}
    <div x-show="tab==='harian'" x-cloak>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.laporanpembelian.index') }}"
              class="flex flex-wrap items-center gap-2 mb-5 bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-sm">
            <input type="hidden" name="tab" value="harian">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mr-2">Filter Harian:</span>
            <a href="{{ route('admin.laporanpembelian.index', array_merge($qsC, ['tab' => 'harian', 'tanggal' => $tglC->copy()->subDay()->toDateString()])) }}"
               class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-600 transition">← Kemarin</a>
            <input type="date" name="tanggal" value="{{ $harian['tanggal'] }}" max="{{ now()->toDateString() }}"
                   onchange="this.form.submit()"
                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 cursor-pointer">
            @if(!$isToday)
                <a href="{{ route('admin.laporanpembelian.index', array_merge($qsC, ['tab' => 'harian', 'tanggal' => $tglC->copy()->addDay()->toDateString()])) }}"
                   class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-600 transition">Besok →</a>
            @endif
        </form>

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <x-card title="Total Transaksi"   value="{{ number_format($harian['totalTransaksi']) }}"                           type="total" />
            <x-card title="Total Pengeluaran" value="Rp {{ number_format($harian['totalPengeluaran'], 0, ',', '.') }}"         type="non-aktif" />
            <x-card title="Total Item Barang" value="{{ number_format($harian['totalItem']) }}"                               type="total" />
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start pb-3 mb-3 border-b border-gray-100">
                    <div>
                        <p class="text-lg font-bold text-gray-800">Rp {{ number_format($harian['totalPengeluaran'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $tglC->translatedFormat('d F Y') }}</p>
                    </div>
                    <div class="flex gap-3 text-[10px] text-gray-400">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-teal-700 inline-block"></span>Pengeluaran</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full border-2 border-teal-400 inline-block"></span>Transaksi</span>
                    </div>
                </div>
                <div id="chart-harian" class="min-h-[200px]"></div>
                <p class="text-[10px] text-gray-400 mt-2 pt-2 border-t border-gray-100">Grafik Pengeluaran per Cabang — {{ $tglC->format('d/m/Y') }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest mb-0.5">Proporsi per Cabang</h3>
                <p class="text-[10px] text-gray-400 mb-3">{{ $tglC->translatedFormat('d F Y') }}</p>
                <div id="donut-harian" class="my-auto"></div>
                <div class="space-y-2 mt-auto">
                    @forelse($harian['cabangBreakdown'] as $idx => $cb)
                        <div class="flex justify-between items-center p-2 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="flex items-center gap-2 text-xs font-bold text-gray-600">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ ['#0d9488', '#5eead4', '#2dd4bf', '#14b8a6', '#0f766e'][$idx % 5] }}"></span>
                                {{ $cb->nama_cabang }}
                            </span>
                            <span class="text-xs font-black text-gray-800">
                                Rp {{ number_format($cb->total, 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-gray-400 italic">Belum ada data pengeluaran</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest">Daftar Pengeluaran Hari Ini</h3>
                <span class="text-[10px] text-gray-400">{{ $tglC->translatedFormat('d F Y') }}</span>
            </div>
            <x-table>
                <x-slot name="header">
                    <tr>
                        <th class="p-4 text-center w-10 text-xs">No</th>
                        <th class="p-4 text-left text-xs">Cabang</th>
                        <th class="p-4 text-left text-xs">Bahan / Barang</th>
                        <th class="p-4 text-center text-xs">Qty</th>
                        <th class="p-4 text-right text-xs">Harga Satuan</th>
                        <th class="p-4 text-right text-xs">Total</th>
                        <th class="p-4 text-left text-xs">Supplier</th>
                        <th class="p-4 text-left text-xs">Keterangan</th>
                    </tr>
                </x-slot>
                @forelse($harian['recentTransaksi'] as $i => $trx)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="p-4 text-center text-gray-400 text-xs">{{ $i+1 }}</td>
                    <td class="p-4 text-sm font-bold text-gray-700">{{ $trx->nama_cabang }}</td>
                    <td class="p-4 text-sm text-gray-800">{{ $trx->nama_barang }}</td>
                    <td class="p-4 text-center text-sm font-bold text-gray-700">{{ number_format($trx->qty) }}</td>
                    <td class="p-4 text-right text-sm text-gray-800">Rp {{ number_format($trx->harga_satuan, 0, ',', '.') }}</td>
                    <td class="p-4 text-right text-sm font-black text-gray-800">Rp {{ number_format($trx->harga_total, 0, ',', '.') }}</td>
                    <td class="p-4 text-sm text-gray-600">{{ $trx->supplier ?? '-' }}</td>
                    <td class="p-4 text-xs text-gray-400 max-w-[200px] truncate">{{ $trx->keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="p-12 text-center text-sm text-gray-400"><i class="fa-solid fa-inbox text-2xl mb-2 block"></i>Belum ada pengeluaran hari ini.</td></tr>
                @endforelse
            </x-table>
        </div>
    </div>

    {{-- ================================================================
         TAB BULANAN
    ================================================================ --}}
    <div x-show="tab==='bulanan'" x-cloak>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.laporanpembelian.index') }}"
              class="flex flex-wrap items-center gap-2 mb-5 bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-sm">
            <input type="hidden" name="tab" value="bulanan">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mr-2">Filter Bulanan:</span>
            <a href="{{ route('admin.laporanpembelian.index', array_merge($qsC, ['tab' => 'bulanan', 'bulan' => $mc->copy()->subMonth()->format('Y-m')])) }}"
               class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-600 transition">← Bulan Lalu</a>
            <input type="month" name="bulan" value="{{ $bulanan['bulanStr'] }}" max="{{ now()->format('Y-m') }}"
                   onchange="this.form.submit()"
                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 cursor-pointer">
            @if(!$isThisMonth)
                <a href="{{ route('admin.laporanpembelian.index', array_merge($qsC, ['tab' => 'bulanan', 'bulan' => $mc->copy()->addMonth()->format('Y-m')])) }}"
                   class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-600 transition">Bulan Depan →</a>
            @endif
        </form>

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <x-card title="Total Transaksi"   value="{{ number_format($bulanan['totalTransaksi']) }}"                           type="total" />
            <x-card title="Total Pengeluaran" value="Rp {{ number_format($bulanan['totalPengeluaran'], 0, ',', '.') }}"         type="non-aktif" />
            <x-card title="Total Item Barang" value="{{ number_format($bulanan['totalItem']) }}"                               type="total" />
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start pb-3 mb-3 border-b border-gray-100">
                    <div>
                        <p class="text-lg font-bold text-gray-800">Rp {{ number_format($bulanan['totalPengeluaran'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $mc->translatedFormat('F Y') }}</p>
                    </div>
                    <div class="flex gap-3 text-[10px] text-gray-400">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-teal-700 inline-block"></span>Pengeluaran</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full border-2 border-teal-400 inline-block"></span>Transaksi</span>
                    </div>
                </div>
                <div id="chart-bulanan" class="min-h-[200px]"></div>
                <p class="text-[10px] text-gray-400 mt-2 pt-2 border-t border-gray-100">Grafik Per Hari — {{ $mc->translatedFormat('F Y') }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest mb-0.5">Proporsi per Cabang</h3>
                <p class="text-[10px] text-gray-400 mb-3">{{ $mc->translatedFormat('F Y') }}</p>
                <div id="donut-bulanan" class="my-auto"></div>
                <div class="space-y-2 mt-auto">
                    @forelse($bulanan['cabangBreakdown'] as $idx => $cb)
                        <div class="flex justify-between items-center p-2 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="flex items-center gap-2 text-xs font-bold text-gray-600">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ ['#0d9488', '#5eead4', '#2dd4bf', '#14b8a6', '#0f766e'][$idx % 5] }}"></span>
                                {{ $cb->nama_cabang }}
                            </span>
                            <span class="text-xs font-black text-gray-800">
                                Rp {{ number_format($cb->total, 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-gray-400 italic">Belum ada data pengeluaran</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest">Transaksi Pengeluaran Terbaru</h3>
                <span class="text-[10px] text-gray-400">{{ $mc->translatedFormat('F Y') }}</span>
            </div>
            <x-table>
                <x-slot name="header">
                    <tr>
                        <th class="p-4 text-center w-10 text-xs">No</th>
                        <th class="p-4 text-left text-xs">Tanggal</th>
                        <th class="p-4 text-left text-xs">Cabang</th>
                        <th class="p-4 text-left text-xs">Bahan / Barang</th>
                        <th class="p-4 text-center text-xs">Qty</th>
                        <th class="p-4 text-right text-xs">Harga Satuan</th>
                        <th class="p-4 text-right text-xs">Total</th>
                        <th class="p-4 text-left text-xs">Supplier</th>
                    </tr>
                </x-slot>
                @forelse($bulanan['recentTransaksi'] as $i => $trx)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="p-4 text-center text-gray-400 text-xs">{{ $i+1 }}</td>
                    <td class="p-4 text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($trx->tgl)->translatedFormat('d M Y') }}</td>
                    <td class="p-4 text-sm text-gray-800">{{ $trx->nama_cabang }}</td>
                    <td class="p-4 text-sm text-gray-800">{{ $trx->nama_barang }}</td>
                    <td class="p-4 text-center text-sm font-bold text-gray-700">{{ number_format($trx->qty) }}</td>
                    <td class="p-4 text-right text-sm text-gray-800">Rp {{ number_format($trx->harga_satuan, 0, ',', '.') }}</td>
                    <td class="p-4 text-right text-sm font-black text-gray-800">Rp {{ number_format($trx->harga_total, 0, ',', '.') }}</td>
                    <td class="p-4 text-sm text-gray-600">{{ $trx->supplier ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="p-12 text-center text-sm text-gray-400"><i class="fa-solid fa-inbox text-2xl mb-2 block"></i>Belum ada pengeluaran bulan ini.</td></tr>
                @endforelse
            </x-table>
        </div>
    </div>

    {{-- ================================================================
         TAB TAHUNAN
    ================================================================ --}}
    <div x-show="tab==='tahunan'" x-cloak>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.laporanpembelian.index') }}"
              class="flex flex-wrap items-center gap-2 mb-5 bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-sm">
            <input type="hidden" name="tab" value="tahunan">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest mr-2">Filter Tahunan:</span>
            <a href="{{ route('admin.laporanpembelian.index', array_merge($qsC, ['tab' => 'tahunan', 'tahun' => $tahun - 1])) }}"
               class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-600 transition">← Tahun Lalu</a>
            <select name="tahun" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-teal-400 cursor-pointer">
                @for($y = intval(now()->format('Y')); $y >= intval(now()->format('Y')) - 5; $y--)
                    <option value="{{ $y }}" {{ $tahun === $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endfor
            </select>
            @if(!$isThisYear)
                <a href="{{ route('admin.laporanpembelian.index', array_merge($qsC, ['tab' => 'tahunan', 'tahun' => $tahun + 1])) }}"
                   class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-gray-600 transition">Tahun Depan →</a>
            @endif
        </form>

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
            <x-card title="Total Transaksi"   value="{{ number_format($tahunan['totalTransaksi']) }}"                           type="total" />
            <x-card title="Total Pengeluaran" value="Rp {{ number_format($tahunan['totalPengeluaran'], 0, ',', '.') }}"         type="non-aktif" />
            <x-card title="Total Item Barang" value="{{ number_format($tahunan['totalItem']) }}"                               type="total" />
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start pb-3 mb-3 border-b border-gray-100">
                    <div>
                        <p class="text-lg font-bold text-gray-800">Rp {{ number_format($tahunan['totalPengeluaran'], 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Tahun {{ $tahun }}</p>
                    </div>
                    <div class="flex gap-3 text-[10px] text-gray-400">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-teal-700 inline-block"></span>Pengeluaran</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full border-2 border-teal-400 inline-block"></span>Transaksi</span>
                    </div>
                </div>
                <div id="chart-tahunan" class="min-h-[200px]"></div>
                <p class="text-[10px] text-gray-400 mt-2 pt-2 border-t border-gray-100">Grafik Per Bulan — Tahun {{ $tahun }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest mb-0.5">Proporsi per Cabang</h3>
                <p class="text-[10px] text-gray-400 mb-3">Tahun {{ $tahun }}</p>
                <div id="donut-tahunan" class="my-auto"></div>
                <div class="space-y-2 mt-auto">
                    @forelse($tahunan['cabangBreakdown'] as $idx => $cb)
                        <div class="flex justify-between items-center p-2 rounded-xl bg-gray-50 border border-gray-100">
                            <span class="flex items-center gap-2 text-xs font-bold text-gray-600">
                                <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ ['#0d9488', '#5eead4', '#2dd4bf', '#14b8a6', '#0f766e'][$idx % 5] }}"></span>
                                {{ $cb->nama_cabang }}
                            </span>
                            <span class="text-xs font-black text-gray-800">
                                Rp {{ number_format($cb->total, 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-gray-400 italic">Belum ada data pengeluaran</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest">Transaksi Pengeluaran Terbaru</h3>
                <span class="text-[10px] text-gray-400">Tahun {{ $tahun }}</span>
            </div>
            <x-table>
                <x-slot name="header">
                    <tr>
                        <th class="p-4 text-center w-10 text-xs">No</th>
                        <th class="p-4 text-left text-xs">Tanggal</th>
                        <th class="p-4 text-left text-xs">Cabang</th>
                        <th class="p-4 text-left text-xs">Bahan / Barang</th>
                        <th class="p-4 text-center text-xs">Qty</th>
                        <th class="p-4 text-right text-xs">Harga Satuan</th>
                        <th class="p-4 text-right text-xs">Total</th>
                        <th class="p-4 text-left text-xs">Supplier</th>
                    </tr>
                </x-slot>
                @forelse($tahunan['recentTransaksi'] as $i => $trx)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="p-4 text-center text-gray-400 text-xs">{{ $i+1 }}</td>
                    <td class="p-4 text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($trx->tgl)->translatedFormat('d M Y') }}</td>
                    <td class="p-4 text-sm text-gray-800">{{ $trx->nama_cabang }}</td>
                    <td class="p-4 text-sm text-gray-800">{{ $trx->nama_barang }}</td>
                    <td class="p-4 text-center text-sm font-bold text-gray-700">{{ number_format($trx->qty) }}</td>
                    <td class="p-4 text-right text-sm text-gray-800">Rp {{ number_format($trx->harga_satuan, 0, ',', '.') }}</td>
                    <td class="p-4 text-right text-sm font-black text-gray-800">Rp {{ number_format($trx->harga_total, 0, ',', '.') }}</td>
                    <td class="p-4 text-sm text-gray-600">{{ $trx->supplier ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="p-12 text-center text-sm text-gray-400"><i class="fa-solid fa-inbox text-2xl mb-2 block"></i>Belum ada pengeluaran tahun ini.</td></tr>
                @endforelse
            </x-table>
        </div>
    </div>

</div>

{{-- ================================================================
     APEXCHARTS — render semua chart sekaligus
================================================================ --}}
<script>
(function () {
    const COLORS = { 
        bar: '#ef4444c0', // Red for expenses (distinct from sales)
        line: '#f87171', 
        donut: ['#ef4444', '#f87171', '#fca5a5', '#fecaca', '#fee2e2'] 
    };

    function barLine(elId, cats, barData, lineData) {
        var el = document.getElementById(elId);
        if (!el) return;
        new ApexCharts(el, {
            series: [
                { name: 'Pengeluaran', type: 'bar',  data: barData  },
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
                labels: { style: { fontSize: '10px', colors: '#9CA3AF' } },
                axisBorder: { show: false }, axisTicks: { show: false },
            },
            yaxis: [
                { labels: { formatter: function(v){ return v>=1e6?'Rp'+(v/1e6).toFixed(1)+'M':v>=1e3?'Rp'+(v/1e3).toFixed(0)+'K':'Rp'+v; }, style:{fontSize:'10px',colors:'#9CA3AF'} } },
                { opposite: true, min: 0, forceNiceScale: true,
                  labels: { formatter: function(v){ return Number.isInteger(v)?v+'x':''; }, style:{fontSize:'10px',colors:'#9CA3AF'} } },
            ],
            tooltip: { shared: true, intersect: false,
                y: [{ formatter: function(v){ return 'Rp '+Number(v).toLocaleString('id-ID'); } },
                    { formatter: function(v){ return v+' transaksi'; } }] },
            legend: { show: false },
            grid: { borderColor: '#F3F4F6', strokeDashArray: 4, padding: { left: 4, right: 4 } },
        }).render();
    }

    function donut(elId, labels, data) {
        var el = document.getElementById(elId);
        if (!el) return;
        var total = data.reduce((a, b) => a + b, 0);
        new ApexCharts(el, {
            series: total === 0 ? [1] : data,
            chart: { type: 'donut', height: 180, toolbar: { show: false }, fontFamily: 'inherit' },
            colors: total === 0 ? ['#E5E7EB'] : COLORS.donut,
            labels: total === 0 ? ['Belum ada data'] : labels,
            plotOptions: { pie: { donut: { size: '68%', labels: { show: true,
                name:  { show: true, fontSize: '10px', color: '#9CA3AF' },
                value: { show: true, fontSize: '14px', fontWeight: 700, color: '#111827',
                         formatter: function(v){ return total===0?'0':'Rp '+Math.round(v).toLocaleString('id-ID'); } },
                total: { show: true, label: 'Total', fontSize: '10px', color: '#9CA3AF',
                         formatter: function(){ return 'Rp '+Math.round(total).toLocaleString('id-ID'); } },
            }}}},
            dataLabels: { enabled: false },
            legend: { show: false },
            stroke: { width: 2, colors: ['#fff'] },
            tooltip: { enabled: total > 0,
                y: { formatter: function(v){
                    return 'Rp '+Number(v).toLocaleString('id-ID')+' ('+Math.round(v/total*100)+'%)';
                }}},
        }).render();
    }

    // ── HARIAN ──────────────────────────────────────────────────────
    var rawH = @json($harian['grafikData']);
    var catsH = rawH.map(function(d){ return d.nama_cabang; });
    var barH = rawH.map(function(d){ return parseFloat(d.total_pengeluaran); });
    var lineH = rawH.map(function(d){ return parseInt(d.total_transaksi); });
    barLine('chart-harian', catsH, barH, lineH);

    var rawDonutH = @json($harian['cabangBreakdown']);
    donut('donut-harian', 
        rawDonutH.map(function(d){ return d.nama_cabang; }), 
        rawDonutH.map(function(d){ return parseFloat(d.total); })
    );

    // ── BULANAN ─────────────────────────────────────────────────────
    var rawB = @json($bulanan['grafikData']);
    var mapB = {};
    rawB.forEach(function(d){ mapB[parseInt(d.hari)] = d; });
    var days = {{ $bulanan['monthCarbon']->daysInMonth }};
    var arrB = Array.from({length:days}, function(_,i){ return i+1; });
    barLine('chart-bulanan',
        arrB.map(function(d){ return String(d).padStart(2,'0'); }),
        arrB.map(function(d){ return parseFloat((mapB[d]||{}).total_pengeluaran||0); }),
        arrB.map(function(d){ return parseInt((mapB[d]||{}).total_transaksi||0); })
    );

    var rawDonutB = @json($bulanan['cabangBreakdown']);
    donut('donut-bulanan', 
        rawDonutB.map(function(d){ return d.nama_cabang; }), 
        rawDonutB.map(function(d){ return parseFloat(d.total); })
    );

    // ── TAHUNAN ─────────────────────────────────────────────────────
    var rawT = @json($tahunan['grafikData']);
    var mapT = {};
    rawT.forEach(function(d){ mapT[parseInt(d.bulan)] = d; });
    var months = Array.from({length:12}, function(_,i){ return i+1; });
    barLine('chart-tahunan',
        ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
        months.map(function(m){ return parseFloat((mapT[m]||{}).total_pengeluaran||0); }),
        months.map(function(m){ return parseInt((mapT[m]||{}).total_transaksi||0); })
    );

    var rawDonutT = @json($tahunan['cabangBreakdown']);
    donut('donut-tahunan', 
        rawDonutT.map(function(d){ return d.nama_cabang; }), 
        rawDonutT.map(function(d){ return parseFloat(d.total); })
    );
})();
</script>
@endsection
