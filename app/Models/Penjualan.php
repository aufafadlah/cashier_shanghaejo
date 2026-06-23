<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'tb_penjualan';
    protected $primaryKey = 'id_penjualan';

    protected $fillable = [
        'id_cabang',
        'id_user',
        'nama_pelanggan',
        'total_awal',
        'total_bayar',
        'metode_pembayaran',
        'uang_tunai',
        'kembalian',
        'status_transaksi'
    ];

    public $timestamps = true;

    // Relasi ke tabel user (Kasir yang melakukan transaksi)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Relasi ke tabel cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang', 'id_cabang');
    }

    // Relasi ke detail penjualan
    public function details()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_penjualan', 'id_penjualan');
    }
        public function scopeFilterCabang($query)
{
    return $query->when(request('id_cabang'), function ($q) {
        $q->where('id_cabang', request('id_cabang'));
    });
}
}