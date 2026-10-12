<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel audit log untuk mencatat setiap pesanan yang dibatalkan/dihapus oleh Kasir.
     * Data tidak pernah dihapus secara permanen dari tb_penjualan (hanya status berubah ke "gagal"),
     * tapi setiap aksi penghapusan dicatat di sini untuk monitoring Admin.
     */
    public function up(): void
    {
        Schema::create('tb_deletion_log', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedBigInteger('id_penjualan');   // ID transaksi yang dihapus
            $table->unsignedBigInteger('id_user');         // Kasir yang melakukan penghapusan
            $table->unsignedBigInteger('id_cabang');       // Cabang tempat penghapusan
            $table->string('no_struk', 50)->nullable();    // Nomor struk (untuk display)
            $table->string('nama_pelanggan', 100)->nullable();
            $table->decimal('total_bayar', 12, 2)->default(0);
            $table->string('alasan', 255)->nullable();     // Alasan penghapusan (opsional)
            $table->timestamps();

            $table->index('id_cabang');
            $table->index('id_user');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_deletion_log');
    }
};
