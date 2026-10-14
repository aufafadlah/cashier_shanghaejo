<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cabang;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BranchDeactivationTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $cabang;
    private $kasir;
    private $menu;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Cabang
        $this->cabang = Cabang::create([
            'nama_cabang' => 'Cabang Uji Coba',
            'alamat'      => 'Jl. Uji No. 1',
            'is_active'   => 1,
        ]);

        // 2. Admin
        $this->admin = User::create([
            'name'      => 'Admin Test',
            'username'  => 'admin_test',
            'password'  => 'password',
            'role'      => 'admin',
            'is_active' => 1,
            'id_cabang' => $this->cabang->id_cabang,
        ]);

        // 3. Kasir
        $this->kasir = User::create([
            'name'      => 'Kasir Test',
            'username'  => 'kasir_test',
            'password'  => 'password',
            'role'      => 'kasir',
            'is_active' => 1,
            'id_cabang' => $this->cabang->id_cabang,
        ]);

        // 4. Menu terhubung ke cabang
        $this->menu = Menu::create([
            'nama_menu'   => 'Pisang Ijo Spesial',
            'harga_menu'  => 0,
            'status_menu' => 'aktif',
            'is_active'   => 1,
        ]);

        DB::table('branch_menu')->insert([
            'menu_id'    => $this->menu->id_menu,
            'branch_id'  => $this->cabang->id_cabang,
            'harga_menu' => 15000,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_deactivating_branch_cascades_inactive_status_to_menu_and_users()
    {
        $this->actingAs($this->admin);

        // Deactivate cabang
        $response = $this->put(route('admin.cabang.update', $this->cabang->id_cabang), [
            'nama_cabang' => 'Cabang Uji Coba',
            'alamat'      => 'Jl. Uji No. 1',
            'is_active'   => 0,
        ]);

        $response->assertRedirect(route('admin.cabang.index'));

        // Cabang is inactive
        $this->assertDatabaseHas('tb_cabang', [
            'id_cabang' => $this->cabang->id_cabang,
            'is_active' => 0,
        ]);

        // branch_menu for this branch is inactive
        $this->assertDatabaseHas('branch_menu', [
            'branch_id' => $this->cabang->id_cabang,
            'is_active' => 0,
        ]);

        // Kasir user for this branch is inactive
        $this->assertDatabaseHas('tb_user', [
            'id_user'   => $this->kasir->id_user,
            'is_active' => 0,
        ]);
    }

    public function test_kasir_cannot_login_when_branch_is_deactivated()
    {
        // Deactivate branch
        $this->cabang->update(['is_active' => 0]);

        $response = $this->post('/login', [
            'username' => 'kasir_test',
            'password' => 'password',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_logged_in_kasir_is_redirected_if_branch_is_deactivated()
    {
        // Log in kasir while active
        $this->actingAs($this->kasir);

        // Now branch gets deactivated
        $this->cabang->update(['is_active' => 0]);

        // Kasir attempts to visit POS page
        $response = $this->get(route('kasir.pesan.index'));

        // Should be logged out and redirected to login with error
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_reactivating_branch_restores_active_status_to_menu_and_users()
    {
        $this->actingAs($this->admin);

        // First deactivate
        $this->put(route('admin.cabang.update', $this->cabang->id_cabang), [
            'nama_cabang' => 'Cabang Uji Coba',
            'alamat'      => 'Jl. Uji No. 1',
            'is_active'   => 0,
        ]);

        // Then reactivate
        $response = $this->put(route('admin.cabang.update', $this->cabang->id_cabang), [
            'nama_cabang' => 'Cabang Uji Coba',
            'alamat'      => 'Jl. Uji No. 1',
            'is_active'   => 1,
        ]);

        $response->assertRedirect(route('admin.cabang.index'));

        // Cabang is active
        $this->assertDatabaseHas('tb_cabang', [
            'id_cabang' => $this->cabang->id_cabang,
            'is_active' => 1,
        ]);

        // branch_menu is active again
        $this->assertDatabaseHas('branch_menu', [
            'branch_id' => $this->cabang->id_cabang,
            'is_active' => 1,
        ]);

        // Kasir is active again
        $this->assertDatabaseHas('tb_user', [
            'id_user'   => $this->kasir->id_user,
            'is_active' => 1,
        ]);
    }

    public function test_cannot_assign_inactive_branch_in_user_or_pembelian()
    {
        $this->actingAs($this->admin);
        $this->cabang->update(['is_active' => 0]);

        // Test User Store with inactive branch
        $userResponse = $this->post(route('admin.user.store'), [
            'id_cabang' => $this->cabang->id_cabang,
            'name'      => 'Kasir Baru',
            'username'  => 'kasir_baru',
            'password'  => 'password123',
            'role'      => 'kasir',
            'is_active' => 1,
        ]);
        $userResponse->assertSessionHas('error');
        $this->assertDatabaseMissing('tb_user', ['username' => 'kasir_baru']);

        // Test Pembelian Store with inactive branch
        $pembelianResponse = $this->post(route('admin.pembelian.store'), [
            'id_cabang'    => $this->cabang->id_cabang,
            'nama_barang'  => 'Gula Pasir',
            'qty'          => 5,
            'harga_satuan' => 12000,
            'tgl'          => '2026-10-14',
            'supplier'     => 'Toko Sembako',
        ]);
        $pembelianResponse->assertSessionHas('error');
        $this->assertDatabaseMissing('tb_beli_bahan', ['nama_barang' => 'Gula Pasir']);
    }

    public function test_anti_double_checkout_prevents_duplicate_transactions()
    {
        $this->actingAs($this->kasir);

        // Put cart into session
        session(['cart' => [
            [
                'id_menu'    => $this->menu->id_menu,
                'nama_menu'  => $this->menu->nama_menu,
                'harga_menu' => 15000,
                'qty'        => 1,
                'subtotal'   => 15000,
                'toppings'   => [],
            ]
        ]]);

        // First checkout request
        $response1 = $this->postJson(route('kasir.pesan.checkout'), [
            'nama_pelanggan'   => 'Budi',
            'jenis_pembayaran' => 'CASH',
            'uang_tunai'       => 20000,
        ]);
        $response1->assertStatus(200);
        $response1->assertJson(['success' => true]);

        // Second duplicate request (cart already pulled)
        $response2 = $this->postJson(route('kasir.pesan.checkout'), [
            'nama_pelanggan'   => 'Budi',
            'jenis_pembayaran' => 'CASH',
            'uang_tunai'       => 20000,
        ]);
        $response2->assertStatus(400);
        $response2->assertJson(['success' => false]);

        // Exactly 1 transaction saved
        $this->assertEquals(1, DB::table('tb_penjualan')->count());
    }
}
