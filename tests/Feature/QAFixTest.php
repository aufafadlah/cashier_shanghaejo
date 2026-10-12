<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QAFixTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $kasirCabang1;
    private $kasirCabang2;
    private $cabang1Id;
    private $cabang2Id;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create 2 Cabang
        $this->cabang1Id = DB::table('tb_cabang')->insertGetId([
            'nama_cabang' => 'Cabang Tebet',
            'alamat' => 'Jl. Tebet Raya No. 1',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->cabang2Id = DB::table('tb_cabang')->insertGetId([
            'nama_cabang' => 'Cabang Kemang',
            'alamat' => 'Jl. Kemang Raya No. 2',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create Admin
        $adminId = DB::table('tb_user')->insertGetId([
            'name' => 'Super Admin',
            'username' => 'admin_qa',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => 1,
            'id_cabang' => $this->cabang1Id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->admin = User::find($adminId);

        // 3. Create Kasir Cabang 1
        $kasir1Id = DB::table('tb_user')->insertGetId([
            'name' => 'Kasir Tebet',
            'username' => 'kasir_tebet',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'is_active' => 1,
            'id_cabang' => $this->cabang1Id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->kasirCabang1 = User::find($kasir1Id);

        // 4. Create Kasir Cabang 2
        $kasir2Id = DB::table('tb_user')->insertGetId([
            'name' => 'Kasir Kemang',
            'username' => 'kasir_kemang',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'is_active' => 1,
            'id_cabang' => $this->cabang2Id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->kasirCabang2 = User::find($kasir2Id);
    }

    public function test_admin_search_cabang(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.cabang.index', ['search' => 'Tebet']));
        $response->assertStatus(200);
        $cabangs = $response->viewData('cabangs');
        $this->assertEquals(1, $cabangs->total());
        $this->assertEquals('Cabang Tebet', $cabangs->first()->nama_cabang);
    }

    public function test_admin_search_menu(): void
    {
        $menu1Id = DB::table('tb_menu')->insertGetId([
            'nama_menu' => 'Mie Goreng Spesial',
            'harga_menu' => 25000,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $menu2Id = DB::table('tb_menu')->insertGetId([
            'nama_menu' => 'Kwetiau Siram',
            'harga_menu' => 30000,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.menu.index', ['search' => 'Mie Goreng']));
        $response->assertStatus(200);
        $menus = $response->viewData('menus');
        $this->assertEquals(1, $menus->total());
        $this->assertEquals('Mie Goreng Spesial', $menus->first()->nama_menu);
    }

    public function test_admin_search_toping(): void
    {
        DB::table('tb_toping')->insert([
            'nama_toping' => 'Telur Puyuh',
            'harga_toping' => 5000,
            'is_active' => 1,
            'bisa_gratis' => 1,
        ]);
        DB::table('tb_toping')->insert([
            'nama_toping' => 'Baso Sapi',
            'harga_toping' => 8000,
            'is_active' => 1,
            'bisa_gratis' => 0,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.toping.index', ['search' => 'Puyuh']));
        $response->assertStatus(200);
        $topings = $response->viewData('topings');
        $this->assertEquals(1, $topings->total());
        $this->assertEquals('Telur Puyuh', $topings->first()->nama_toping);
    }

    public function test_checkout_rejects_cash_underpayment(): void
    {
        $cart = [
            [
                'id_menu' => 1,
                'nama_menu' => 'Mie Ayam',
                'harga_menu' => 20000,
                'qty' => 1,
                'subtotal' => 20000,
                'toppings' => []
            ]
        ];

        // Uang tunai kurang: total tagihan 20.000 tapi bayar 10.000
        $response = $this->actingAs($this->kasirCabang1)
            ->withSession(['cart' => $cart])
            ->postJson(route('kasir.pesan.checkout'), [
                'nama_pelanggan' => 'Budi Santoso',
                'jenis_pembayaran' => 'cash',
                'uang_tunai' => 10000,
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertDatabaseMissing('tb_penjualan', [
            'nama_pelanggan' => 'Budi Santoso'
        ]);
    }

    public function test_checkout_succeeds_with_valid_payment(): void
    {
        $cart = [
            [
                'id_menu' => 1,
                'nama_menu' => 'Mie Ayam',
                'harga_menu' => 20000,
                'qty' => 1,
                'subtotal' => 20000,
                'toppings' => []
            ]
        ];

        $response = $this->actingAs($this->kasirCabang1)
            ->withSession(['cart' => $cart])
            ->postJson(route('kasir.pesan.checkout'), [
                'nama_pelanggan' => 'Siti Rahma',
                'jenis_pembayaran' => 'cash',
                'uang_tunai' => 50000,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('tb_penjualan', [
            'nama_pelanggan' => 'Siti Rahma',
            'uang_tunai' => 50000,
            'kembalian' => 30000,
            'id_cabang' => $this->cabang1Id,
        ]);
    }

    public function test_idor_protection_print_struk_across_branches(): void
    {
        // Buat order milik Cabang 1 (Tebet)
        $penjualanId = DB::table('tb_penjualan')->insertGetId([
            'id_cabang' => $this->cabang1Id,
            'id_user' => $this->kasirCabang1->id_user,
            'nama_pelanggan' => 'Pelanggan Tebet',
            'total_awal' => 25000,
            'total_bayar' => 25000,
            'metode_pembayaran' => 'cash',
            'uang_tunai' => 30000,
            'kembalian' => 5000,
            'status_transaksi' => 'berhasil',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Kasir Cabang 1 BISA cetak nota miliknya
        $response1 = $this->actingAs($this->kasirCabang1)
            ->get(route('kasir.pesan.print', $penjualanId));
        $response1->assertStatus(200);

        // Kasir Cabang 2 TIDAK BISA cetak nota Cabang 1 (IDOR protection)
        $response2 = $this->actingAs($this->kasirCabang2)
            ->get(route('kasir.pesan.print', $penjualanId));
        $response2->assertStatus(403);
    }
}
