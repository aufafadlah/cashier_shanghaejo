<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    protected $table = 'tb_detail_penjualan';
    protected $primaryKey = 'id_detail_penjualan';
    public $timestamps = false;

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }
}