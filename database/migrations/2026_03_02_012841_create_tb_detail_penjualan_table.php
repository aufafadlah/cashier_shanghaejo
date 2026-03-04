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
    Schema::create('tb_detail_penjualan', function (Blueprint $table) {
        // 1. Primary Key utama untuk tabel ini
        $table->id('id_detail_penjualan');

        // 2. Buat kolom untuk Join (Wajib didefinisikan dulu!)
        $table->unsignedBigInteger('id_penjualan'); 
        $table->unsignedBigInteger('id_menu');

        // 3. Kolom data lainnya
        $table->integer('qty');
        $table->decimal('harga_snapshot', 15, 2);
        $table->decimal('subtotal', 15, 2);
        
        $table->timestamps();

        // 4. (Opsional) Tambahkan Index agar JOIN super cepat
        $table->index('id_penjualan');
        $table->index('id_menu');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_detail_penjualan');
    }
};
