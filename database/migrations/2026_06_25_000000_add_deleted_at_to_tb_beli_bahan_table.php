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
        if (!Schema::hasColumn('tb_beli_bahan', 'deleted_at')) {
            Schema::table('tb_beli_bahan', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tb_beli_bahan', 'deleted_at')) {
            Schema::table('tb_beli_bahan', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
