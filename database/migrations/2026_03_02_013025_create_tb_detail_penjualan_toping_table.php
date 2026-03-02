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
    $table->id('id_detail_penjualan_toping');
    $table->foreignId('id_detail_penjualan')
          ->constrained('tb_detail_penjualan','id_detail_penjualan')
          ->cascadeOnDelete();
    $table->foreignId('id_toping')
          ->constrained('tb_toping','id_toping');
    $table->string('nama_toping', 100);
    $table->decimal('harga_toping', 15, 2);
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
