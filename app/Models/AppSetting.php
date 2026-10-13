<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $table = 'tb_app_settings';
    protected $primaryKey = 'id';

    protected $fillable = [
        'brand_name',
        'logo_path',
        'instagram',
        'telepon',
    ];

    /**
     * Ambil baris pengaturan global (selalu id = 1).
     */
    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'brand_name' => 'ShangHaejoCicio',
                'logo_path'  => 'img/shanghaejo.jpg',
                'instagram'  => '@shanghaejo_cicio',
                'telepon'    => '+62 88905779517',
            ]
        );
    }
}
