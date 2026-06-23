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
    Schema::create('branch_menu', function (Blueprint $table) {
        $table->id();
        
        // Menghubungkan ke tabel tb_menu (id_menu)
        $table->foreignId('menu_id')->constrained('tb_menu', 'id_menu')->onDelete('cascade');
        
        // Menghubungkan ke tabel tb_cabang (sesuaikan nama tabel 'tb_cabang' dan primary key 'id_cabang' dengan database tim kamu)
        $table->foreignId('branch_id')->constrained('tb_cabang', 'id_cabang')->onDelete('cascade');
        
        // Di sini kita taruh harganya agar bisa berbeda tiap cabang!
        $table->decimal('harga_menu', 15, 2); 
        $table->boolean('is_active')->default(true); // Status menu aktif/tidak di cabang ini
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_menu');
    }
};
