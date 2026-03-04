<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_target', function (Blueprint $table) {
            // 1. Primary Key Utama
            $table->id('id_target');

            // 2. Buat kolom untuk JOIN (Wajib didefinisikan dulu!)
            $table->unsignedBigInteger('id_cabang');
            $table->unsignedBigInteger('id_menu');

            // 3. Kolom Data
            $table->date('tanggal');
            $table->integer('stok_target');
            $table->integer('over_target')->default(0);
            $table->timestamps();

            // 4. Tambahkan Index supaya JOIN ngebut (Gantiin fungsi primary tadi)
            $table->index('id_cabang');
            $table->index('id_menu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_target');
    }
};