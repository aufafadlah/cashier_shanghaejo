<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    public function cabang()
{
    return $this->belongsTo(Cabang::class, 'id_cabang');
}

public function menu()
{
    return $this->belongsTo(Menu::class, 'id_menu');
}
}
