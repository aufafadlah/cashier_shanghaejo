@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen">
    <div class="bg-white w-[350px] rounded-2xl shadow-lg p-6 border">
        <h2 class="font-bold text-gray-800 mb-6 border-b pb-2">Update Topping</h2>
        
        <form action="{{ route('toping.update', $toping->id_toping) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="text-xs font-bold text-gray-600 block mb-1">Nama Topping</label>
                <input type="text" name="nama_toping" value="{{ $toping->nama_toping }}" class="w-full border rounded p-2 text-sm focus:outline-green-500" required>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-600 block mb-1">Harga</label>
                <input type="number" name="harga_toping" value="{{ $toping->harga_toping }}" class="w-full border rounded p-2 text-sm focus:outline-green-500" required>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-600 block mb-1">Status</label>
                <select name="is_active" class="w-full border rounded p-2 text-sm focus:outline-green-500">
                    <option value="1" {{ $toping->is_active == 1 ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ $toping->is_active == 0 ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="bg-green-500 text-white px-4 py-1 rounded text-xs">Update</button>
                <a href="{{ route('toping.index') }}" class="bg-red-500 text-white px-4 py-1 rounded text-xs">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection