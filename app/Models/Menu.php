<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'tb_menu';
    protected $primaryKey = 'id_menu';

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_menu');
    }
}