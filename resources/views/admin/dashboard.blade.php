@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    
    {{-- 1. Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-card title="Omzet Hari Ini" value="Rp {{ number_format($totalOmzet, 0, ',', '.') }}" type="aktif" />
        <x-card title="Transaksi Sukses" value="{{ $countTransaksi }}" type="total" />
        <x-card title="Total Pengeluaran" value="Rp {{ number_format($totalBeli, 0, ',', '.') }}" type="non-aktif" />
        <x-card title="Menu Aktif" value="{{ $countMenu }}" type="aktif" />
    </div>

    {{-- 2. Charts Section (Grafik Penjualan & Metode Pembayaran) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Grafik Penjualan (Line Chart) --}}
        <div class="lg:col-span-2 bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Hourly Sales Performance</h2>
                    <p class="text-xs text-gray-400 font-medium mt-1">Hari Ini</p>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Live Updates</span>
            </div>
            <div class="h-[250px]">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Grafik Metode Pembayaran (Donut Chart) --}}
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Payment Methods</h2>
            </div>
            <div class="relative h-[180px] flex items-center justify-center">
                <canvas id="paymentChart"></canvas>
            </div>
            <div class="flex justify-center gap-6 mt-4 text-xs font-bold text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-[#1E5E3A] rounded-full"></span> QRIS ({{ $pieData['qris'] ?? 0 }}%)
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-[#8BC34A] rounded-full"></span> Cash ({{ $pieData['cash'] ?? 0 }}%)
                </div>
            </div>
        </div>

    </div>

    {{-- 3. Bottom Section (Tabel Transaksi Terbaru & Progress Target Menu) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Tabel Transaksi Terbaru (Kiri) --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800">Transaksi Terbaru</h2>
                <a href="#" class="text-sm font-bold text-teal-600 hover:opacity-70 transition-all">Lihat Semua →</a>
            </div>
            
            <x-table>
                <x-slot name="header">
                    <th class="px-6 py-4 text-xs font-bold text-gray-400">JAM</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400">PEMBAYARAN</th>
                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-400">NOMINAL</th>
                </x-slot>

                @forelse($transaksi ?? [] as $tr)
                <tr class="hover:bg-gray-50/50 transition-colors group">
                    <td class="px-6 py-4 font-bold text-gray-700">
                        {{ \Carbon\Carbon::parse($tr->created_at)->format('H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 text-[10px] font-black rounded-lg bg-slate-100 text-slate-600 uppercase">
                            {{ $tr->metode_pembayaran ?? 'CASH' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right font-black text-gray-900">
                        Rp {{ number_format($tr->total_bayar, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">Belum ada transaksi hari ini.</td>
                </tr>
                @endforelse
            </x-table>
        </div>

        {{-- Target Menu Progress (Kanan) --}}
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-8">Target Menu</h2>
                <div class="space-y-8">
                    @forelse($targetMenu as $tm)
                    <div>
                        <div class="flex justify-between items-end mb-3">
                            <div>
                                <h4 class="text-sm font-black text-gray-800 leading-none mb-1">{{ $tm->nama_menu }}</h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                                    {{ $tm->terjual ?? 0 }} Terjual / {{ $tm->stok_target }} Target
                                </p>
                            </div>
                            <span class="text-xs font-black text-teal-600">{{ $tm->persen ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-teal-600 h-2.5 rounded-full shadow-[0_0_10px_rgba(13,148,136,0.3)] transition-all duration-1000" 
                                 style="width: {{ ($tm->persen ?? 0) > 100 ? 100 : ($tm->persen ?? 0) }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray-400">Belum ada target diatur.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-10">
                <a href="/admin/target" class="block w-full py-4 text-center bg-gray-50 hover:bg-gray-100 text-gray-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all">
                    Edit Target Harian
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
{{-- Load Plugin Datalabels agar teks persen langsung tercetak permanen di dalam lingkaran Donut Chart --}}
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil data murni JSON yang dioper dari backend controller
        const labelsJam = <?php echo json_encode($chartLabels ?? ['10:00', '12:00', '14:00', '16:00', '18:00', '20:00']); ?>;
        const dataPorsi = <?php echo json_encode($chartPorsi ?? [0,0,0,0,0,0]); ?>;
        const dataRupiah = <?php echo json_encode($chartRupiah ?? [0,0,0,0,0,0]); ?>;
        const pieData    = <?php echo json_encode($pieData ?? ['qris' => 0, 'cash' => 0]); ?>;

        // ==========================================
        // 1. LINE CHART (Hourly Sales)
        // ==========================================
        const ctxSales = document.getElementById('salesChart').getContext('2d');
        const gradient = ctxSales.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(13, 148, 136, 0.3)');
        gradient.addColorStop(1, 'rgba(13, 148, 136, 0)');

        new Chart(ctxSales, {
            type: 'line',
            data: {
                labels: labelsJam,
                datasets: [{
                    label: 'Porsi Terjual',
                    data: dataPorsi, 
                    borderColor: '#0d9488',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0d9488',
                    pointBorderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                return 'Jam ' + context[0].label;
                            },
                            label: function(context) {
                                let index = context.dataIndex;
                                let porsi = dataPorsi[index];
                                let rupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(dataRupiah[index]);
                                return [
                                    'Terjual: ' + porsi + ' Porsi',
                                    'Pendapatan: ' + rupiah
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#f3f4f6' },
                        ticks: { color: '#9ca3af', font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9ca3af', font: { size: 10 } }
                    }
                }
            }
        });

        // ==========================================
        // 2. DONUT CHART (Payment Methods)
        // ==========================================
        const ctxPayment = document.getElementById('paymentChart').getContext('2d');
        new Chart(ctxPayment, {
            type: 'doughnut',
            data: {
                labels: ['QRIS', 'Cash'],
                datasets: [{
                    data: [pieData.qris, pieData.cash],
                    backgroundColor: ['#1E5E3A', '#8BC34A'],
                    borderWidth: 0
                }]
            },
            plugins: [ChartDataLabels], // Mengaktifkan plugin pencetak label teks
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.raw + '%';
                            }
                        }
                    },
                    datalabels: {
                        color: '#ffffff',
                        font: {
                            weight: 'bold',
                            size: 12
                        },
                        formatter: function(value) {
                            return value > 0 ? value + '%' : ''; 
                        }
                    }
                }
            }
        });
    });
</script>
@endpush