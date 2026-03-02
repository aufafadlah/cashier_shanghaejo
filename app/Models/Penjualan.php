<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'tb_penjualan';
    protected $primaryKey = 'id_penjualan';

    public function detail()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_penjualan');
    }
}