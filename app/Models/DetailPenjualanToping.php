<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualanToping extends Model
{
    public function detailPenjualan()
{
    return $this->belongsTo(DetailPenjualan::class, 'id_detail_penjualan');
}

public function toping()
{
    return $this->belongsTo(Toping::class, 'id_toping');
}
}
