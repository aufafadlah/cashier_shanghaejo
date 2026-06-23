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
            $table->id('id_menu'); // Primary key custom
            $table->string('nama_menu', 100);
            $table->decimal('harga_menu', 15, 2)->default(0); // Sesuai Model & phpMyAdmin
            $table->string('status_menu', 20)->default('aktif'); // Sesuai Model & phpMyAdmin
            $table->boolean('is_active')->default(true); // Sesuai Model & phpMyAdmin
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