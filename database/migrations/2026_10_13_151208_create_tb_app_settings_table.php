<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name', 100)->default('ShangHaejoCicio');
            $table->string('logo_path', 255)->nullable();   // Path relatif di public/
            $table->string('instagram', 100)->nullable();
            $table->string('telepon', 50)->nullable();
            $table->timestamps();
        });

        // Seed 1 baris default (hanya 1 baris konfigurasi global)
        DB::table('tb_app_settings')->insert([
            'brand_name' => 'ShangHaejoCicio',
            'logo_path'  => 'img/shanghaejo.jpg',
            'instagram'  => '@shanghaejo_cicio',
            'telepon'    => '+62 88905779517',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_app_settings');
    }
};
