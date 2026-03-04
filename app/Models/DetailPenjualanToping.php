<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualanToping extends Model
{
    protected $table = 'tb_detail_penjualan_toping';
    protected $primaryKey = 'id_detail_penjualan_toping';

    protected $fillable = [
        'id_detail_penjualan',
        'id_toping',
        'nama_toping',
        'harga_toping'
    ];

    public $timestamps = false;
}
