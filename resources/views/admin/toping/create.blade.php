@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-50/50">
    <div class="bg-white w-[450px] rounded-3xl shadow-xl p-8 border border-gray-100">
        <h2 class="text-center text-xl font-semibold mb-8">Add New Topping</h2>
        
        <form action="{{ route('toping.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="nama_toping" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-green-400 outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                <input type="number" name="harga_toping" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-green-400 outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="is_active" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-green-400 outline-none">
                    <option value="1">Aktif</option>
                    <option value="0">Non-Aktif</option>
                </select>
            </div>

            <div class="flex gap-4 pt-4">
                <a href="{{ route('toping.index') }}" class="flex-1 text-center bg-red-500 text-white py-2 rounded-xl hover:bg-red-600 transition">Batal</a>
                <button type="submit" class="flex-1 bg-green-500 text-white py-2 rounded-xl hover:bg-green-600 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection