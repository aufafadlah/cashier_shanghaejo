<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'tb_menu';
    protected $primaryKey = 'id_menu';

    protected $fillable = [
        'nama_menu',
        'harga_menu',
        'is_active'
        ];
        protected $casts = [
        'is_active' => 'boolean'
        ];
        public $timestamps = true;
}