<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    protected $table = 'tb_user';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama',
        'username',
        'id_cabang',
        'password',
        'role',

        'is_active'
        ];
        protected $casts = [
        'is_active' => 'boolean'
        ];
        public $timestamps = true;
}
