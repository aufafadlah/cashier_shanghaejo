<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToppingController extends Controller
{
    public function index(Request $request)
    {
        $totalToping = DB::table('tb_toping')->count();
        $aktif       = DB::table('tb_toping')->where('is_active', 1)->count();
        $nonAktif    = DB::table('tb_toping')->where('is_active', 0)->count();

        $query = DB::table('tb_toping')->orderBy('id_toping', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nama_toping', 'like', "%{$search}%");
        }

        $topings = $query->paginate(10)->withQueryString();

        return view('admin.toping.index', compact('topings', 'totalToping', 'aktif', 'nonAktif'));
    }

    public function create()
    {
        return view('admin.toping.create');
    }

    public function store(Request $request)
    {
        if ($request->has('harga_toping')) {
            $request->merge([
                'harga_toping' => $this->cleanCurrency($request->harga_toping)
            ]);
        }

        $request->validate([
            'nama_toping'  => 'required|string|max:100',
            'harga_toping' => 'required|numeric|min:0',
            'is_active'    => 'required|in:0,1',
            'bisa_gratis'  => 'nullable|in:0,1',
        ]);

        DB::table('tb_toping')->insert([
            'nama_toping'  => $request->nama_toping,
            'harga_toping' => $request->harga_toping,
            'is_active'    => (int) $request->is_active,
            'bisa_gratis'  => (int) $request->input('bisa_gratis', 1), // default: bisa gratis
        ]);

        return redirect()->route('admin.toping.index')->with('success', 'Topping berhasil ditambah!');
    }

    public function edit($id)
    {
        $toping = DB::table('tb_toping')->where('id_toping', $id)->first();

        if (!$toping) {
            return redirect()->route('admin.toping.index')->with('error', 'Topping tidak ditemukan!');
        }

        return view('admin.toping.edit', compact('toping'));
    }

    public function update(Request $request, $id)
    {
        if ($request->has('harga_toping')) {
            $request->merge([
                'harga_toping' => $this->cleanCurrency($request->harga_toping)
            ]);
        }

        $request->validate([
            'nama_toping'  => 'required|string|max:100',
            'harga_toping' => 'required|numeric|min:0',
            'is_active'    => 'required|in:0,1',
            'bisa_gratis'  => 'nullable|in:0,1',
        ]);

        DB::table('tb_toping')->where('id_toping', $id)->update([
            'nama_toping'  => $request->nama_toping,
            'harga_toping' => $request->harga_toping,
            'is_active'    => (int) $request->is_active,
            'bisa_gratis'  => (int) $request->input('bisa_gratis', 1),
        ]);

        return redirect()->route('admin.toping.index')->with('success', 'Topping berhasil diupdate!');
    }

    public function destroy($id)
    {
        $dipakai = DB::table('tb_detail_penjualan_toping')->where('id_toping', $id)->exists();

        if ($dipakai) {
            return redirect()->route('admin.toping.index')->with('error', 'Topping tidak bisa dihapus karena masih digunakan dalam transaksi!');
        }

        DB::table('tb_toping')->where('id_toping', $id)->delete();
        return redirect()->route('admin.toping.index')->with('success', 'Topping berhasil dihapus!');
    }

    private function cleanCurrency($value): int
    {
        if ($value === null || $value === '') return 0;
        $val = trim((string) $value);
        if (preg_match('/\.00$/', $val)) {
            $val = preg_replace('/\.00$/', '', $val);
        }
        return (int) str_replace(['.', ','], '', $val);
    }
}