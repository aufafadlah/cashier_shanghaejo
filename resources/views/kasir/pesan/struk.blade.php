<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $penjualan->id_penjualan }}</title>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc; 
            margin: 0; 
            display: flex; 
            flex-direction: column;
            align-items: center;
            padding: 20px 0;
        }
         @media print {
    @page {
        size: 80mm auto; /* atau 58mm */
        margin: 0;
    }

    body {
        margin: 0;
    }

    .receipt-card {
        width: 100% !important;
        max-width: 300px;
    }
}
        /* Navigasi */
        .no-print { display: flex; gap: 8px; margin-bottom: 15px; }
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
        }
        .btn-print { background: #10b981; color: white; }
        .btn-back { background: white; color: #64748b; border: 1px solid #e2e8f0; }

        /* Card Struk (Dikecilkan ukurannya) */
        .receipt-card { 
            background: white; 
            width: 260px; /* Ukuran lebih pas untuk struk */
            border-radius: 16px; 
            padding: 25px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* Logo Section */
        .logo-container { text-align: center; margin-bottom: 15px; }
        .logo-img {
            width: 55px;
            height: 55px;
            background: #064e3b;
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid #f1f5f9;
        }
        .logo-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-name { font-weight: 800; font-size: 16px; color: #1e293b; margin: 8px 0 2px; text-align: center; }
        .address { font-size: 10px; color: #94a3b8; text-align: center; margin-bottom: 20px; line-height: 1.3; }

        /* Meta Info */
        .meta-info { font-size: 10px; color: #64748b; margin-bottom: 15px; }
        .flex-between { display: flex; justify-content: space-between; margin-bottom: 3px; }
        
        .divider { border-top: 1px dashed #000; margin: 10px 0; }

        /* Items */
        .item-row { margin-bottom: 12px; }
        .item-main { display: flex; justify-content: space-between; align-items: baseline; }
        .menu-name { font-weight: 700; font-size: 12px; color: #1e293b; }
        .menu-qty { font-weight: 700; font-size: 12px; }
        .topping-list { font-size: 10px; color: #10b981; margin-top: 2px; font-style: italic; }
        .price-sub { font-size: 11px; color: #64748b; margin-top: 2px; }

        /* Totals */
        .calc-section { font-size: 11px; color: #475569; line-height: 1.8; }
        .total-row { font-weight: 800; font-size: 15px; color: #0f172a; margin: 8px 0; border-top: 1px solid #f8fafc; padding-top: 5px; }

        .footer { font-size: 10px; color: #cbd5e1; text-align: center; margin-top: 25px; }

        @media print {
            body { background: white; padding: 0; }
            .no-print { display: none; }
            .receipt-card { box-shadow: none; border-radius: 0; width: 100%; padding: 10px; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="{{ route('kasir.pesan.index') }}" class="btn btn-back">← Kembali</a>
        <button class="btn btn-print" onclick="window.print()">Cetak</button>
    </div>

    <div class="receipt-card">
        <div class="logo-container">
            <div class="logo-img">
                <img src="{{ asset('img/shanghaejo.jpg') }}" alt="Logo" onerror="this.style.display='none'">
            </div>
            <div class="brand-name">ShangHaejoCicio</div>
            <div class="address">
                {{ $penjualan->nama_cabang }}<br>
                {{ $penjualan->alamat_cabang }}
            </div>
        </div>

<div class="meta-info">

    <div class="flex-between">
        <span>No:</span>
        <span>{{ $penjualan->id_penjualan }}</span>
    </div>

    <div class="flex-between">
        <span>{{ date('d/m/y', strtotime($penjualan->created_at)) }} {{ date('H:i', strtotime($penjualan->created_at)) }}</span>
        <span>Kasir: {{ $penjualan->nama_kasir ?? '-' }}</span>
    </div>

    <div class="flex-between">
        <span>Pelanggan:</span>
        <span>{{ $penjualan->nama_pelanggan }}</span>
    </div>

</div>
        <div class="divider"></div>

        @foreach($details as $item)
        <div class="item-row">
            <div class="item-main">
                <span class="menu-name">{{ $item->nama_menu }}</span>
                <span class="menu-qty">{{ $item->qty }}</span>
            </div>
            @if(count($item->toppings) > 0)
                <div class="topping-list">
                    + {{ implode(', ', array_column($item->toppings->toArray(), 'nama_toping')) }}
                </div>
            @endif
            <div class="price-sub">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
        </div>
        @endforeach

        <div class="divider"></div>

        <div class="calc-section">
            <div class="flex-between">
                <span>Subtotal</span>
                <span>Rp {{ number_format($penjualan->total_awal) }}</span>
            </div>
            <div class="flex-between total-row">
                <span>Total</span>
                <span>Rp {{ number_format($penjualan->total_bayar) }}</span>
            </div>
            <div class="flex-between">
                <span>Bayar ({{ ucfirst($penjualan->metode_pembayaran) }})</span>
                <span>Rp {{ number_format($penjualan->uang_tunai) }}</span>
            </div>
            <div class="flex-between">
                <span>Kembali</span>
                <span>Rp {{ number_format($penjualan->kembalian) }}</span>
            </div>
        </div>

        <div class="footer">
            <div class="divider"></div>
            <p style="font-weight: 700; color: #94a3b8; margin-bottom: 2px;">TERIMA KASIH</p>

            <p>Selamat Menikmati, Sampai Jumpa Kembali.</p>
            <p style="color :#064e3b">IG: @shanghaejo_cicio</p>
            <p style="color :#064e3b">Number: +62 88905779517 </p>
            

        </div>
    </div>
</body>
</html>