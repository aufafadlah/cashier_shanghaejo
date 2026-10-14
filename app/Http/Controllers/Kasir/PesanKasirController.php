<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\AppSetting;

class PesanKasirController extends Controller
{
public function index()
{
    // Pertahankan cart dari session — jangan clear saat kasir kembali ke halaman POS
    $cart = session()->get('cart', []);

    $id_cabang = Auth::user()->id_cabang;

    $menus = DB::table('tb_menu')
        ->join('branch_menu', 'tb_menu.id_menu', '=', 'branch_menu.menu_id')
        ->where('branch_menu.branch_id', $id_cabang) 
        ->where('branch_menu.is_active', 1)
        ->select(
            'tb_menu.id_menu', 
            'tb_menu.nama_menu', 
            'branch_menu.harga_menu'
        ) 
        ->get();

    $toppings = DB::table('tb_toping')
        ->where('is_active', 1)
        ->get();

    // Ambil nama cabang untuk ditampilkan di halaman POS
    $cabang = DB::table('tb_cabang')
        ->where('id_cabang', $id_cabang)
        ->first();

    if (!$cabang || !$cabang->is_active) {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login')->with('error', 'Cabang Anda telah dinonaktifkan. Silakan hubungi Administrator.');
    }

    return view('kasir.pesan.index', compact('menus', 'toppings', 'cart', 'cabang'));
}

public function addToCart(Request $request)
{
    $request->validate([
        'menu_id' => 'required|integer',
        'qty'     => 'required|integer|min:1|max:100',
    ]);

    $id_cabang = Auth::user()->id_cabang;

    // ✅ UBAH BAGIAN INI: Ambil menu dan harga spesifik cabang
    $menu = DB::table('tb_menu')
        ->join('branch_menu', 'tb_menu.id_menu', '=', 'branch_menu.menu_id')
        ->where('tb_menu.id_menu', $request->menu_id)
        ->where('branch_menu.branch_id', $id_cabang)
        ->select('tb_menu.id_menu', 'tb_menu.nama_menu', 'branch_menu.harga_menu')
        ->first();

    // Proteksi jika menu tidak ada di cabang tersebut
    if (!$menu) {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Menu tidak tersedia untuk cabang ini!'], 404);
        }
        return redirect()->back()->with('error', 'Menu tidak tersedia untuk cabang ini!');
    }

    $selectedToppings = DB::table('tb_toping')->whereIn('id_toping', $request->toppings ?? [])->get();

    $processedToppings = [];
    $toppingTotalHarga = 0;

    // LOGIKA PROMO: Maksimal 2 topping ber-flag bisa_gratis=1 dengan harga PALING MURAH digratiskan
    // Topping bisa_gratis=0 atau topping yang lebih mahal → TETAP BERBAYAR
    $freeEligibleIds = $selectedToppings
        ->filter(fn($t) => (bool) $t->bisa_gratis)
        ->sortBy(fn($t) => (float) $t->harga_toping)
        ->take(2)
        ->pluck('id_toping')
        ->toArray();

    foreach ($selectedToppings as $top) {
        $isFree = in_array($top->id_toping, $freeEligibleIds);
        $hargaSnapshot = $isFree ? 0 : $top->harga_toping;

        $processedToppings[] = [
            'id_toping'      => $top->id_toping,
            'nama_toping'    => $top->nama_toping,
            'harga_snapshot' => $hargaSnapshot
        ];

        $toppingTotalHarga += $hargaSnapshot;
    }

        $cart = session()->get('cart', []);
        
        $cart[] = [
            'id_menu' => $menu->id_menu,
            'nama_menu' => $menu->nama_menu,
            'harga_menu' => $menu->harga_menu,
            'qty' => $request->qty,
            'toppings' => $processedToppings,
            'subtotal' => ($menu->harga_menu + $toppingTotalHarga) * $request->qty
        ];

        session()->put('cart', $cart);
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart' => $cart,
                'message' => 'Menu ditambahkan!'
            ]);
        }
        return redirect()->route('kasir.pesan.index')->with('success', 'Menu ditambahkan!');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|min:3',
            'jenis_pembayaran' => 'required'
        ]);

        $id_cabang = Auth::user()->id_cabang ?? 1;
        $cabang = DB::table('tb_cabang')->where('id_cabang', $id_cabang)->first();
        if (!$cabang || !$cabang->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Cabang Anda telah dinonaktifkan. Transaksi tidak dapat diproses.'
            ], 403);
        }

        // Anti Double-Checkout: Ambil dan langsung kosongkan cart secara atomik
        $cart = session()->pull('cart', []);
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang kosong atau pesanan sudah berhasil diproses!'
            ], 400);
        }

        $totalAwal = array_sum(array_column($cart, 'subtotal'));
        $metode = strtolower($request->jenis_pembayaran);
        $isCash = in_array($metode, ['cash', 'tunai']);
        $uangTunai = $request->filled('uang_tunai') ? (float)$request->uang_tunai : ($isCash ? 0 : $totalAwal);

        // Validasi Pembayaran Tunai: Uang tunai tidak boleh kurang dari total belanja
        if ($isCash && $uangTunai < $totalAwal) {
            // Kembalikan cart ke session karena validasi gagal
            session()->put('cart', $cart);

            return response()->json([
                'success' => false,
                'message' => 'Uang tunai (Rp ' . number_format($uangTunai, 0, ',', '.') . ') kurang dari total bayar (Rp ' . number_format($totalAwal, 0, ',', '.') . ')!'
            ], 422);
        }

        try {
            $id = DB::transaction(function () use ($request, $cart, $totalAwal, $metode, $uangTunai, $id_cabang) {
                // 1. Simpan Header Penjualan
                $penjualanId = DB::table('tb_penjualan')->insertGetId([
                    'id_cabang'         => $id_cabang,
                    'id_user'           => Auth::id() ?? 1,
                    'nama_pelanggan'    => $request->nama_pelanggan,
                    'total_awal'        => $totalAwal,
                    'total_bayar'       => $totalAwal,
                    'metode_pembayaran' => $metode,
                    'uang_tunai'        => $uangTunai,
                    'kembalian'         => max(0, $uangTunai - $totalAwal),
                    'status_transaksi'  => 'berhasil',
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                // 2. Simpan Detail Menu
                foreach ($cart as $item) {
                    $detailId = DB::table('tb_detail_penjualan')->insertGetId([
                        'id_penjualan'   => $penjualanId,
                        'id_menu'        => $item['id_menu'],
                        'qty'            => $item['qty'],
                        'harga_snapshot' => $item['harga_menu'],
                        'subtotal'       => $item['subtotal']
                    ]);

                    // 3. Simpan Topping
                    if (!empty($item['toppings'])) {
                        foreach ($item['toppings'] as $top) {
                            DB::table('tb_detail_penjualan_toping')->insert([
                                'id_detail_penjualan' => $detailId,
                                'id_toping'           => $top['id_toping'],
                                'nama_toping'         => $top['nama_toping'],
                                'harga_toping'        => $top['harga_snapshot']
                            ]);
                        }
                    }
                }

                return $penjualanId;
            });

            return response()->json([
                'success' => true, 
                'id_penjualan' => $id
            ]);
        } catch (\Exception $e) {
            // Kembalikan cart ke session jika terjadi kegagalan transaksi
            session()->put('cart', $cart);

            // Catat error lengkap di file log (tidak ditampilkan ke user)
            \Illuminate\Support\Facades\Log::error('Checkout Gagal', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal diproses. Silakan coba lagi atau hubungi admin.'
            ], 500);
        }
    }

    public function riwayat(Request $request)
    {
        $id_cabang = Auth::user()->id_cabang;
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        // Get today's successful orders for this branch, ordered by creation time
        $query = DB::table('tb_penjualan')
            ->leftJoin('tb_user', 'tb_penjualan.id_user', '=', 'tb_user.id_user')
            ->where('tb_penjualan.id_cabang', $id_cabang)
            ->where('tb_penjualan.status_transaksi', 'berhasil')
            ->whereDate('tb_penjualan.created_at', $today);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('tb_penjualan.nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('tb_penjualan.metode_pembayaran', 'like', "%{$search}%")
                  ->orWhere('tb_user.name', 'like', "%{$search}%");
            });
        }

        $orders = $query->select(
                'tb_penjualan.*',
                'tb_user.name as nama_kasir'
            )
            ->orderBy('tb_penjualan.created_at', 'asc')
            ->get();

        // Assign daily sequential number (no_urut)
        foreach ($orders as $index => $order) {
            $order->no_urut = $index + 1;
        }

        return view('kasir.riwayat.index', compact('orders'));
    }

    public function exportExcel()
    {
        $id_cabang = Auth::user()->id_cabang;
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        $cabang = DB::table('tb_cabang')->where('id_cabang', $id_cabang)->first();
        $namaCabang = $cabang ? str_replace(' ', '_', $cabang->nama_cabang) : 'Cabang';

        $orders = DB::table('tb_penjualan')
            ->leftJoin('tb_user', 'tb_penjualan.id_user', '=', 'tb_user.id_user')
            ->where('tb_penjualan.id_cabang', $id_cabang)
            ->where('tb_penjualan.status_transaksi', 'berhasil')
            ->whereDate('tb_penjualan.created_at', $today)
            ->select(
                'tb_penjualan.*',
                'tb_user.name as nama_kasir'
            )
            ->orderBy('tb_penjualan.created_at', 'asc')
            ->get();

        $filename = "Riwayat_Transaksi_{$namaCabang}_{$today}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($orders, $cabang) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so MS Excel opens without encoding issues
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Column Headers
            fputcsv($handle, [
                'No',
                'No. Struk',
                'Waktu',
                'Pelanggan',
                'Metode Pembayaran',
                'Total Bayar (Rp)',
                'Uang Tunai (Rp)',
                'Kembalian (Rp)',
                'Kasir',
                'Cabang'
            ]);

            foreach ($orders as $index => $order) {
                $noUrut = $index + 1;
                $noStruk = 'SHJ-' . date('dmy', strtotime($order->created_at)) . '-' . str_pad($noUrut, 3, '0', STR_PAD_LEFT);
                fputcsv($handle, [
                    $noUrut,
                    $noStruk,
                    date('H:i:s', strtotime($order->created_at)),
                    $order->nama_pelanggan,
                    strtoupper($order->metode_pembayaran),
                    (int) $order->total_bayar,
                    (int) ($order->uang_tunai ?? $order->total_bayar),
                    (int) ($order->kembalian ?? 0),
                    $order->nama_kasir ?? '-',
                    $cabang->nama_cabang ?? '-'
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $id_cabang = Auth::user()->id_cabang;
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        $cabang = DB::table('tb_cabang')->where('id_cabang', $id_cabang)->first();

        $orders = DB::table('tb_penjualan')
            ->leftJoin('tb_user', 'tb_penjualan.id_user', '=', 'tb_user.id_user')
            ->where('tb_penjualan.id_cabang', $id_cabang)
            ->where('tb_penjualan.status_transaksi', 'berhasil')
            ->whereDate('tb_penjualan.created_at', $today)
            ->select(
                'tb_penjualan.*',
                'tb_user.name as nama_kasir'
            )
            ->orderBy('tb_penjualan.created_at', 'asc')
            ->get();

        foreach ($orders as $index => $order) {
            $order->no_urut = $index + 1;
            $order->no_struk = 'SHJ-' . date('dmy', strtotime($order->created_at)) . '-' . str_pad($order->no_urut, 3, '0', STR_PAD_LEFT);
        }

        return view('kasir.riwayat.pdf', compact('orders', 'cabang', 'today'));
    }

    public function deleteOrder(Request $request, $id)
    {
        $id_cabang = Auth::user()->id_cabang;

        // Verify the order belongs to this branch
        $order = DB::table('tb_penjualan')
            ->where('id_penjualan', $id)
            ->where('id_cabang', $id_cabang)
            ->where('status_transaksi', 'berhasil')
            ->first();

        if (!$order) {
            return redirect()->route('kasir.riwayat.index')->with('error', 'Pesanan tidak ditemukan!');
        }

        // Hitung nomor urut struk untuk pencatatan
        $today = date('Y-m-d', strtotime($order->created_at));
        $noUrut = DB::table('tb_penjualan')
            ->where('id_cabang', $id_cabang)
            ->where('status_transaksi', 'berhasil')
            ->whereDate('created_at', $today)
            ->where('created_at', '<=', $order->created_at)
            ->count();
        $noStruk = 'SHJ-' . date('dmy', strtotime($order->created_at)) . '-' . str_pad($noUrut, 3, '0', STR_PAD_LEFT);

        // 1. Simpan ke Riwayat Penghapusan (Audit Log)
        DB::table('tb_deletion_log')->insert([
            'id_penjualan'   => $order->id_penjualan,
            'id_user'        => Auth::id(),
            'id_cabang'      => $id_cabang,
            'no_struk'       => $noStruk,
            'nama_pelanggan' => $order->nama_pelanggan,
            'total_bayar'    => $order->total_bayar,
            'alasan'         => $request->input('alasan', 'Pesanan dibatalkan/dihapus oleh Kasir'),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // 2. Mark as failed instead of deleting (audit trail: data tidak hilang)
        DB::table('tb_penjualan')
            ->where('id_penjualan', $id)
            ->update([
                'status_transaksi' => 'gagal',
                'updated_at' => now(),
            ]);

        return redirect()->route('kasir.riwayat.index')->with('success', 'Pesanan berhasil dihapus dan dicatat di riwayat audit!');
    }

    public function printStruk($id)
    {
        $user = Auth::user();
        $query = DB::table('tb_penjualan')
            ->leftJoin('tb_user', 'tb_penjualan.id_user', '=', 'tb_user.id_user')
            ->leftJoin('tb_cabang', 'tb_penjualan.id_cabang', '=', 'tb_cabang.id_cabang')
            ->where('tb_penjualan.id_penjualan', $id);

        // Isolasi multi-cabang: Kasir hanya boleh mencetak struk dari cabangnya sendiri
        if ($user && $user->role !== 'admin') {
            $query->where('tb_penjualan.id_cabang', $user->id_cabang);
        }

        $penjualan = $query->select(
                'tb_penjualan.*', 
                'tb_user.name as nama_kasir', 
                'tb_cabang.nama_cabang', 
                'tb_cabang.alamat as alamat_cabang'
            )
            ->first();

        if (!$penjualan) {
            abort(403, 'Akses Ditolak: Struk tidak ditemukan atau Anda tidak memiliki akses ke cabang ini.');
        }

        // Calculate daily sequential number for this receipt
        $today = date('Y-m-d', strtotime($penjualan->created_at));
        $no_urut = DB::table('tb_penjualan')
            ->where('id_cabang', $penjualan->id_cabang)
            ->where('status_transaksi', 'berhasil')
            ->whereDate('created_at', $today)
            ->where('created_at', '<=', $penjualan->created_at)
            ->count();

        $penjualan->no_urut = $no_urut;

        $details = DB::table('tb_detail_penjualan')
            ->join('tb_menu', 'tb_detail_penjualan.id_menu', '=', 'tb_menu.id_menu')
            ->where('id_penjualan', $id)
            ->select('tb_detail_penjualan.*', 'tb_menu.nama_menu')
            ->get();

        foreach ($details as $detail) {
            $detail->toppings = DB::table('tb_detail_penjualan_toping')
                ->where('id_detail_penjualan', $detail->id_detail_penjualan)
                ->get();
        }

        $setting = AppSetting::current();

        return view('kasir.pesan.struk', compact('penjualan', 'details', 'setting')); 
    }

    public function removeCart(Request $request, $index)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$index])) {
            unset($cart[$index]);
            $cart = array_values($cart);
            session()->put('cart', $cart);
        }
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'cart' => $cart,
                'message' => 'Menu dihapus dari keranjang'
            ]);
        }
        return back();
    }
    public function updateQty(Request $request, $index, $action)
{
    $cart = session()->get('cart', []);
    $message = '';

    if (isset($cart[$index])) {
        // 1. Update Quantity
        if ($action === 'plus') {
            $cart[$index]['qty']++;
        } elseif ($action === 'minus') {
            $cart[$index]['qty']--;
        }

        // 2. Jika Qty menjadi 0 atau kurang, hapus item dari keranjang
        if ($cart[$index]['qty'] < 1) {
            unset($cart[$index]);
            $cart = array_values($cart);
            session()->put('cart', $cart);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'cart' => $cart,
                    'message' => 'Menu dihapus dari keranjang'
                ]);
            }
            return redirect()->back()->with('success', 'Menu dihapus dari keranjang');
        }

        // 3. Hitung Ulang Subtotal untuk item ini
        $toppingTotalHarga = 0;
        foreach ($cart[$index]['toppings'] as $top) {
            $toppingTotalHarga += $top['harga_snapshot'];
        }

        $cart[$index]['subtotal'] = ($cart[$index]['harga_menu'] + $toppingTotalHarga) * $cart[$index]['qty'];

        // 4. Simpan kembali ke session
        session()->put('cart', $cart);
        $message = 'Jumlah pesanan diperbarui';
    }

    if ($request->wantsJson()) {
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'message' => $message
        ]);
    }

    return redirect()->back();
}

public function clearCart(Request $request)
{
    session()->forget('cart');
    if ($request->wantsJson()) {
        return response()->json([
            'success' => true,
            'cart' => [],
            'message' => 'Keranjang pesanan berhasil dibersihkan!'
        ]);
    }
    return redirect()->route('kasir.pesan.index')->with('success', 'Keranjang dibersihkan!');
}
}