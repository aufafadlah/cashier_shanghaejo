<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toping extends Model
{
    protected $table = 'tb_toping';
    protected $primaryKey = 'id_toping';

    protected $fillable = [
        'nama_toping',
        'harga_toping'
    ];

    public $timestamps = false;
}