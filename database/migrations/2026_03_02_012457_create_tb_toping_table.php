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
        Schema::create('tb_toping', function (Blueprint $table) {
    $table->id('id_toping');
    $table->string('nama_toping', 100);
    $table->decimal('harga_toping', 15, 2);
    $table->boolean('is_active')->default(true);
   
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_toping');
    }
};
