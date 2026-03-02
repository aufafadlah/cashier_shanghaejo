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
       Schema::create('tb_menu', function (Blueprint $table) {
    $table->id('id_menu');
    $table->string('nama_menu', 100);
    $table->decimal('harga_menu', 15, 2);
    $table->enum('status_menu', ['aktif','nonaktif']);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_menu');
    }
};
