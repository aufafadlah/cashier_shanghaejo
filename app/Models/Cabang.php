<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Menu;

/**
 * App\Models\Cabang
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Cabang extends Model
{
    use HasFactory;

    protected $table = 'tb_cabang';
    protected $primaryKey = 'id_cabang';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nama_cabang',
        'alamat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi Many-to-Many ke model Menu melalui branch_menu
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'branch_menu', 'branch_id', 'menu_id')
                    ->withPivot('harga_menu', 'is_active')
                    ->withTimestamps();
    }
}
