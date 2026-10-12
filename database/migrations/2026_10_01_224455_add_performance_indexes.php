<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah index pada kolom yang sering dipakai di WHERE, JOIN, ORDER BY
     * untuk meningkatkan performa query laporan, dashboard, dan POS.
     */
    public function up(): void
    {
        // Index pada tb_penjualan — tabel paling sering di-query
        Schema::table('tb_penjualan', function (Blueprint $table) {
            // Composite index untuk filter laporan: status + tanggal + cabang
            $table->index(['status_transaksi', 'created_at'], 'idx_penjualan_status_created');
            $table->index(['id_cabang', 'created_at'], 'idx_penjualan_cabang_created');
            $table->index(['id_user'], 'idx_penjualan_user');
        });

        // Index pada tb_beli_bahan — laporan pengeluaran
        Schema::table('tb_beli_bahan', function (Blueprint $table) {
            $table->index(['id_cabang', 'tgl'], 'idx_beli_cabang_tgl');
        });

        // Index pada tb_detail_penjualan — join saat detail transaksi
        Schema::table('tb_detail_penjualan', function (Blueprint $table) {
            $table->index(['id_penjualan'], 'idx_detail_penjualan_id');
        });

        // Index pada branch_menu — query kasir POS
        Schema::table('branch_menu', function (Blueprint $table) {
            $table->index(['branch_id', 'is_active'], 'idx_branch_menu_active');
        });
    }

    public function down(): void
    {
        Schema::table('tb_penjualan', function (Blueprint $table) {
            $table->dropIndex('idx_penjualan_status_created');
            $table->dropIndex('idx_penjualan_cabang_created');
            $table->dropIndex('idx_penjualan_user');
        });
        Schema::table('tb_beli_bahan', function (Blueprint $table) {
            $table->dropIndex('idx_beli_cabang_tgl');
        });
        Schema::table('tb_detail_penjualan', function (Blueprint $table) {
            $table->dropIndex('idx_detail_penjualan_id');
        });
        Schema::table('branch_menu', function (Blueprint $table) {
            $table->dropIndex('idx_branch_menu_active');
        });
    }
};
