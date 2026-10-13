<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Kasir - {{ $cabang->nama_cabang ?? 'Shanghaejo' }} - {{ $today }}</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
        }
        body {
            margin: 0;
            padding: 24px;
            background: #fff;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .brand-title {
            font-size: 22px;
            font-weight: 800;
            color: #0f766e;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 12px;
            color: #64748b;
            margin: 3px 0 0 0;
        }
        .meta-info {
            text-align: right;
            font-size: 11px;
            color: #475569;
        }
        .meta-info strong {
            color: #0f172a;
        }
        .summary-box {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
        }
        .summary-card {
            flex: 1;
            padding: 10px 14px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .summary-label {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
        }
        .summary-value {
            font-size: 16px;
            font-weight: 800;
            color: #0f766e;
            margin-top: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 8px;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-align: left;
            padding: 8px 10px;
            border-bottom: 1px solid #cbd5e1;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }
        td {
            padding: 7px 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        tr:nth-child(even) td {
            background-color: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-qris {
            background: #f3e8ff;
            color: #7e22ce;
        }
        .badge-cash {
            background: #dcfce7;
            color: #15803d;
        }
        .footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px dashed #cbd5e1;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #94a3b8;
        }
        .no-print-bar {
            background: #0f766e;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: white;
            color: #0f766e;
            font-weight: 700;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
        }
        @media print {
            .no-print-bar {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <span>Pratinjau Cetak / Ekspor PDF Riwayat Transaksi</span>
        <div>
            <button class="btn-print" onclick="window.print()">Cetak / Simpan sebagai PDF</button>
        </div>
    </div>

    <div class="header">
        <div>
            <h1 class="brand-title">SHANGHAEJO POS</h1>
            <p class="brand-subtitle">Laporan Transaksi Kasir Harian</p>
        </div>
        <div class="meta-info">
            <div><strong>Cabang:</strong> {{ $cabang->nama_cabang ?? 'Pusat' }}</div>
            <div><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}</div>
            <div><strong>Dicetak pada:</strong> {{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</div>
        </div>
    </div>

    @php
        $totalTransaksi = count($orders);
        $totalPendapatan = $orders->sum('total_bayar');
        $totalCash = $orders->where('metode_pembayaran', 'cash')->sum('total_bayar');
        $totalQris = $orders->where('metode_pembayaran', 'qris')->sum('total_bayar');
    @endphp

    <div class="summary-box">
        <div class="summary-card">
            <div class="summary-label">Total Pesanan</div>
            <div class="summary-value">{{ $totalTransaksi }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Tunai (Cash)</div>
            <div class="summary-value" style="color: #15803d;">Rp {{ number_format($totalCash, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Total Non-Tunai (QRIS)</div>
            <div class="summary-value" style="color: #7e22ce;">Rp {{ number_format($totalQris, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card" style="border-color: #0d9488; background: #f0fdfa;">
            <div class="summary-label" style="color: #0d9488;">Grand Total Omset</div>
            <div class="summary-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">No</th>
                <th style="width: 140px;">No. Struk</th>
                <th style="width: 60px;" class="text-center">Jam</th>
                <th>Nama Pelanggan</th>
                <th class="text-center" style="width: 80px;">Metode</th>
                <th>Kasir</th>
                <th class="text-right" style="width: 110px;">Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td class="text-center font-bold">{{ $order->no_urut }}</td>
                <td style="font-family: monospace; font-weight: 700;">{{ $order->no_struk }}</td>
                <td class="text-center">{{ date('H:i', strtotime($order->created_at)) }}</td>
                <td style="font-weight: 600; text-transform: uppercase;">{{ $order->nama_pelanggan }}</td>
                <td class="text-center">
                    <span class="badge {{ strtolower($order->metode_pembayaran) === 'qris' ? 'badge-qris' : 'badge-cash' }}">
                        {{ strtoupper($order->metode_pembayaran) }}
                    </span>
                </td>
                <td>{{ $order->nama_kasir ?? '-' }}</td>
                <td class="text-right font-bold">Rp {{ number_format($order->total_bayar, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color: #94a3b8;">
                    Tidak ada transaksi pada tanggal ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div>Cashier Shanghaejo System &bull; Dokumen Resmi Penjualan Harian</div>
        <div>Halaman 1 dari 1</div>
    </div>

    <script>
        // Otomatis buka dialog print saat halaman dibuka jika diinginkan
        window.addEventListener('load', function() {
            // Uncomment line below if instant print dialog is desired:
            // window.print();
        });
    </script>
</body>
</html>
