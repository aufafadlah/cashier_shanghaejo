<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    // 1. Nama tabel induk di database
    protected $table = 'tb_menu';

    // 2. Primary key custom yang kamu pakai
    protected $primaryKey = 'id_menu';

    // 3. Kolom wajib tb_menu yang sudah disesuaikan dengan phpMyAdmin asli
    protected $fillable = [
        'nama_menu',
        'harga_menu',
        'status_menu',
        'is_active',
    ];

    /**
     * Relasi Many-to-Many ke model Cabang.
     * Menghubungkan tb_menu ke tb_cabang melalui tabel jembatan branch_menu.
     */
    public function cabangs(): BelongsToMany
    {
        return $this->belongsToMany(
            Cabang::class, 
            'branch_menu', // TTP DI SINI: Nama tabel jembatan di database kamu
            'menu_id',     // Foreign key merujuk ke tb_menu
            'branch_id'    // Foreign key merujuk ke tb_cabang
        )
        // Mengambil kolom dinamis (harga unik & status aktif) dari tabel jembatan branch_menu
        ->withPivot('harga_menu', 'is_active')
        ->withTimestamps();
    }

    /**
     * Filter menu berdasarkan cabang yang terdaftar di pivot branch_menu.
     */
    public function scopeFilterCabang($query, $branchId = null)
    {
        $id = $branchId ?: request('id_cabang');
        return $query->when($id, function ($q) use ($id) {
            $q->whereHas('cabangs', function ($sub) use ($id) {
                $sub->where('tb_cabang.id_cabang', $id);
            });
        });
    }
}