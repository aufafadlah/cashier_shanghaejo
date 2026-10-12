<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_toping', function (Blueprint $table) {
            // Flag apakah topping ini bisa dimasukkan ke promo "2 topping gratis"
            $table->boolean('bisa_gratis')->default(true)->after('is_active')
                  ->comment('1 = bisa masuk promo gratis, 0 = selalu berbayar');
        });
    }

    public function down(): void
    {
        Schema::table('tb_toping', function (Blueprint $table) {
            $table->dropColumn('bisa_gratis');
        });
    }
};
