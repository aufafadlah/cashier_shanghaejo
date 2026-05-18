<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BeliBahan;
use App\Models\Cabang;
use Illuminate\Http\Request;

class BeliBahanController extends Controller
{
    public function index()
    {
        $pengeluaran = BeliBahan::with('cabang')->latest()->get();
        return view('admin.pembelian.index', compact('pengeluaran'));
    }

    public function create()
    {
        $cabang = Cabang::all();
        return view('admin.pembelian.create', compact('cabang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cabang'    => 'required',
            'nama_barang'  => 'required|string|max:255',
            'qty'          => 'required|numeric|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tgl'          => 'required|date',
            'supplier'     => 'required|string|max:100',
            'keterangan'   => 'nullable|string',
        ]);

        // ✅ harga_total dihitung di satu tempat saja
        BeliBahan::create([
            'id_cabang'    => $request->id_cabang,
            'nama_barang'  => $request->nama_barang,
            'qty'          => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'harga_total'  => $this->hitungTotal($request->qty, $request->harga_satuan),
            'tgl'          => $request->tgl,
            'keterangan'   => $request->keterangan,
            'supplier'     => $request->supplier,
        ]);

        return redirect()->route('admin.pembelian.index')->with('success', 'Pengeluaran berhasil ditambah');
    }

    public function edit($id)
    {
        $pengeluaran = BeliBahan::findOrFail($id);
        $cabang      = Cabang::all();
        return view('admin.pembelian.edit', compact('pengeluaran', 'cabang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_cabang'    => 'required',
            'nama_barang'  => 'required|string|max:255',
            'qty'          => 'required|numeric|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tgl'          => 'required|date',
            'supplier'     => 'required|string|max:100',
            'keterangan'   => 'nullable|string',
        ]);

        $data = BeliBahan::findOrFail($id);

        // ✅ harga_total dihitung lewat method yang sama
        $data->update([
            'id_cabang'    => $request->id_cabang,
            'nama_barang'  => $request->nama_barang,
            'qty'          => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'harga_total'  => $this->hitungTotal($request->qty, $request->harga_satuan),
            'tgl'          => $request->tgl,
            'keterangan'   => $request->keterangan,
            'supplier'     => $request->supplier,
        ]);

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pengeluaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $data = BeliBahan::findOrFail($id);
        $data->delete();
        return redirect()->route('admin.pembelian.index')->with('success', 'Data pengeluaran berhasil dihapus!');
    }

    // ✅ logika hitung total dipusatkan di sini
    private function hitungTotal($qty, $hargaSatuan): float
    {
        return $qty * $hargaSatuan;
    }
}