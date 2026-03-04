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
        Schema::create('tb_target', function (Blueprint $table) {
    $table->id('id_target');
    $table->date('tanggal');
    $table->integer('stok_target');
    $table->integer('over_target')->default(0);
    $table->timestamps();
    $table->primary(['id_cabang', 'id_menu']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_target');
    }
};
