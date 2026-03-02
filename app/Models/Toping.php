<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toping extends Model
{
    protected $table = 'tb_toping';
    protected $primaryKey = 'id_toping';

    public function detailToping()
    {
        return $this->hasMany(DetailPenjualanToping::class, 'id_toping');
    }
}
