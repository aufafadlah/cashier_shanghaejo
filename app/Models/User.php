<?php

namespace App\Models;

// 1. TAMBAHKAN IMPORT INI
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

// 2. UBAH 'extends Model' MENJADI 'extends Authenticatable'
class User extends Authenticatable 
{
    use HasFactory, Notifiable;

    protected $table = 'tb_user';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'name',
        'username',
        'id_cabang',
        'password',
        'role',
        'is_active'
    ];

    // 3. TAMBAHKAN CASTS UNTUK PASSWORD AGAR TER-ENKRIPSI OTOMATIS
    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'hashed', // Sangat penting untuk fitur Ubah Password
    ];

    public $timestamps = true;

    // 4. TAMBAHKAN RELASI CABANG (Opsional tapi berguna untuk tampilan Profil)
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