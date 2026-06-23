<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeliBahan extends Model
{
    protected $table = 'tb_beli_bahan';
    protected $primaryKey = 'id_pengeluaran';

    protected $fillable = [
        'id_cabang',
        'nama_barang',
        'qty',
        'harga_satuan',
        'harga_total',
        'tgl',
        'keterangan',
        'supplier'
    ];

    public $timestamps = true;


    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang', 'id_cabang');
    }
        public function scopeFilterCabang($query)
{
    return $query->when(request('id_cabang'), function ($q) {
        $q->where('id_cabang', request('id_cabang'));
    });
}
}