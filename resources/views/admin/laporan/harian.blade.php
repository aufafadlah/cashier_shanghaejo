@extends('layouts.admin')

@section('title', 'Laporan Harian')

@section('content')
<div class="space-y-6 pb-20">
    {{-- Header Halaman --}}
    <div class="mb-6">
        <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Laporan Penjualan Hari Ini</h2>
        <p class="text-xs text-gray-500 italic">Analisis performa transaksi dan metode pembayaran secara real-time.</p>
    </div>

    {{-- Kartu Statistik (Menggunakan Komponen Card) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card title="Total Penjualan Hari Ini" value="50" type="total" />
        <x-card title="Total Pendapatan" value="Rp 4.500.000" type="aktif" />
        <x-card title="Total Pelanggan" value="45" type="total" />
    </div>

    {{-- Area Grafik --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Grafik Tren Per Jam --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="font-bold text-gray-800">Hourly Sales Performance</h3>
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Update: {{ now()->format('H:i') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-[#7AB465] rounded-full"></span>
                    <span class="text-xs font-bold text-gray-500">Penjualan</span>
                </div>
            </div>
            <div class="h-[300px]">
                <canvas id="hourlyChart"></canvas>
            </div>
        </div>

        {{-- Grafik Metode Pembayaran --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="font-bold text-gray-800 mb-6">Payment Methods</h3>
            <div class="h-[250px] relative">
                <canvas id="paymentChart"></canvas>
            </div>
            <div class="mt-6 space-y-3">
                <div class="flex items-center justify-between p-2 rounded-xl bg-gray-50">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-[#1E5128] rounded-full"></div>
                        <span class="text-xs font-bold text-gray-600">QRIS</span>
                    </div>
                    <span class="text-xs font-black text-gray-800">70%</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-xl bg-gray-50">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-[#7AB465] rounded-full"></div>
                        <span class="text-xs font-bold text-gray-600">Cash</span>
                    </div>
                    <span class="text-xs font-black text-gray-800">30%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Aktivitas Terbaru (Menggunakan Komponen Table) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 uppercase text-sm tracking-widest">Recent Activity</h3>
            <button class="text-xs font-bold text-[#7AB465] hover:underline">Lihat Semua</button>
        </div>
        <x-table>
            <x-slot name="header">
                <tr>
                    <th class="p-4 text-center">No</th>
                    <th class="p-4 text-left">Waktu</th>
                    <th class="p-4 text-left">Pelanggan</th>
                    <th class="p-4 text-left">Total Transaksi</th>
                    <th class="p-4 text-center">Metode</th>
                    <th class="p-4 text-center">Status</th>
                </tr>
            </x-slot>

            {{-- Contoh Data --}}
            <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="p-4 text-center text-gray-400">1</td>
                <td class="p-4 text-sm font-bold text-gray-700">16:20</td>
                <td class="p-4">
                    <div class="text-sm font-bold text-gray-800">Meja 04</div>
                    <div class="text-[10px] text-gray-400 uppercase">Pelanggan Umum</div>
                </td>
                <td class="p-4 text-sm font-black text-gray-800">Rp 125.000</td>
                <td class="p-4 text-center">
                    <span class="px-2 py-1 rounded bg-teal-50 text-teal-700 text-[10px] font-bold uppercase">QRIS</span>
                </td>
                <td class="p-4 text-center">
                    <x-status-badge :active="true" />
                </td>
            </tr>
        </x-table>
    </div>
</div>

{{-- Script Chart.js --}}
@push('scripts')
<script>
    // Grafik Garis (Hourly Performance)
    const ctxHourly = document.getElementById('hourlyChart').getContext('2d');
    new Chart(ctxHourly, {
        type: 'line',
        data: {
            labels: ['10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'],
            datasets: [{
                data: [10, 15, 12, 30, 20, 25, 35],
                borderColor: '#7AB465',
                backgroundColor: 'rgba(122, 180, 101, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#7AB465',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f3f4f6' }, border: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });

    // Grafik Donat (Payment Methods)
    const ctxPayment = document.getElementById('paymentChart').getContext('2d');
    new Chart(ctxPayment, {
        type: 'doughnut',
        data: {
            labels: ['QRIS', 'Cash'],
            datasets: [{
                data: [70, 30],
                backgroundColor: ['#1E5128', '#7AB465'],
                hoverOffset: 4,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: { legend: { display: false } }
        }
    });
</script>
@endpush
@endsection