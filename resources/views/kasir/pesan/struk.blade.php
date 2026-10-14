<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - SHJ-{{ date('dmy', strtotime($penjualan->created_at)) }}-{{ str_pad($penjualan->no_urut ?? 1, 3, '0', STR_PAD_LEFT) }}</title>
    
    <!-- Dynamic Print Page Size (48mm / 58mm / 80mm) -->
    <style id="dynamic-page-size">
        @page {
            size: 58mm auto;
            margin: 0;
        }
    </style>

    <style>
        /* Base Screen Style */
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; 
            background-color: #f1f5f9; 
            margin: 0; 
            display: flex; 
            flex-direction: column;
            align-items: center;
            padding: 20px 12px 40px;
            color: #000000;
            -webkit-font-smoothing: antialiased;
        }

        /* Navigasi & Toolbar (Simple, No Emoji) */
        .no-print { 
            display: flex; 
            align-items: center;
            justify-content: center;
            gap: 8px; 
            margin-bottom: 18px; 
            flex-wrap: wrap;
            max-width: 100%;
        }

        .btn {
            padding: 7px 14px;
            border-radius: 6px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            user-select: none;
        }
        .btn:active {
            transform: scale(0.98);
        }

        .btn-print { 
            background: #059669; 
            color: #ffffff; 
        }
        .btn-print:hover { 
            background: #047857; 
        }

        .btn-back { 
            background: #ffffff; 
            color: #334155; 
            border: 1px solid #cbd5e1; 
        }
        .btn-back:hover { 
            background: #f8fafc; 
            color: #000000; 
        }

        /* Tombol Toggle Logo */
        .btn-logo {
            background: #ffffff; 
            color: #475569; 
            border: 1px solid #cbd5e1; 
        }
        .btn-logo:hover {
            background: #f8fafc;
        }
        .btn-logo.active {
            background: #ecfdf5;
            color: #065f46;
            border-color: #10b981;
        }
        .btn-logo.active:hover {
            background: #d1fae5;
        }

        /* Ukuran Kertas Selector */
        .paper-switcher {
            display: inline-flex;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            overflow: hidden;
            padding: 2px;
        }
        .btn-size {
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 700;
            border: none;
            background: transparent;
            color: #475569;
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.15s ease;
        }
        .btn-size.active {
            background: #059669;
            color: #ffffff;
        }

        /* =========================================================
           CARD STRUK (WARNA TULISAN HITAM PEKAT & SANGAT JELAS)
           ========================================================= */
        .receipt-card { 
            background: #ffffff; 
            border-radius: 14px; 
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
            transition: width 0.2s ease, padding 0.2s ease;
            box-sizing: border-box;
            color: #000000; /* Hitam pekat di seluruh struk */
        }

        /* Ukuran 48mm (Ekstra Kecil) */
        .receipt-card.size-48mm {
            width: 195px;
            max-width: 95vw;
            padding: 14px 10px;
        }
        .receipt-card.size-48mm .brand-name { font-size: 13px; }
        .receipt-card.size-48mm .address { font-size: 8.5px; line-height: 1.25; margin-bottom: 8px; }
        .receipt-card.size-48mm .meta-info { font-size: 8.5px; line-height: 1.35; margin-bottom: 6px; }
        .receipt-card.size-48mm .menu-name { font-size: 9.5px; }
        .receipt-card.size-48mm .menu-qty { font-size: 9.5px; }
        .receipt-card.size-48mm .topping-list { font-size: 8px; }
        .receipt-card.size-48mm .price-sub { font-size: 9.5px; }
        .receipt-card.size-48mm .calc-section { font-size: 9px; line-height: 1.5; }
        .receipt-card.size-48mm .total-row { font-size: 11.5px; }
        .receipt-card.size-48mm .footer { font-size: 8.5px; margin-top: 12px; }

        /* Ukuran 58mm (Default Standar Mini Kasir) */
        .receipt-card.size-58mm {
            width: 225px;
            max-width: 95vw;
            padding: 18px 14px;
        }
        .receipt-card.size-58mm .brand-name { font-size: 14.5px; }
        .receipt-card.size-58mm .address { font-size: 9.5px; line-height: 1.3; margin-bottom: 10px; }
        .receipt-card.size-58mm .meta-info { font-size: 9.5px; line-height: 1.4; margin-bottom: 10px; }
        .receipt-card.size-58mm .menu-name { font-size: 10.5px; }
        .receipt-card.size-58mm .menu-qty { font-size: 10.5px; }
        .receipt-card.size-58mm .topping-list { font-size: 8.5px; }
        .receipt-card.size-58mm .price-sub { font-size: 10.5px; }
        .receipt-card.size-58mm .calc-section { font-size: 10px; line-height: 1.6; }
        .receipt-card.size-58mm .total-row { font-size: 13.5px; }
        .receipt-card.size-58mm .footer { font-size: 9.5px; margin-top: 14px; }

        /* Ukuran 80mm (Besar / Standar POS Desktop) */
        .receipt-card.size-80mm {
            width: 340px;
            max-width: 95vw;
            padding: 26px 24px;
        }
        .receipt-card.size-80mm .brand-name { font-size: 17.5px; }
        .receipt-card.size-80mm .address { font-size: 11.5px; line-height: 1.4; margin-bottom: 16px; }
        .receipt-card.size-80mm .meta-info { font-size: 11.5px; line-height: 1.5; margin-bottom: 14px; }
        .receipt-card.size-80mm .menu-name { font-size: 13px; }
        .receipt-card.size-80mm .menu-qty { font-size: 13px; }
        .receipt-card.size-80mm .topping-list { font-size: 11px; }
        .receipt-card.size-80mm .price-sub { font-size: 12.5px; }
        .receipt-card.size-80mm .calc-section { font-size: 12.5px; line-height: 1.8; }
        .receipt-card.size-80mm .total-row { font-size: 16.5px; }
        .receipt-card.size-80mm .footer { font-size: 11.5px; margin-top: 22px; }

        /* Logo Section */
        .logo-container { 
            text-align: center; 
            margin-bottom: 10px; 
        }
        .logo-img {
            width: 52px;
            height: 52px;
            background: #064e3b;
            border-radius: 50%;
            margin: 0 auto 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 2px solid #000000;
        }
        .logo-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Saat opsi Tanpa Logo aktif */
        .hide-logo .logo-img {
            display: none !important;
        }

        /* TIPOGRAFI TEGAS & WARNA HITAM PEKAT */
        .brand-name { 
            font-weight: 900; 
            color: #000000; 
            margin: 4px 0 2px; 
            text-align: center; 
            letter-spacing: 0.3px;
        }
        .address { 
            color: #000000; 
            font-weight: 600;
            text-align: center; 
        }

        /* Meta Info (No Struk, Tanggal, Kasir, Pelanggan) */
        .meta-info { 
            color: #000000; 
            font-weight: 600;
        }
        .flex-between { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 3px; 
        }
        
        /* Garis Pemisah Hitam Tebal & Rapi */
        .divider { 
            border-top: 1.5px dashed #000000; 
            margin: 8px 0; 
        }

        /* Daftar Menu Item */
        .item-row { 
            margin-bottom: 8px; 
        }
        .item-main { 
            display: flex; 
            justify-content: space-between; 
            align-items: baseline; 
            gap: 6px;
        }
        .menu-name { 
            font-weight: 800; 
            color: #000000; 
            flex: 1;
            word-break: break-word;
        }
        .menu-qty { 
            font-weight: 800; 
            color: #000000;
            white-space: nowrap;
        }
        .topping-list { 
            color: #000000; 
            font-weight: 700;
            margin-top: 2px; 
            padding-left: 4px;
        }
        .price-sub { 
            color: #000000; 
            margin-top: 2px; 
            text-align: right;
            font-weight: 800;
        }

        /* Bagian Perhitungan Total */
        .calc-section { 
            color: #000000; 
            font-weight: 700;
        }
        .total-row { 
            font-weight: 900; 
            color: #000000; 
            margin: 6px 0; 
            border-top: 1.5px dashed #000000; 
            border-bottom: 1.5px dashed #000000; 
            padding: 4px 0; 
        }

        /* Footer Struk */
        .footer { 
            color: #000000; 
            font-weight: 600;
            text-align: center; 
            line-height: 1.45;
        }
        .footer-thanks {
            font-weight: 900 !important;
            color: #000000 !important;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .footer-contact {
            color: #000000 !important;
            font-weight: 700 !important;
            margin-top: 4px;
        }

        /* =========================================================
           PRINT STYLES (100% HITAM PEKAT, ULTRA CONTRAST THERMAL)
           ========================================================= */
        @media print {
            *,
            *::before,
            *::after {
                color: #000000 !important;
                text-shadow: none !important;
                box-shadow: none !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body { 
                background: #ffffff !important; 
                margin: 0 !important; 
                padding: 0 !important; 
                width: 100% !important;
            }

            .no-print { 
                display: none !important; 
            }

            .receipt-card { 
                box-shadow: none !important; 
                border-radius: 0 !important; 
                margin: 0 auto !important; 
                padding: 0 !important;
                color: #000000 !important;
            }

            /* UKURAN 48mm SAAT CETAK (Mini / Bluetooth Printer) */
            .receipt-card.size-48mm {
                width: 42mm !important;
                max-width: 42mm !important;
                font-size: 9px !important;
                padding: 1mm 0 3mm !important;
            }
            .receipt-card.size-48mm .brand-name { font-size: 12px !important; }
            .receipt-card.size-48mm .address { font-size: 8.5px !important; line-height: 1.25 !important; }
            .receipt-card.size-48mm .meta-info { font-size: 8.5px !important; }
            .receipt-card.size-48mm .menu-name { font-size: 9.5px !important; }
            .receipt-card.size-48mm .menu-qty { font-size: 9.5px !important; }
            .receipt-card.size-48mm .topping-list { font-size: 8px !important; }
            .receipt-card.size-48mm .price-sub { font-size: 9.5px !important; }
            .receipt-card.size-48mm .calc-section { font-size: 9px !important; }
            .receipt-card.size-48mm .total-row { font-size: 11.5px !important; }
            .receipt-card.size-48mm .footer { font-size: 8.5px !important; }

            /* UKURAN 58mm SAAT CETAK (Standar Thermal POS: Printable area 48mm / 384 dots) */
            .receipt-card.size-58mm {
                width: 48mm !important;
                max-width: 48mm !important;
                font-size: 10px !important;
                padding: 1.5mm 0 4mm !important;
            }
            .receipt-card.size-58mm .brand-name { font-size: 13.5px !important; }
            .receipt-card.size-58mm .address { font-size: 9.5px !important; line-height: 1.3 !important; }
            .receipt-card.size-58mm .meta-info { font-size: 9.5px !important; }
            .receipt-card.size-58mm .menu-name { font-size: 10.5px !important; }
            .receipt-card.size-58mm .menu-qty { font-size: 10.5px !important; }
            .receipt-card.size-58mm .topping-list { font-size: 8.5px !important; }
            .receipt-card.size-58mm .price-sub { font-size: 10.5px !important; }
            .receipt-card.size-58mm .calc-section { font-size: 10px !important; }
            .receipt-card.size-58mm .total-row { font-size: 13px !important; }
            .receipt-card.size-58mm .footer { font-size: 9.5px !important; }

            /* UKURAN 80mm SAAT CETAK */
            .receipt-card.size-80mm {
                width: 72mm !important;
                max-width: 72mm !important;
                font-size: 12px !important;
                padding: 3mm 1mm 6mm !important;
            }
            .receipt-card.size-80mm .brand-name { font-size: 16px !important; }
            .receipt-card.size-80mm .total-row { font-size: 15px !important; }

            .divider { 
                border-top: 1.5px dashed #000000 !important; 
                margin: 5px 0 !important; 
            }

            .brand-name,
            .address,
            .meta-info,
            .menu-name,
            .menu-qty,
            .topping-list,
            .price-sub,
            .calc-section,
            .total-row,
            .footer,
            .footer-thanks,
            .footer-contact {
                color: #000000 !important;
                font-weight: 700 !important;
            }

            .logo-img {
                background: transparent !important;
                border: none !important;
            }

            .logo-img img {
                filter: grayscale(100%) contrast(200%) brightness(85%) !important;
                -webkit-filter: grayscale(100%) contrast(200%) brightness(85%) !important;
            }

            .hide-logo .logo-img {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Navigasi Simple (Tanpa Emoji) -->
    <div class="no-print">
        <a href="{{ route('kasir.pesan.index') }}" onclick="if(window.history.length > 1) { window.history.back(); return false; }" class="btn btn-back">
            ← Kembali
        </a>

        <!-- Pilihan Ukuran Printer: 48mm (Mini), 58mm (Standar Mini), 80mm (Besar) -->
        <div class="paper-switcher">
            <button type="button" class="btn-size" id="btn-size-48" onclick="setPaperSize('48mm')">48mm</button>
            <button type="button" class="btn-size" id="btn-size-58" onclick="setPaperSize('58mm')">58mm</button>
            <button type="button" class="btn-size" id="btn-size-80" onclick="setPaperSize('80mm')">80mm</button>
        </div>

        <!-- Toggle Logo (Pakai Logo / Tanpa Logo) -->
        <button type="button" class="btn btn-logo active" id="btn-toggle-logo" onclick="toggleLogo()">
            Logo: On
        </button>

        <button type="button" class="btn btn-print" onclick="window.print()">
            Cetak
        </button>
    </div>

    <!-- Card Struk -->
    <div class="receipt-card size-58mm" id="receipt-card">
        <div class="logo-container">
            <div class="logo-img" id="receipt-logo">
                <img src="{{ asset($setting->logo_path ?? 'img/shanghaejo.jpg') }}" alt="Logo" onerror="this.parentElement.style.display='none'">
            </div>
            <div class="brand-name">{{ $setting->brand_name ?? 'ShangHaejoCicio' }}</div>
            <div class="address">
                {{ $penjualan->nama_cabang }}<br>
                {{ $penjualan->alamat_cabang }}
            </div>
        </div>

        <div class="meta-info">
            <div class="flex-between">
                <span>No:</span>
                <span>SHJ-{{ date('dmy', strtotime($penjualan->created_at)) }}-{{ str_pad($penjualan->no_urut ?? 1, 3, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex-between">
                <span>{{ date('d/m/y H:i', strtotime($penjualan->created_at)) }}</span>
                <span>Kasir: {{ $penjualan->nama_kasir ?? '-' }}</span>
            </div>
            <div class="flex-between">
                <span>Pelanggan:</span>
                <span>{{ $penjualan->nama_pelanggan ?: 'Umum' }}</span>
            </div>
        </div>

        <div class="divider"></div>

        @foreach($details as $item)
        <div class="item-row">
            <div class="item-main">
                <span class="menu-name">{{ $item->nama_menu }}</span>
                <span class="menu-qty">{{ $item->qty }}</span>
            </div>
            @if(isset($item->toppings) && count($item->toppings) > 0)
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
                <span>Rp {{ number_format($penjualan->total_awal, 0, ',', '.') }}</span>
            </div>
            @if($penjualan->total_awal > $penjualan->total_bayar)
            <div class="flex-between">
                <span>Diskon</span>
                <span>- Rp {{ number_format($penjualan->total_awal - $penjualan->total_bayar, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex-between total-row">
                <span>Total</span>
                <span>Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</span>
            </div>
            <div class="flex-between">
                <span>Bayar ({{ ucfirst($penjualan->metode_pembayaran) }})</span>
                <span>Rp {{ number_format($penjualan->uang_tunai ?? $penjualan->total_bayar, 0, ',', '.') }}</span>
            </div>
            <div class="flex-between">
                <span>Kembali</span>
                <span>Rp {{ number_format($penjualan->kembalian ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="footer">
            <div class="divider"></div>
            <p class="footer-thanks">TERIMA KASIH</p>
            <p>Selamat Menikmati, Sampai Jumpa Kembali.</p>
            <p class="footer-contact">IG: {{ $setting->instagram ?? '@shanghaejo_cicio' }}</p>
            <p class="footer-contact">Number: {{ $setting->telepon ?? '+62 88905779517' }}</p>
        </div>
    </div>

    <script>
        const STORAGE_KEY_SIZE = 'shj_printer_size';
        const STORAGE_KEY_LOGO = 'shj_use_logo';

        // Fungsi Mengatur Ukuran Kertas (48mm / 58mm / 80mm)
        function setPaperSize(size) {
            const card = document.getElementById('receipt-card');
            const pageStyle = document.getElementById('dynamic-page-size');

            // Reset classes
            card.classList.remove('size-48mm', 'size-58mm', 'size-80mm');
            document.querySelectorAll('.btn-size').forEach(btn => btn.classList.remove('active'));

            if (size === '48mm') {
                card.classList.add('size-48mm');
                document.getElementById('btn-size-48').classList.add('active');
                pageStyle.innerHTML = '@page { size: 48mm auto; margin: 0; }';
                localStorage.setItem(STORAGE_KEY_SIZE, '48mm');
            } else if (size === '80mm') {
                card.classList.add('size-80mm');
                document.getElementById('btn-size-80').classList.add('active');
                pageStyle.innerHTML = '@page { size: 80mm auto; margin: 0; }';
                localStorage.setItem(STORAGE_KEY_SIZE, '80mm');
            } else {
                card.classList.add('size-58mm');
                document.getElementById('btn-size-58').classList.add('active');
                pageStyle.innerHTML = '@page { size: 58mm auto; margin: 0; }';
                localStorage.setItem(STORAGE_KEY_SIZE, '58mm');
            }
        }

        // Fungsi Toggle Logo On / Off
        function toggleLogo(forceState) {
            const card = document.getElementById('receipt-card');
            const btnLogo = document.getElementById('btn-toggle-logo');

            let isEnabled;
            if (typeof forceState === 'boolean') {
                isEnabled = forceState;
            } else {
                // Toggle state saat tombol diklik
                isEnabled = !btnLogo.classList.contains('active');
            }

            if (isEnabled) {
                card.classList.remove('hide-logo');
                btnLogo.classList.add('active');
                btnLogo.textContent = 'Logo: On';
                localStorage.setItem(STORAGE_KEY_LOGO, 'true');
            } else {
                card.classList.add('hide-logo');
                btnLogo.classList.remove('active');
                btnLogo.textContent = 'Logo: Off';
                localStorage.setItem(STORAGE_KEY_LOGO, 'false');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Restore ukuran kertas (default 58mm)
            const savedSize = localStorage.getItem(STORAGE_KEY_SIZE) || '58mm';
            setPaperSize(savedSize);

            // Restore preferensi logo (default On / true)
            const savedLogo = localStorage.getItem(STORAGE_KEY_LOGO);
            if (savedLogo === 'false') {
                toggleLogo(false);
            } else {
                toggleLogo(true);
            }
        });
    </script>
</body>
</html>