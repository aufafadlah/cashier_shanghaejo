<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeliBahan extends Model
{
    protected $table = 'tb_beli_bahan';
    protected $primaryKey = 'id_pengeluaran';

    protected $fillable = [
        'id_cabang',
        'nama_barang',
        'qty',
        'harga_satuan',
        'harga_total',
        'tgl',
        'keterangan',
        'supplier'
    ];

    public $timestamps = true;

    // --- TAMBAHKAN DI SINI ---
    /**
     * Relasi ke model Cabang
     * id_cabang di tabel ini merujuk ke id_cabang di tabel tb_cabang
     */
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang', 'id_cabang');
    }
}