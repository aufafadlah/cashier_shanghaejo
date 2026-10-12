<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengeluaran - {{ $periodeText }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background-color: #fff;
            margin: 0;
            padding: 24px;
            line-height: 1.4;
        }

        .no-print {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .action-btn-print { background-color: #0d9488; color: #fff; }
        .action-btn-print:hover { background-color: #0f766e; }
        .action-btn-close { background-color: #64748b; color: #fff; }
        .action-btn-close:hover { background-color: #475569; }

        .header {
            border-bottom: 2px solid #0d9488;
            padding-bottom: 14px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .header-title h1 {
            font-size: 20px;
            font-weight: 800;
            color: #0f766e;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }
        .header-title p {
            margin: 0;
            font-size: 11px;
            color: #64748b;
        }
        .header-meta {
            text-align: right;
            font-size: 10px;
            color: #475569;
        }
        .header-meta .badge-periode {
            display: inline-block;
            background-color: #ccfbf1;
            color: #0f766e;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 11px;
            margin-bottom: 4px;
        }

        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }
        .summary-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            background-color: #f8fafc;
        }
        .summary-card .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .summary-card .val {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }
        .summary-card .sub {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th {
            background-color: #f0fdfa;
            color: #115e59;
            font-weight: 700;
            text-align: left;
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            vertical-align: middle;
        }
        tr:nth-child(even) { background-color: #f8fafc; }
        tr { page-break-inside: avoid; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }

        .table-total {
            margin-top: 14px;
            padding: 10px 14px;
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .table-total .title {
            font-size: 11px;
            font-weight: 700;
            color: #991b1b;
            text-transform: uppercase;
        }
        .table-total .amount {
            font-size: 16px;
            font-weight: 800;
            color: #dc2626;
        }

        .footer {
            margin-top: 24px;
            padding-top: 10px;
            border-top: 1px dashed #cbd5e1;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #94a3b8;
        }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 10px; }
            .header { border-bottom: 2px solid #000; }
            th { background-color: #eee !important; color: #000 !important; }
            .summary-card { border: 1px solid #ccc !important; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="action-btn action-btn-print">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak / Simpan PDF
        </button>
        <button onclick="window.close(); if(!window.closed){ window.history.back(); }" class="action-btn action-btn-close">
            Tutup
        </button>
    </div>

    <div class="header">
        <div class="header-title">
            <h1>ShangHaejo</h1>
            <p>Laporan Pengeluaran &amp; Pembelian Bahan</p>
        </div>
        <div class="header-meta">
            <div class="badge-periode">{{ $periodeText }}</div>
            <div><strong>Cabang:</strong> {{ $cabang ? $cabang->nama_cabang : 'Semua Cabang' }}</div>
            <div><strong>Waktu Cetak:</strong> {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="label">Total Transaksi</div>
            <div class="val">{{ number_format($totalTransaksi ?? count($items)) }}</div>
            <div class="sub">Catatan pengeluaran</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Kuantitas (Item)</div>
            <div class="val">{{ number_format($totalItem ?? $items->sum('qty')) }}</div>
            <div class="sub">Total unit bahan/barang dibeli</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Pengeluaran</div>
            <div class="val" style="color: #dc2626;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            <div class="sub">Biaya operasional bahan</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th style="width: 85px;">Tanggal</th>
                <th>Bahan / Barang</th>
                <th class="text-center" style="width: 50px;">Qty</th>
                <th class="text-right" style="width: 90px;">Harga Satuan</th>
                <th class="text-right" style="width: 95px;">Total Biaya</th>
                <th>Cabang</th>
                <th>Supplier</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $i => $row)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->tgl)->translatedFormat('d M Y') }}</td>
                <td class="font-bold">{{ $row->nama_barang ?? $row->nama_bahan ?? '-' }}</td>
                <td class="text-center font-bold">{{ number_format($row->qty) }}</td>
                <td class="text-right">Rp {{ number_format($row->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right font-bold" style="color: #dc2626;">Rp {{ number_format($row->harga_total, 0, ',', '.') }}</td>
                <td>{{ $row->nama_cabang ?? 'Pusat' }}</td>
                <td>{{ $row->supplier ?? '-' }}</td>
                <td>{{ $row->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 24px; color: #94a3b8;">
                    Tidak ada pengeluaran dalam periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="table-total">
        <div class="title">Total Biaya Pengeluaran</div>
        <div class="amount">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
    </div>

    <div class="footer">
        <div>Dicetak otomatis oleh Sistem POS ShangHaejo</div>
        <div>Dokumen Pengeluaran Bahan &bull; {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>
</body>
</html>
