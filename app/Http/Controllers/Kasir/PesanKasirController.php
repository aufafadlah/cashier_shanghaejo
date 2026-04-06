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
        
        // Simpan ke session dengan perhitungan subtotal
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
        $cart = session()->get('cart', []);
        if (empty($cart)) return back()->with('error', 'Keranjang masih kosong!');

        try {
            DB::transaction(function () use ($request, $cart) {
                // 1. Simpan ke tb_penjualan (Header)
                $penjualanId = DB::table('tb_penjualan')->insertGetId([
                    'id_cabang'         => Auth::user()->id_cabang ?? 1, // Mengambil id_cabang dari user login
                    'id_user'           => Auth::id() ?? 1,
                    'nama_pelanggan'    => $request->nama_pelanggan,
                    'total_awal'        => $request->total_awal, // Total sebelum diskon
                    'diskon'            => $request->diskon ?? 0,
                    'total_bayar'       => $request->total_bayar,
                    'metode_pembayaran' => $request->metode_pembayaran, // 'cash' atau 'qris'
                    'uang_tunai'        => $request->uang_tunai ?? 0,
                    'kembalian'         => $request->kembalian ?? 0,
                    'status_transaksi'  => 'berhasil',
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                foreach ($cart as $item) {
                    // 2. Simpan ke tb_detail_penjualan
                    $detailId = DB::table('tb_detail_penjualan')->insertGetId([
                        'id_penjualan'   => $penjualanId,
                        'id_menu'        => $item['id_menu'],
                        'qty'            => $item['qty'],
                        'harga_snapshot' => $item['harga_menu'],
                        'subtotal'       => $item['subtotal']
                    ]);

                    // 3. Simpan ke tb_detail_penjualan_toping (Snapshot Topping)
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
            });

            session()->forget('cart');
            return redirect()->route('kasir.pesan.index')->with('success', 'Transaksi Berhasil Disimpan!');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
public function updateQty($index, $action)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$index])) {
        if ($action === 'plus') {
            $cart[$index]['qty']++;
        } elseif ($action === 'minus') {
            $cart[$index]['qty']--;
            
            // Kalau qty jadi 0, hapus saja dari keranjang
            if ($cart[$index]['qty'] <= 0) {
                unset($cart[$index]);
            }
        }
        
        session()->put('cart', $cart);
    }

    return redirect()->back();
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

    public function clearCart()
    {
        session()->forget('cart');
        return back();
    }
}