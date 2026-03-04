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
    Schema::create('tb_beli_bahan', function (Blueprint $table) {
        // 1. Primary Key Utama (Sudah otomatis jadi PK)
        $table->id('id_pengeluaran');

        // 2. Buat kolom id_cabang untuk keperluan Join (Wajib ada!)
        $table->unsignedBigInteger('id_cabang');

        // 3. Kolom data lainnya
        $table->string('nama_barang');
        $table->integer('qty');
        $table->decimal('harga_satuan', 15, 2);
        $table->decimal('harga_total', 15, 2);
        $table->dateTime('tgl');
        $table->text('keterangan')->nullable();
        $table->string('supplier', 100)->nullable();
        $table->timestamps();

        // 4. Tambahkan Index (Agar proses Join ke tabel cabang lebih cepat)
        $table->index('id_cabang');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_beli_bahan');
    }
};
