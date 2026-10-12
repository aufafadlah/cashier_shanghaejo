<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_penjualan', function (Blueprint $table) {
            $table->id('id_penjualan');
            $table->unsignedBigInteger('id_cabang');
            $table->unsignedBigInteger('id_user');
            $table->string('nama_pelanggan', 100)->nullable();
            $table->decimal('total_awal', 15, 2);
            $table->decimal('total_bayar', 15, 2);
            $table->enum('metode_pembayaran', ['cash','qris']);
            $table->decimal('uang_tunai', 15, 2)->nullable();
            $table->decimal('kembalian', 15, 2)->nullable();
            $table->enum('status_transaksi', ['berhasil','gagal']);
            $table->timestamps();
            
            $table->index('id_cabang');
            $table->index('id_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_penjualan');
    }
};
