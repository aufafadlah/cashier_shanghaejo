# 🔍 QA Report — Cashier Shanghaejo POS System

**Auditor:** Ghiats Abdurahman Rasyid 
**Tanggal Audit:** 23 Juni 2026  
**Versi Framework:** Laravel 11 / Tailwind CSS v4 / Alpine.js v3  
**Database:** MySQL (SQLite sebagai fallback dev)

---

## Ringkasan Eksekutif

| Kategori | Kritis 🔴 | Tinggi 🟠 | Sedang 🟡 | Rendah 🟢 |
|---|---|---|---|---|
| Security | 0 | 2 | 1 | 1 |
| Database / Schema | 0 | 3 | 2 | 1 |
| Backend Logic | 1 | 2 | 2 | 2 |
| Frontend / UI | 0 | 1 | 2 | 3 |
| Testing Coverage | — | 1 | — | 1 |
| Environment / Config | 0 | 1 | 1 | 0 |
| **Total** | **1** | **10** | **8** | **8** |

---

## 1. Security

### 🔴 KRITIS — `dd()` Debug Statement Aktif di Production Path

**File:** [`PesanKasirController.php:168`](file:///c:/laragon/www/cashier_shanghaejo/app/Http/Controllers/Kasir/PesanKasirController.php)

```php
// ❌ Kode aktif di production
dd('ERROR CHECKOUT: ', $e->getMessage(), 'Di Baris: ' . $e->getLine());
```

**Dampak:** Jika checkout error terjadi, server menampilkan stack trace penuh ke browser. Ini mengekspos nama tabel, query SQL, dan struktur direktori kepada pengguna akhir. Kode yang seharusnya diaktifkan (return JSON response) sudah ada tapi di-comment out.

**Rekomendasi:** Hapus baris `dd()` dan uncomment baris `return response()->json(...)` di bawahnya.

---

### 🟠 TINGGI — Login Page Masih Menggunakan Tailwind CDN

**File:** [`auth/login.blade.php:7`](file:///c:/laragon/www/cashier_shanghaejo/resources/views/auth/login.blade.php)

```html
<!-- ❌ CDN untuk production adalah anti-pattern -->
<script src="https://cdn.tailwindcss.com"></script>
```

**Dampak:** Halaman login memuat Tailwind ~350KB secara sinkronus dari CDN, tanpa menggunakan build pipeline Vite. Ini menyebabkan: (1) FOUC (Flash of Unstyled Content) di koneksi lambat, (2) ketergantungan pada CDN eksternal, (3) inconsistency dengan halaman lain yang sudah menggunakan compiled CSS.

**Rekomendasi:** Ubah ke `@vite(['resources/css/app.css'])` atau buat layout terpisah untuk halaman auth.

---

### 🟠 TINGGI — RoleMiddleware Redirect ke URL Non-Existent

**File:** [`RoleMiddleware.php:16`](file:///c:/laragon/www/cashier_shanghaejo/app/Http/Middleware/RoleMiddleware.php)

```php
// ❌ '/dashboard' tidak terdaftar di routes/web.php
return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses.');
```

**Dampak:** Ketika kasir mencoba akses route admin, akan diredirect ke `/dashboard` yang tidak ada rutenya, menyebabkan 404. Semestinya redirect ke `route('kasir.pesan.index')` atau `route('login')`.

**Rekomendasi:** Ubah redirect ke route yang sesuai berdasarkan peran user.

---

### 🟡 SEDANG — Tidak Ada CSRF Protection Verification untuk AJAX Endpoint

**File:** [`kasir/pesan/index.blade.php`](file:///c:/laragon/www/cashier_shanghaejo/resources/views/kasir/pesan/index.blade.php)

**Dampak:** Endpoint `GET /kasir/pesan/update-qty` dan `GET /kasir/pesan/remove` menggunakan HTTP `GET` untuk operasi yang mengubah state (cart). GET requests tidak membawa CSRF token secara default, namun Laravel juga tidak melindungi GET routes dengan CSRF, sehingga operasi state-mutating via GET bisa dipicu dari luar aplikasi (CSRF attack vector via link/image embedding).

**Rekomendasi:** Ubah `removeCart` dan `updateQty` ke `POST`/`PATCH`/`DELETE` dengan CSRF token. Frontend Alpine.js sudah menggunakan `fetch()` sehingga mudah diubah.

---

### 🟢 RENDAH — Password Validation Tidak Ada `confirmed` Rule di Store

**File:** [`UserController.php:36`](file:///c:/laragon/www/cashier_shanghaejo/app/Http/Controllers/Admin/UserController.php)

```php
'password' => 'required|min:6',
// ❌ Tidak ada konfirmasi password di form tambah user
```

**Dampak:** Admin bisa salah ketik password baru saat membuat user tanpa ada validasi konfirmasi. Tidak kritis karena admin yang membuat, namun best practice memerlukan konfirmasi.

---

## 2. Database / Schema

### 🟠 TINGGI — Migration `tb_penjualan` Punya Composite Primary Key yang Salah

**File:** [`2026_03_02_012736_create_tb_penjualan_table.php:24`](file:///c:/laragon/www/cashier_shanghaejo/database/migrations/2026_03_02_012736_create_tb_penjualan_table.php)

```php
// ❌ Composite PK dari id_cabang + id_user — seharusnya id_penjualan saja
$table->id('id_penjualan'); // sudah ada auto-increment PK
$table->primary(['id_cabang', 'id_user']); // konflik! dua primary key
```

**Dampak:** Ini akan menyebabkan migration error di MySQL (`Multiple primary key defined`) karena `id('id_penjualan')` sudah mendefinisikan PK. Di SQLite mungkin lolos, tapi di MySQL akan gagal. Selain itu, logika PK-nya secara bisnis salah — satu cabang + satu kasir hanya boleh punya satu transaksi seumur hidup.

**Rekomendasi:** Hapus baris `$table->primary(['id_cabang', 'id_user'])`. Jika diinginkan, buat `index` bukan `primary`.

---

### 🟠 TINGGI — Migration `tb_detail_penjualan` Tidak Punya Foreign Key

**File:** [`2026_03_02_012841_create_tb_detail_penjualan_table.php`](file:///c:/laragon/www/cashier_shanghaejo/database/migrations/2026_03_02_012841_create_tb_detail_penjualan_table.php)

```php
// ❌ Kolom id_penjualan dan id_menu disebutkan di primary() tapi tidak didefinisikan di tabel
$table->primary(['id_penjualan', 'id_menu']);
// Kolom id_penjualan dan id_menu tidak ada definisinya di atas
```

**Dampak:** Migration ini akan error karena kolom `id_penjualan` dan `id_menu` tidak pernah di-`define`. Tidak ada `foreignId('id_penjualan')` atau `foreignId('id_menu')`.

**Rekomendasi:** Tambahkan definisi kolom foreign key sebelum membuat composite primary key atau unique index.

---

### 🟠 TINGGI — Migration `tb_beli_bahan` Composite Primary Key Bermasalah

**File:** [`2026_03_02_013120_create_tb_beli_bahan_table.php:25`](file:///c:/laragon/www/cashier_shanghaejo/database/migrations/2026_03_02_013120_create_tb_beli_bahan_table.php)

```php
$table->id('id_pengeluaran'); // sudah primary key
$table->primary(['id_cabang']); // ❌ id_cabang tidak didefinisikan, konflik dengan PK di atas
```

**Dampak:** `id_cabang` tidak pernah didefinisikan di tabel ini tapi digunakan di `primary()`. Migration akan error.

---

### 🟡 SEDANG — Migration `tb_detail_penjualan_toping` Juga Punya Kolom Tidak Terdefinisi

**File:** [`2026_03_02_013025_create_tb_detail_penjualan_toping_table.php:18`](file:///c:/laragon/www/cashier_shanghaejo/database/migrations/2026_03_02_013025_create_tb_detail_penjualan_toping_table.php)

```php
// ❌ id_detail_penjualan dan id_toping tidak pernah didefinisikan di atas
$table->primary(['id_detail_penjualan', 'id_toping']);
```

---

### 🟡 SEDANG — Kolom `id_cabang` dan `id_user` Tidak Ada di Schema `tb_penjualan`

Kolom `id_cabang` dan `id_user` tidak ditemukan di definisi migration `tb_penjualan`, namun digunakan secara aktif di semua query (`where('id_cabang', ...)`, `where('id_user', ...)`). Ini mengindikasikan schema migration sudah berbeda dari database aktual yang mungkin dimodifikasi manual.

---

### 🟢 RENDAH — Tidak Ada Index pada Kolom Filter Umum

Kolom `created_at`, `id_cabang`, `status_transaksi` di `tb_penjualan` digunakan sangat sering di WHERE + ORDER BY clause namun tidak ada migration yang mendefinisikan composite index untuk performa.

---

## 3. Backend Logic

### 🔴 KRITIS — `dd()` Aktif (sudah disebut di Security #1)

Masuk ulang sebagai backend bug: kode `// return response()->json(...)` yang benar di-comment, memastikan setiap error checkout akan dump ke layar dan menghentikan eksekusi.

---

### 🟠 TINGGI — Laporan Controller: Admin Bisa Terkunci ke `id_cabang` User-nya

**File:** [`LaporanController.php:22`](file:///c:/laragon/www/cashier_shanghaejo/app/Http/Controllers/Admin/LaporanController.php)

```php
// ❌ Logika ini menyebabkan admin yang juga punya id_cabang tidak bisa lihat Semua Cabang
$id_cabang = $user->id_cabang ?: $request->input('id_cabang');
```

**Dampak:** Jika user admin punya `id_cabang` yang tidak null (misal admin yang merangkap kasir), laporan akan otomatis terkunci ke cabang tersebut — tidak bisa melihat laporan gabungan. Berbeda dengan `DashboardController` yang mengecek `$user->role === 'admin'` terlebih dahulu.

**Rekomendasi:** Samakan logika dengan `DashboardController`:
```php
$id_cabang = ($user->role === 'admin') 
    ? ($request->filled('id_cabang') ? $request->get('id_cabang') : null) 
    : $user->id_cabang;
```

---

### 🟠 TINGGI — `TargetController` Tidak Terdaftar di Routes

**File:** [`TargetController.php`](file:///c:/laragon/www/cashier_shanghaejo/app/Http/Controllers/Admin/TargetController.php) vs [`web.php`](file:///c:/laragon/www/cashier_shanghaejo/routes/web.php)

**Dampak:** `TargetController` (`index`, `store`) ada di filesystem dan diimport di controller, namun tidak ada `Route::resource('target', TargetController::class)` atau route apapun di `routes/web.php`. Fitur Target stok tidak dapat diakses melalui HTTP.

---

### 🟡 SEDANG — `BeliBahanController::destroy` Menghapus Data Permanent

**File:** [`BeliBahanController.php:98-103`](file:///c:/laragon/www/cashier_shanghaejo/app/Http/Controllers/Admin/BeliBahanController.php)

```php
public function destroy($id) {
    $data = BeliBahan::findOrFail($id);
    $data->delete(); // ❌ Hard delete tanpa audit trail
}
```

**Dampak:** Bertentangan dengan prinsip utama sistem: **Data Persistence / audit trail**. Pengeluaran bahan yang dihapus permanent akan merusak rekonsiliasi laporan keuangan historis.

**Rekomendasi:** Gunakan `SoftDelete` Eloquent atau implementasi flag `is_cancelled` seperti pada `tb_penjualan`.

---

### 🟡 SEDANG — Query `HOUR()`, `DAY()`, `MONTH()` MySQL-Specific

**Files:** [`DashboardController.php`](file:///c:/laragon/www/cashier_shanghaejo/app/Http/Controllers/Admin/DashboardController.php), [`LaporanController.php`](file:///c:/laragon/www/cashier_shanghaejo/app/Http/Controllers/Admin/LaporanController.php)

```php
DB::raw('HOUR(created_at) AS jam')
DB::raw('DAY(created_at) AS hari')
DB::raw('MONTH(created_at) AS bulan')
```

**Dampak:** Kode ini tidak kompatibel dengan SQLite yang didefinisikan sebagai default database di `.env`. Jika developer menjalankan dengan SQLite, semua grafik dashboard dan laporan akan error karena `HOUR()`, `DAY()`, `MONTH()` tidak didukung SQLite.

---

### 🟢 RENDAH — `ProfileController` Tidak Ada Validasi `current_password` untuk Kasir

**File:** [`ProfileController.php`](file:///c:/laragon/www/cashier_shanghaejo/app/Http/Controllers/Admin/ProfileController.php)

Route `kasir.profile.update` dan `kasir.profile.password` menggunakan controller yang sama dengan admin. Kasir tidak punya akses ke `route('admin.profile.password')` tapi route `kasir.profile.*` di `web.php` tidak mendefinisikan route password untuk kasir.

---

### 🟢 RENDAH — Tidak Ada Validation `max` pada Input Qty

**File:** [`PesanKasirController.php:38-41`](file:///c:/laragon/www/cashier_shanghaejo/app/Http/Controllers/Kasir/PesanKasirController.php)

```php
'qty' => 'required|integer|min:1'
// ❌ Tidak ada max, kasir bisa input qty 999999
```

---

## 4. Frontend / UI

### 🟠 TINGGI — Login Page Masih Menggunakan Tailwind CDN

Sudah dicatat di Security, berdampak langsung pada performa loading halaman login yang merupakan halaman pertama semua pengguna.

---

### 🟡 SEDANG — `riwayat/index.blade.php` Masih Pakai Inline Style Block

**File:** [`kasir/riwayat/index.blade.php:163-169`](file:///c:/laragon/www/cashier_shanghaejo/resources/views/kasir/riwayat/index.blade.php)

```html
<style>
    body { overflow: hidden !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    ...
    [x-cloak] { display: none !important; }
</style>
```

**Dampak:** Duplikat dari CSS yang sudah ada di `app.css`. Inline style tidak di-purge oleh build tool dan tidak bisa di-override dengan mudah. `body { overflow: hidden !important }` memaksa overflow hidden di semua viewport termasuk mobile, yang bermasalah untuk riwayat.

---

### 🟡 SEDANG — Riwayat Pesanan Tidak Responsif untuk Mobile

**File:** [`kasir/riwayat/index.blade.php`](file:///c:/laragon/www/cashier_shanghaejo/resources/views/kasir/riwayat/index.blade.php)

Tabel menggunakan `grid-cols-12` dengan 7 kolom tetap tanpa `overflow-x-auto` wrapper. Di mobile/tablet viewport, konten akan terpotong atau tertimpa.

**Rekomendasi:** Tambahkan `overflow-x-auto` pada wrapper tabel dan pertimbangkan card-based layout untuk mobile seperti yang sudah dilakukan di `pesan/index.blade.php`.

---

### 🟢 RENDAH — Sidebar Kasir Mempunyai Struktur HTML Duplikat

**File:** [`components/kasir/sidebar.blade.php`](file:///c:/laragon/www/cashier_shanghaejo/resources/views/components/kasir/sidebar.blade.php)

```html
<!-- Ada dua <div class="flex-1 overflow-y-auto py-4 px-3"> yang bersarang -->
<div class="flex-1 overflow-y-auto py-4 px-3">
  <ul class="space-y-1">
  {{-- Menu List --}}
  <div class="flex-1 overflow-y-auto py-4 px-3">  <!-- ❌ duplikat! -->
    <ul class="space-y-1">
```

**Dampak:** Rendering ganda div wrapper menyebabkan double padding/margin. Tidak kritis tapi berantakan secara layout.

---

### 🟢 RENDAH — Elemen Teks di Login Memiliki Typo di HTML

**File:** [`auth/login.blade.php:14`](file:///c:/laragon/www/cashier_shanghaejo/resources/views/auth/login.blade.php)

```html
<p class="...">Log in to your account"</p>
                                    <!-- ❌ Ada tanda kutip ekstra di akhir -->
```

---

### 🟢 RENDAH — Warna Tombol Login Tidak Konsisten dengan Design System

**File:** [`auth/login.blade.php`](file:///c:/laragon/www/cashier_shanghaejo/resources/views/auth/login.blade.php)

Login menggunakan `green-600` sementara seluruh sistem sudah menggunakan `teal-600` / `teal-700` sebagai primary color brand.

---

## 5. Testing Coverage

### 🟠 TINGGI — Hampir Nol Test Coverage untuk Business Logic

**Files:** [`tests/Feature/ExampleTest.php`](file:///c:/laragon/www/cashier_shanghaejo/tests/Feature/ExampleTest.php), [`tests/Unit/ExampleTest.php`](file:///c:/laragon/www/cashier_shanghaejo/tests/Unit/ExampleTest.php)

Hanya ada 2 test default Laravel yang tidak menguji fitur apapun dari sistem ini. Fitur kritikal berikut tidak punya test coverage sama sekali:

- Proses checkout dan pembuatan transaksi
- Logika harga snapshot
- Logika 2 topping gratis
- Update/remove cart
- RoleMiddleware authorization
- Login rate limiting
- Laporan harian/bulanan/tahunan

**Rekomendasi:** Prioritaskan feature test untuk checkout flow dan laporan keuangan.

---

### 🟢 RENDAH — Tidak Ada Seeder untuk Data Testing

**File:** [`database/seeders/`](file:///c:/laragon/www/cashier_shanghaejo/database/seeders)

Tidak ada `DatabaseSeeder` yang bermakna untuk mengisi data cabang, user, menu demo. Ini mempersulit onboarding developer baru dan QA testing dari awal.

---

## 6. Environment / Config

### 🟠 TINGGI — Database Default SQLite Tidak Kompatibel Penuh dengan Kode Produksi

**File:** [`.env`](file:///c:/laragon/www/cashier_shanghaejo/.env) + [`AGENTS.md`](file:///c:/laragon/www/cashier_shanghaejo/AGENTS.md)

Default `DB_CONNECTION=sqlite` namun kode menggunakan MySQL-specific raw queries (`HOUR()`, `DAY()`, `MONTH()`). Ini menyebabkan inkonsistensi antara environment development dan production, berpotensi menyembunyikan bug yang hanya muncul di MySQL.

**Rekomendasi:** Gunakan MySQL sejak development, atau gunakan Eloquent date helpers yang kompatibel lintas database.

---

### 🟡 SEDANG — Error Koneksi MySQL Tidak Ditangani dengan Pesan yang Jelas

Ketika MySQL tidak running, aplikasi menampilkan raw PDO error `SQLSTATE[HY000] [2002] No connection could be made`. Tidak ada error handling yang menampilkan pesan user-friendly.

**Rekomendasi:** Pastikan Laragon/XAMPP MySQL service running sebelum menjalankan `php artisan serve`. Tambahkan `DB_CONNECTION=mysql` dan pastikan service aktif.

---

## Prioritas Tindakan

| # | Item | Prioritas | Estimasi |
|---|---|---|---|
| 1 | Hapus `dd()` di PesanKasirController | 🔴 Segera | 5 menit |
| 2 | Fix Migration schema errors (tb_penjualan, tb_detail, tb_beli_bahan) | 🟠 Tinggi | 1 jam |
| 3 | Fix RoleMiddleware redirect URL | 🟠 Tinggi | 5 menit |
| 4 | Ubah login page ke Vite build | 🟠 Tinggi | 15 menit |
| 5 | Daftarkan TargetController ke routes | 🟠 Tinggi | 10 menit |
| 6 | Perbaiki logika filter cabang di LaporanController | 🟠 Tinggi | 10 menit |
| 7 | Soft delete untuk BeliBahanController | 🟠 Tinggi | 30 menit |
| 8 | Tambah feature test untuk checkout flow | 🟠 Tinggi | 2 jam |
| 9 | Ubah GET cart operations ke POST/DELETE | 🟡 Sedang | 30 menit |
| 10 | Perbaiki duplikat HTML di sidebar kasir | 🟢 Rendah | 5 menit |
| 11 | Fix typo HTML di login.blade.php | 🟢 Rendah | 2 menit |
| 12 | Seragamkan warna login ke teal brand | 🟢 Rendah | 5 menit |
| 13 | Buat Database Seeder | 🟢 Rendah | 1 jam |
