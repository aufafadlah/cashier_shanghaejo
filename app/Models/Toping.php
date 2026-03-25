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
        'is_active'
        ];
        protected $casts = [
        'is_active' => 'boolean'
        ];
        public $timestamps = true;
}