<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toping extends Model
{
    protected $table = 'tb_toping';
    protected $primaryKey = 'id_toping';

    protected $fillable = [
        'nama_toping',
        'harga_toping',
        'is_active',
        'bisa_gratis',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'bisa_gratis' => 'boolean',
    ];
    public $timestamps = false;
}