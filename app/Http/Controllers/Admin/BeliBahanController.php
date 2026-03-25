<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BeliBahan; 
use App\Models\Cabang; // Pastikan model Cabang sudah ada
use Illuminate\Http\Request;

class BeliBahanController extends Controller 
{
    public function index()
    {
        // Mengambil data pembelian beserta informasi cabangnya
        $pengeluaran = BeliBahan::with('cabang')->latest()->get();
        return view('admin.pembelian.index', compact('pengeluaran'));
    }

    public function create()
    {
        // Mengambil semua data dari tabel tb_cabang
        $cabang = Cabang::all(); 
        return view('admin.pembelian.create', compact('cabang')); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cabang' => 'required',
            'nama_barang' => 'required',
            'qty' => 'required|numeric',
            'harga_satuan' => 'required|numeric',
            'tgl' => 'required|date',
            'supplier' => 'required'
        ]);

        BeliBahan::create([
            'id_cabang' => $request->id_cabang, 
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'harga_total' => $request->qty * $request->harga_satuan,
            'tgl' => $request->tgl,
            'keterangan' => $request->keterangan,
            'supplier' => $request->supplier,
        ]);

        return redirect()->route('admin.pembelian.index')->with('success', 'Pengeluaran berhasil ditambah');
    }

    public function destroy($id)
    {
        // Cari data berdasarkan primary key id_pengeluaran
        $data = BeliBahan::findOrFail($id);
    
        // Hapus data
        $data->delete();
    
        // Kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.pembelian.index')->with('success', 'Data pengeluaran berhasil dihapus!');
    }
    // --- TAMBAHKAN KODE INI DI BAWAH FUNGSI DESTROY ---

    public function edit($id)
    {
        // Ambil data pengeluaran yang mau diedit
        $pengeluaran = BeliBahan::findOrFail($id);
        
        // Ambil daftar cabang untuk dropdown
        $cabang = Cabang::all(); 
        
        return view('admin.pembelian.edit', compact('pengeluaran', 'cabang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_cabang' => 'required',
            'nama_barang' => 'required',
            'qty' => 'required|numeric',
            'harga_satuan' => 'required|numeric',
            'tgl' => 'required|date',
            'supplier' => 'required'
        ]);

        $data = BeliBahan::findOrFail($id);

        $data->update([
            'id_cabang' => $request->id_cabang, 
            'nama_barang' => $request->nama_barang,
            'qty' => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            // Hitung ulang harga_total kalau-kalau qty/harga berubah
            'harga_total' => $request->qty * $request->harga_satuan,
            'tgl' => $request->tgl,
            'keterangan' => $request->keterangan,
            'supplier' => $request->supplier,
        ]);

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pengeluaran berhasil diperbarui!');
    }

}