@extends('layouts.admin')

@section('content')
<div class="container-fluid p-6">
    {{-- Breadcrumb & Judul --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Cabang</h1>
            <p class="text-sm text-gray-500">Informasi lengkap ID: #{{ $cabang->id_cabang }}</p>
        </div>
        <a href="{{ route('admin.cabang.index') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition shadow-sm">
             Kembali ke Daftar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Card Kiri: Info Utama --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center gap-4 mb-8">
                    <div class="p-3 bg-red-50 rounded-2xl text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $cabang->nama_cabang }}</h2>
                        <span class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Profil Cabang</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-4">
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase">Alamat Lengkap</label>
                        <p class="text-gray-700 mt-2 leading-relaxed">{{ $cabang->alamat }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase">Status Operasional</label>
                        <div class="mt-2">
                            @if($cabang->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">● Aktif</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">● Non-Aktif</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase">Terdaftar Sejak</label>
                        <p class="text-gray-700 mt-2 font-medium">
                            {{ \Carbon\Carbon::parse($cabang->created_at)->translatedFormat('d F Y') }}
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase">Terakhir Diperbarui</label>
                        <p class="text-gray-700 mt-2 font-medium">
                            {{ $cabang->updated_at ? $cabang->updated_at->diffForHumans() : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Kanan: Aksi Cepat --}}
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4">Opsi Kelola</h3>
                <div class="grid gap-3">
                    <a href="{{ route('admin.cabang.edit', $cabang->id_cabang) }}" class="w-full py-3 bg-blue-600 text-white rounded-xl font-semibold text-center hover:bg-blue-700 transition shadow-lg shadow-blue-100">
                        Edit Informasi
                    </a>
                    <button onclick="window.print()" class="w-full py-3 bg-gray-50 text-gray-600 rounded-xl font-semibold border border-gray-100 hover:bg-gray-100 transition text-center">
                        Cetak Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection