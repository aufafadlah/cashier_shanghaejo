@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-8 p-4">
    
    {{-- 1. Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-card title="Total Transaksi" value="{{ number_format($countTransaksi) }}" type="total" />
        <x-card title="Total Pendapatan (Omzet)" value="Rp {{ number_format($totalOmzet, 0, ',', '.') }}" type="aktif" />
        <x-card title="Total Pelanggan" value="{{ number_format($totalPelanggan) }}" type="total" />
        <x-card title="Total Pengeluaran" value="Rp {{ number_format($totalBeli, 0, ',', '.') }}" type="non-aktif" />
    </div>

    {{-- 2. Charts Section (Grafik Penjualan & Metode Pembayaran) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Grafik Penjualan (ApexCharts hourly dual axis) --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-start pb-3 mb-3 border-b border-gray-100">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Hari Ini</p>
                </div>
                <div class="flex gap-3 text-[10px] text-gray-400">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-teal-700 inline-block"></span>Pendapatan</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full border-2 border-teal-400 inline-block"></span>Transaksi</span>
                </div>
            </div>
            <div id="chart-harian" class="min-h-[220px]"></div>
            <p class="text-[10px] text-gray-400 mt-2 pt-2 border-t border-gray-100">Grafik Penjualan Per Hari — Minggu Ini</p>
        </div>

        {{-- Grafik Metode Pembayaran (ApexCharts donut) --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest mb-0.5">Metode Pembayaran</h3>
                <p class="text-[10px] text-gray-400 mb-3">Hari Ini</p>
            </div>
            <div id="donut-harian" class="my-auto"></div>
            @php 
                $qH = $metodePembayaran['QRIS'] ?? 0; 
                $cH = $metodePembayaran['Cash'] ?? 0; 
            @endphp
            <div class="space-y-2 mt-auto">
                <div class="flex justify-between items-center p-2.5 rounded-xl bg-teal-50/50 border border-teal-100/50">
                    <span class="flex items-center gap-2 text-xs font-bold text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-teal-700"></span>QRIS</span>
                    <span class="text-xs font-black text-gray-800">{{ $qrisPct }}% <span class="font-normal text-gray-400">({{ $qH }})</span></span>
                </div>
                <div class="flex justify-between items-center p-2.5 rounded-xl bg-green-50/50 border border-green-100/50">
                    <span class="flex items-center gap-2 text-xs font-bold text-gray-600"><span class="w-2.5 h-2.5 rounded-full bg-teal-400"></span>Cash</span>
                    <span class="text-xs font-black text-gray-800">{{ $cashPct }}% <span class="font-normal text-gray-400">({{ $cH }})</span></span>
                </div>
            </div>
        </div>
    </div>

    {{-- 2b. Distribusi Penjualan per Cabang (Hari Ini) --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest mb-0.5">Distribusi Penjualan per Cabang</h3>
                <p class="text-[10px] text-gray-400">Hari Ini{{ $selectedCabang ? ' — Cabang Terpilih' : ' — Semua Cabang' }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
            <div id="donut-cabang" class=""></div>
            <div class="space-y-2">
                @forelse($cabangBreakdown as $cb)
                @php
                    $totalCabangPct = $cabangBreakdown->sum('total') > 0 ? round(($cb->total / $cabangBreakdown->sum('total')) * 100) : 0;
                @endphp
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                    <span class="text-xs font-bold text-gray-700 truncate">{{ Str::title($cb->nama_cabang) }}</span>
                    <div class="text-right ml-2 shrink-0">
                        <span class="text-xs font-black text-gray-800">Rp {{ number_format($cb->total, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-gray-400 ml-1">({{ $totalCabangPct }}%)</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-sm text-gray-400 italic">Belum ada transaksi hari ini.</div>
                @endforelse
            </div>
        </div>
    </div>


    {{-- 3. Bottom Section (Tabel Transaksi Terbaru) --}}
    <div class="grid grid-cols-1 gap-8">
        
        {{-- Tabel Transaksi Terbaru --}}
        <div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full">
                <div class="p-5 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700 text-xs uppercase tracking-widest">Transaksi Terbaru</h3>
                    <a href="{{ route('admin.laporan.index') }}" class="text-[10px] font-bold text-teal-600 hover:opacity-70 transition-all uppercase tracking-wider">Lihat Lengkap →</a>
                </div>
                <x-table>
                    <x-slot name="header">
                        <tr>
                            <th class="p-4 text-center w-10 text-xs">No</th>
                            <th class="p-4 text-left text-xs">Waktu</th>
                            <th class="p-4 text-left text-xs">Pelanggan</th>
                            <th class="p-4 text-left text-xs">Total</th>
                            <th class="p-4 text-center text-xs">Metode</th>
                            <th class="p-4 text-center text-xs">Status</th>
                        </tr>
                    </x-slot>
                    @forelse($transaksi ?? [] as $i => $tr)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="p-4 text-center text-gray-400 text-xs">{{ $i+1 }}</td>
                        <td class="p-4 text-sm font-bold text-gray-700">
                            {{ \Carbon\Carbon::parse($tr->created_at)->format('H:i') }}
                        </td>
                        <td class="p-4 text-sm text-gray-800">{{ $tr->nama_pelanggan ?? '-' }}</td>
                        <td class="p-4 text-sm font-black text-gray-800">
                            Rp {{ number_format($tr->total_bayar, 0, ',', '.') }}
                        </td>
                        <td class="p-4 text-center">
                            @if(strtolower($tr->metode_pembayaran ?? '') === 'qris')
                                <span class="px-2 py-1 rounded-lg bg-teal-50 text-teal-700 text-[10px] font-bold border border-teal-100">QRIS</span>
                            @else
                                <span class="px-2 py-1 rounded-lg bg-yellow-50 text-yellow-700 text-[10px] font-bold border border-yellow-100">Cash</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <x-status-badge :active="($tr->status_transaksi ?? '') === 'berhasil'" />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-sm text-gray-400"><i class="fa-solid fa-inbox text-2xl mb-2 block"></i>Belum ada transaksi hari ini.</td>
                    </tr>
                    @endforelse
                </x-table>
            </div>
        </div>


        
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function () {
    const COLORS = { bar: '#0d9460c5', line: '#21b09aff', donut1: '#0d9488', donut2: '#5eead4' };

    function barLine(elId, cats, barData, lineData) {
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
                labels: { style: { fontSize: '10px', colors: '#9CA3AF' } },
                axisBorder: { show: false }, axisTicks: { show: false },
            },
            yaxis: [
                { labels: { formatter: function(v){ return v>=1e6?'Rp '+(v/1e6).toFixed(1)+'M':v>=1e3?'Rp '+(v/1e3).toFixed(0)+'K':'Rp '+v; }, style:{fontSize:'10px',colors:'#9CA3AF'} } },
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

    // ── HARIAN PENJUALAN (sekarang Mingguan) ──
    var rawH = @json($grafikData);
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
        dayLabels.map(function(_, i){ return parseInt((mapH[i]||{}).total_transaksi||0); })
    );

    donut('donut-harian',
        {{ $qrisPct }}, {{ $cashPct }},
        {{ $metodePembayaran['QRIS'] ?? 0 }},
        {{ $metodePembayaran['Cash'] ?? 0 }}
    );

    // ── DISTRIBUSI PER CABANG ──
    var cabangRaw = @json($cabangBreakdown);
    var cabangEl  = document.getElementById('donut-cabang');
    if (cabangEl && cabangRaw.length > 0) {
        var cabangLabels = cabangRaw.map(function(c){ return c.nama_cabang ? c.nama_cabang.charAt(0).toUpperCase() + c.nama_cabang.slice(1) : '?'; });
        var cabangValues = cabangRaw.map(function(c){ return parseFloat(c.total) || 0; });
        var palette = ['#0d9488','#5eead4','#0f766e','#99f6e4','#134e4a','#14b8a6'];
        var totalCabang = cabangValues.reduce(function(a,b){ return a+b; }, 0);
        new ApexCharts(cabangEl, {
            series: cabangValues,
            labels: cabangLabels,
            chart: { type: 'donut', height: 200, toolbar: { show: false }, fontFamily: 'inherit' },
            colors: palette,
            plotOptions: { pie: { donut: { size: '65%', labels: { show: true,
                name:  { show: true, fontSize: '10px', color: '#9CA3AF' },
                value: { show: true, fontSize: '14px', fontWeight: 700, color: '#111827',
                         formatter: function(v){ return 'Rp '+(parseFloat(v)/1000).toFixed(0)+'K'; } },
                total: { show: true, label: 'Total', fontSize: '10px', color: '#9CA3AF',
                         formatter: function(){ return 'Rp '+(totalCabang/1000000).toFixed(1)+'M'; } },
            }}}},
            dataLabels: { enabled: false },
            legend: { show: false },
            stroke: { width: 2, colors: ['#fff'] },
            tooltip: { y: { formatter: function(v){ return 'Rp '+Number(v).toLocaleString('id-ID'); } } },
        }).render();
    } else if (cabangEl) {
        cabangEl.innerHTML = '<div class="flex items-center justify-center h-48 text-sm text-gray-400 italic">Belum ada transaksi hari ini.</div>';
    }
})();
</script>
@endpush