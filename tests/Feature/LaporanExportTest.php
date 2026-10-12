<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LaporanExportTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $cabangId;

    protected function setUp(): void
    {
        parent::setUp();

        $cabang = DB::table('tb_cabang')->first();
        if (!$cabang) {
            $this->cabangId = DB::table('tb_cabang')->insertGetId([
                'nama_cabang' => 'Cabang Test',
                'alamat' => 'Alamat Test',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $this->cabangId = $cabang->id_cabang;
        }

        $user = DB::table('tb_user')->where('role', 'admin')->first();
        if (!$user) {
            $userId = DB::table('tb_user')->insertGetId([
                'name' => 'Admin Test',
                'username' => 'admintest_' . time(),
                'id_cabang' => $this->cabangId,
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->admin = User::find($userId);
        } else {
            $this->admin = User::find($user->id_user);
        }
    }

    public function test_laporan_export_excel_harian(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.export.excel', [
            'tab' => 'harian',
            'single_date' => now()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('LAPORAN PENJUALAN - SHANGHAEJO', $response->streamedContent());
        $this->assertStringContainsString('Harian - ', $response->streamedContent());
    }

    public function test_laporan_export_excel_mingguan(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.export.excel', [
            'tab' => 'mingguan',
            'tanggal' => now()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('LAPORAN PENJUALAN - SHANGHAEJO', $response->streamedContent());
        $this->assertStringContainsString('Mingguan (', $response->streamedContent());
    }

    public function test_laporan_export_excel_bulanan_and_tahunan(): void
    {
        $resBulan = $this->actingAs($this->admin)->get(route('admin.laporan.export.excel', [
            'tab' => 'bulanan',
            'tahun_bulanan' => now()->year,
        ]));
        $resBulan->assertStatus(200);
        $this->assertStringContainsString('Bulanan - Tahun ' . now()->year, $resBulan->streamedContent());

        $resTahun = $this->actingAs($this->admin)->get(route('admin.laporan.export.excel', [
            'tab' => 'tahunan',
        ]));
        $resTahun->assertStatus(200);
        $this->assertStringContainsString('Tahunan (', $resTahun->streamedContent());
    }

    public function test_laporan_export_pdf_views(): void
    {
        // Harian PDF
        $resHarian = $this->actingAs($this->admin)->get(route('admin.laporan.export.pdf', [
            'tab' => 'harian',
            'single_date' => now()->toDateString(),
        ]));
        $resHarian->assertStatus(200);
        $resHarian->assertSee('Harian - ');
        $resHarian->assertSee('ShangHaejo');

        // Mingguan PDF
        $resMinggu = $this->actingAs($this->admin)->get(route('admin.laporan.export.pdf', [
            'tab' => 'mingguan',
            'tanggal' => now()->toDateString(),
        ]));
        $resMinggu->assertStatus(200);
        $resMinggu->assertSee('Mingguan (');

        // Bulanan PDF
        $resBulan = $this->actingAs($this->admin)->get(route('admin.laporan.export.pdf', [
            'tab' => 'bulanan',
            'tahun_bulanan' => now()->year,
        ]));
        $resBulan->assertStatus(200);
        $resBulan->assertSee('Bulanan - Tahun ' . now()->year);

        // Tahunan PDF
        $resTahun = $this->actingAs($this->admin)->get(route('admin.laporan.export.pdf', [
            'tab' => 'tahunan',
        ]));
        $resTahun->assertStatus(200);
        $resTahun->assertSee('Tahunan (');
    }

    public function test_laporan_pembelian_export_filters(): void
    {
        $resExcel = $this->actingAs($this->admin)->get(route('admin.laporanpembelian.export.excel', [
            'tab' => 'mingguan',
            'tanggal' => now()->toDateString(),
        ]));
        $resExcel->assertStatus(200);
        $this->assertStringContainsString('Mingguan (', $resExcel->streamedContent());

        $resPdf = $this->actingAs($this->admin)->get(route('admin.laporanpembelian.export.pdf', [
            'tab' => 'harian',
            'single_date' => now()->toDateString(),
        ]));
        $resPdf->assertStatus(200);
        $resPdf->assertSee('Harian - ');
    }

    public function test_laporan_penjualan_tahunan_pagination_maintains_tab(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index', [
            'page_t' => 2,
        ]));

        $response->assertStatus(200);
        // Pastikan Alpine data tab terinisialisasi sebagai 'tahunan'
        $response->assertSee("tab: 'tahunan'", false);
    }
}

