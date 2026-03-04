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
}