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
    Schema::create('tb_detail_penjualan_toping', function (Blueprint $table) {
        //Primary Key
        $table->id('id_detail_penjualan_toping');

        // 2. Tambahkan kolom yang dibutuhkan untuk JOIN (Wajib ada!)
        $table->unsignedBigInteger('id_detail_penjualan');
        $table->unsignedBigInteger('id_toping');

        // 3. Kolom data lainnya
        $table->string('nama_toping', 100);
        $table->decimal('harga_toping', 15, 2);
        
        $table->timestamps();

        // 4. Tambahkan Index (untuk join)
        $table->index('id_detail_penjualan');
        $table->index('id_toping');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_detail_penjualan_toping');
    }
};
