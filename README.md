🥟 Cashier Shanghaejo: Enterprise Multi-Outlet POS Solution
Cashier Shanghaejo adalah sistem manajemen kasir modern berbasis web yang dikembangkan menggunakan Laravel 11. Fokus utama aplikasi ini adalah efisiensi operasional kuliner skala menengah-atas dengan manajemen multi-cabang terpusat, kontrol inventaris yang ketat, dan integritas data keuangan yang absolut.

🛠️ Arsitektur Sistem & Fitur Unggulan
🛡️ Keamanan & Kontrol Akses (Admin)
Admin memiliki kontrol penuh sebagai Superuser untuk menjaga stabilitas bisnis di semua titik:

Centralized Branch Control: Fitur Kill-Switch yang memungkinkan Admin menonaktifkan cabang secara instan. Jika cabang dinonaktifkan, akses login kasir tertutup namun data historis tetap tersimpan permanen.

Inventory & Raw Material Tracking: Manajemen pembelian bahan baku (tb_beli_bahan) yang terintegrasi langsung untuk menghitung laba bersih.

Daily Stock Target: Sistem pembatasan penjualan berdasarkan stok harian (tb_target) untuk mencegah over-selling.

User Management: Pengelolaan user terpusat untuk menghindari registrasi tidak sah (tidak ada fitur daftar mandiri).

⚡ Transaksi & Integritas Data (Kasir)
Sisi kasir dioptimalkan untuk kecepatan dan keakuratan data:

Price Snapshot Logic: Setiap transaksi menyimpan harga dan nama menu pada saat itu juga ke dalam harga_snapshot. Laporan keuangan tidak akan berubah meskipun harga master diupdate di kemudian hari.

Multi-Topping Integration: Sistem pemilihan topping yang fleksibel dengan kalkulasi subtotal otomatis.

Real-time Void System: Pembatalan transaksi dengan status gagal (bukan dihapus) guna menjaga integritas audit (mencegah kecurangan kasir).

📈 Dashboard & Analitik
Sistem ini menyediakan visualisasi data untuk mempermudah pengambilan keputusan:

Dashboard Monitoring: Ringkasan Omzet, Pengeluaran, dan Profitabilitas harian dalam bentuk grafik interaktif.

Laporan Target vs Realisasi: Memantau efisiensi penjualan setiap menu dibandingkan dengan target stok yang ditetapkan.

Multi-Payment Analytics: Laporan metode pembayaran (Cash vs QRIS) untuk mempermudah rekonsiliasi kas harian.

💻 Tech Stack
Framework: Laravel 11 (Latest Version)

UI Engine: Blade Templating + [Tailwind CSS/Bootstrap]

Database: MySQL (Optimized with Snapshots & Soft Disable logic)

Charts: Chart.js

📝 Catatan Penting untuk Penguji
Proyek ini mengimplementasikan prinsip Data Persistence. Tidak ada data transaksi yang benar-benar dihapus dari sistem. Jika terjadi kesalahan, status transaksi diubah menjadi gagal, memastikan jejak audit tetap utuh dan laporan keuangan dapat dipertanggungjawabkan 100%.
