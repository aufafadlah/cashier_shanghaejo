<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cabang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Pastikan minimal ada 1 cabang aktif yang valid
        $cabang = Cabang::first();
        if (!$cabang) {
            $cabang = Cabang::create([
                'nama_cabang' => 'Cabang Pusat',
                'alamat'      => 'Jl. Pusat Bisnis No. 1',
                'is_active'   => true,
            ]);
        }

        // 2. Buat akun Administrator dengan id_cabang yang valid (tidak boleh NULL sesuai skema tb_user)
        $admin = User::where('username', 'admin')->first();
        if (!$admin) {
            User::create([
                'name'      => 'Administrator',
                'username'  => 'admin',
                'password'  => 'password',
                'role'      => 'admin',
                'is_active' => true,
                'id_cabang' => $cabang->id_cabang,
            ]);
        } elseif (empty($admin->id_cabang) || !Cabang::where('id_cabang', $admin->id_cabang)->exists()) {
            $admin->update(['id_cabang' => $cabang->id_cabang]);
        }

        // 3. Buat akun Kasir default jika belum ada
        $kasir = User::where('username', 'kasir')->first();
        if (!$kasir) {
            User::create([
                'name'      => 'Kasir Utama',
                'username'  => 'kasir',
                'password'  => 'password',
                'role'      => 'kasir',
                'is_active' => true,
                'id_cabang' => $cabang->id_cabang,
            ]);
        } elseif (empty($kasir->id_cabang) || !Cabang::where('id_cabang', $kasir->id_cabang)->exists()) {
            $kasir->update(['id_cabang' => $cabang->id_cabang]);
        }
    }
}

