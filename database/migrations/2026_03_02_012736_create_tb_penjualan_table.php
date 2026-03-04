<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_penjualan', function (Blueprint $table) {
            // 1. Definisikan Kolom Utama
            $table->bigIncrements('id_penjualan'); 
            
            // 2. Definisikan Kolom yang akan dipakai buat Join (Wajib ada!)
            $table->unsignedBigInteger('id_cabang'); 
            $table->unsignedBigInteger('id_user');

            // 3. Kolom Data Lainnya
            $table->string('nama_pelanggan', 100)->nullable();
            $table->decimal('total_awal', 15, 2);
            $table->decimal('total_bayar', 15, 2);
            $table->enum('metode_pembayaran', ['cash', 'qris']);
            $table->decimal('uang_tunai', 15, 2)->nullable();
            $table->decimal('kembalian', 15, 2)->nullable();
            $table->enum('status_transaksi', ['berhasil', 'gagal']);
            
            $table->timestamps();

            // CATATAN: Karena 'id_penjualan' sudah otomatis jadi Primary Key (bigIncrements),
            // Kamu tidak perlu lagi menulis $table->primary(['id_cabang', 'id_user']).
            // Kalau kamu mau id_cabang & id_user cepat saat di-join, tambahkan index saja:
            $table->index('id_cabang');
            $table->index('id_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_penjualan');
    }
};