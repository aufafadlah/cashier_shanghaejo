<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $table = 'tb_target';
    protected $primaryKey = 'id_target';

    protected $fillable = [
        'id_cabang',
        'id_menu',
        'tanggal',
        'stok_target',
        'over_target',



        
    ];

    public $timestamps = true;
}
