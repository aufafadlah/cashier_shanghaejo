<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;
    protected $table = 'tb_cabang';
    protected $primaryKey = 'id_cabang';
    public $incrementing = true;

    protected $fillable = [
        'nama_cabang',
        'alamat',
        'is_active'
        ];
        protected $casts = [
        'is_active' => 'boolean'
        ];
        public $timestamps = true;
        // Di App\Models\Cabang.php
public function menus(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
{
    return $this->belongsToMany(Menu::class, 'branch_menu', 'branch_id', 'menu_id')
                ->withPivot('harga_menu', 'is_active')
                ->withTimestamps();
}

}
