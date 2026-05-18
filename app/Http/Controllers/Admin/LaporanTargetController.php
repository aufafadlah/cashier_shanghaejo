<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanTargetController extends Controller
{
    public function index(Request $request)
    {
        $cabangs = Cabang::all();

        // ✅ ambil daftar tahun unik dari database
        $years = Target::selectRaw('YEAR(tanggal) as year')
                        ->distinct()
                        ->orderBy('year', 'desc')
                        ->pluck('year');

        $query = Target::select(
                'tb_target.id_target',
                'tb_target.id_cabang',
                'tb_target.id_menu',
                'tb_target.tanggal',
                'tb_target.stok_target',
                'tb_target.over_target',
                'tb_target.created_at',
                'tb_target.updated_at',
                'tb_menu.nama_menu',
                'tb_cabang.nama_cabang',
                // ✅ hitung total penjualan yang sudah difilter cabang & tanggal
                DB::raw('COALESCE(SUM(tb_detail_penjualan.qty), 0) as total_penjualan')
            )
            ->leftJoin('tb_menu', 'tb_menu.id_menu', '=', 'tb_target.id_menu')
            ->leftJoin('tb_cabang', 'tb_cabang.id_cabang', '=', 'tb_target.id_cabang')
            // ✅ JOIN tb_penjualan dulu dengan filter cabang & tanggal
            ->leftJoin('tb_penjualan', function ($join) {
                $join->on('tb_penjualan.id_cabang', '=', 'tb_target.id_cabang')
                     ->on(DB::raw('DATE(tb_penjualan.created_at)'), '=', 'tb_target.tanggal');
            })
            // ✅ baru JOIN tb_detail_penjualan dari tb_penjualan yang sudah difilter
            ->leftJoin('tb_detail_penjualan', function ($join) {
                $join->on('tb_detail_penjualan.id_penjualan', '=', 'tb_penjualan.id_penjualan')
                     ->on('tb_detail_penjualan.id_menu', '=', 'tb_target.id_menu');
            })
            // ✅ groupBy lengkap sesuai semua kolom di SELECT
            ->groupBy(
                'tb_target.id_target',
                'tb_target.id_cabang',
                'tb_target.id_menu',
                'tb_target.tanggal',
                'tb_target.stok_target',
                'tb_target.over_target',
                'tb_target.created_at',
                'tb_target.updated_at',
                'tb_menu.nama_menu',
                'tb_cabang.nama_cabang'
            );

        // filter cabang
        if ($request->filled('id_cabang')) {
            $query->where('tb_target.id_cabang', $request->id_cabang);
        }

        // filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tb_target.tanggal', $request->tanggal);
        } else {
            if ($request->filled('bulan')) {
                $query->whereMonth('tb_target.tanggal', $request->bulan);
            }
            if ($request->filled('tahun')) {
                $query->whereYear('tb_target.tanggal', $request->tahun);
            }
        }

        $laporanTarget = $query->orderBy('tb_target.tanggal', 'desc')->get();

        // hitung persentase tiap item
        $laporanTarget->transform(function ($item) {
            $item->persen = $item->stok_target > 0
                ? round(($item->total_penjualan / $item->stok_target) * 100)
                : 0;
            return $item;
        });

        // ✅ summary persentase pakai rata-rata yang benar
        $summary = [
            'total_stok'  => $laporanTarget->sum('stok_target'),
            'total_over'  => $laporanTarget->sum('over_target'),
            'persentase'  => round($laporanTarget->avg('persen') ?? 0),
        ];

        return view('admin.laporantarget.index', compact(
            'cabangs',
            'laporanTarget',
            'summary',
            'years'
        ));
    }
}