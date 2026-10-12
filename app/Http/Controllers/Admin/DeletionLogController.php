<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeletionLogController extends Controller
{
    public function index(Request $request)
    {
        $id_cabang = session('admin_cabang_filter');
        if ($request->filled('id_cabang')) {
            $id_cabang = $request->input('id_cabang');
        }

        $query = DB::table('tb_deletion_log')
            ->leftJoin('tb_user', 'tb_deletion_log.id_user', '=', 'tb_user.id_user')
            ->leftJoin('tb_cabang', 'tb_deletion_log.id_cabang', '=', 'tb_cabang.id_cabang')
            ->select(
                'tb_deletion_log.*',
                'tb_user.name as nama_kasir',
                'tb_cabang.nama_cabang'
            );

        if ($id_cabang) {
            $query->where('tb_deletion_log.id_cabang', $id_cabang);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('tb_deletion_log.no_struk', 'like', "%{$search}%")
                  ->orWhere('tb_deletion_log.nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('tb_user.name', 'like', "%{$search}%");
            });
        }

        $totalHapus = (clone $query)->count();
        $totalNominal = (clone $query)->sum('total_bayar');

        $logs = $query->orderByDesc('tb_deletion_log.created_at')
            ->paginate(10)
            ->withQueryString();

        $cabangs = DB::table('tb_cabang')->orderBy('nama_cabang')->get();

        return view('admin.deletion_log.index', compact('logs', 'totalHapus', 'totalNominal', 'cabangs', 'id_cabang'));
    }

    public function exportExcel(Request $request)
    {
        $id_cabang = session('admin_cabang_filter');
        if ($request->filled('id_cabang')) {
            $id_cabang = $request->input('id_cabang');
        }

        $query = DB::table('tb_deletion_log')
            ->leftJoin('tb_user', 'tb_deletion_log.id_user', '=', 'tb_user.id_user')
            ->leftJoin('tb_cabang', 'tb_deletion_log.id_cabang', '=', 'tb_cabang.id_cabang')
            ->select(
                'tb_deletion_log.*',
                'tb_user.name as nama_kasir',
                'tb_cabang.nama_cabang'
            );

        if ($id_cabang) {
            $query->where('tb_deletion_log.id_cabang', $id_cabang);
        }

        $logs = $query->orderByDesc('tb_deletion_log.created_at')->get();

        $filename = "Riwayat_Penghapusan_Kasir_" . date('Ymd_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($handle, ['No', 'Waktu Dihapus', 'No. Struk', 'Nama Pelanggan', 'Total Bayar (Rp)', 'Kasir', 'Cabang', 'Alasan']);

            foreach ($logs as $i => $row) {
                fputcsv($handle, [
                    $i + 1,
                    date('d/m/Y H:i:s', strtotime($row->created_at)),
                    $row->no_struk ?? '-',
                    $row->nama_pelanggan ?? '-',
                    (int) $row->total_bayar,
                    $row->nama_kasir ?? '-',
                    $row->nama_cabang ?? '-',
                    $row->alasan ?? 'Dibatalkan oleh kasir'
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
