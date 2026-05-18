<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PesanKasirController extends Controller
{
    public function index()
    {
        // Ambil menu & topping yang aktif saja
        $menus = DB::table('tb_menu')->where('is_active', 1)->get();
        $toppings = DB::table('tb_toping')->where('is_active', 1)->get();
        $cart = session()->get('cart', []);

        return view('kasir.pesan.index', compact('menus', 'toppings', 'cart'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'menu_id' => 'required',
            'qty' => 'required|integer|min:1'
        ]);

        $menu = DB::table('tb_menu')->where('id_menu', $request->menu_id)->first();
        $selectedToppings = DB::table('tb_toping')->whereIn('id_toping', $request->toppings ?? [])->get();

        $processedToppings = [];
        $toppingTotalHarga = 0;

        // LOGIKA: 2 Topping pertama GRATIS
        foreach ($selectedToppings as $index => $top) {
            $hargaSnapshot = ($index < 2) ? 0 : $top->harga_toping;
            
            $processedToppings[] = [
                'id_toping' => $top->id_toping,
                'nama_toping' => $top->nama_toping,
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
        return redirect()->route('kasir.pesan.index')->with('success', 'Menu ditambahkan!');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|min:3',
            'jenis_pembayaran' => 'required'
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) return response()->json(['success' => false, 'message' => 'Keranjang kosong!'], 400);

        try {
            $id = DB::transaction(function () use ($request, $cart) {
                $totalAwal = array_sum(array_column($cart, 'subtotal'));
                $uangTunai = $request->uang_tunai ?? $totalAwal;

                // 1. Simpan Header Penjualan
                $penjualanId = DB::table('tb_penjualan')->insertGetId([
                    'id_cabang'         => Auth::user()->id_cabang ?? 1,
                    'id_user'           => Auth::id() ?? 1,
                    'nama_pelanggan'    => $request->nama_pelanggan,
                    'total_awal'        => $totalAwal,
                    'total_bayar'       => $totalAwal,
                    'metode_pembayaran' => strtolower($request->jenis_pembayaran),
                    'uang_tunai'        => $uangTunai,
                    'kembalian'         => $uangTunai - $totalAwal,
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

            session()->forget('cart');

            return response()->json([
                'success' => true, 
                'id_penjualan' => $id
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function printStruk($id)
    {
        $penjualan = DB::table('tb_penjualan')
            ->leftJoin('tb_user', 'tb_penjualan.id_user', '=', 'tb_user.id_user')
            ->leftJoin('tb_cabang', 'tb_penjualan.id_cabang', '=', 'tb_cabang.id_cabang')
            ->where('tb_penjualan.id_penjualan', $id)
            ->select(
                'tb_penjualan.*', 
                'tb_user.name as nama_kasir', 
                'tb_cabang.nama_cabang', 
                'tb_cabang.alamat as alamat_cabang'
            )
            ->first();

        if (!$penjualan) {
            return "Gagal: ID $id tidak ditemukan.";
        }

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

        return view('kasir.pesan.struk', compact('penjualan', 'details')); 
    }

    public function removeCart($index)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$index])) {
            unset($cart[$index]);
            session()->put('cart', array_values($cart));
        }
        return back();
    }
    public function updateQty($index, $action)
{
    $cart = session()->get('cart', []);

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
            session()->put('cart', array_values($cart));
            return redirect()->back()->with('success', 'Menu dihapus dari keranjang');
        }

        // 3. Hitung Ulang Subtotal untuk item ini
        // Kita hitung ulang karena subtotal = (Harga Menu + Total Harga Topping) * Qty
        $toppingTotalHarga = 0;
        foreach ($cart[$index]['toppings'] as $top) {
            $toppingTotalHarga += $top['harga_snapshot'];
        }

        $cart[$index]['subtotal'] = ($cart[$index]['harga_menu'] + $toppingTotalHarga) * $cart[$index]['qty'];

        // 4. Simpan kembali ke session
        session()->put('cart', $cart);
    }

    return redirect()->back();
}
}