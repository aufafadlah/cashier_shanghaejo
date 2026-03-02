<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeliBahan extends Model
{
    public function cabang()
{
    return $this->belongsTo(Cabang::class, 'id_cabang');
}
}
