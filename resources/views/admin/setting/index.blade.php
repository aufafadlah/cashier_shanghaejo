@extends('layouts.admin')
@section('title', 'Pengaturan Struk')
@section('content')
<div class="container-fluid p-6 bg-[#F8FAFC] min-h-screen">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Struk</h1>
        <p class="text-sm text-gray-500 mt-1">Ubah identitas toko yang tampil di struk kasir.</p>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-green-500"></i>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-semibold">
        <i class="fa-solid fa-circle-exclamation text-red-500 mr-1"></i>
        {{ $errors->first() }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-5xl">

        {{-- FORM PENGATURAN --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-700 mb-5 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-teal-600"></i>
                Informasi Toko
            </h2>

            <form action="{{ route('admin.setting.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Nama Toko --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                        Nama Toko / Brand
                    </label>
                    <input type="text" name="brand_name"
                           value="{{ old('brand_name', $setting->brand_name) }}"
                           placeholder="Contoh: ShangHaejoCicio"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400 outline-none transition-all text-sm font-medium text-gray-800">
                    @error('brand_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Instagram --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                        Instagram
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">@</span>
                        <input type="text" name="instagram"
                               value="{{ old('instagram', ltrim($setting->instagram ?? '', '@')) }}"
                               placeholder="shanghaejo_cicio"
                               class="w-full pl-7 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400 outline-none transition-all text-sm font-medium text-gray-800">
                    </div>
                    @error('instagram')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nomor Telepon --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                        Nomor Telepon / WhatsApp
                    </label>
                    <input type="text" name="telepon"
                           value="{{ old('telepon', $setting->telepon) }}"
                           placeholder="Contoh: +62 88905779517"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400 outline-none transition-all text-sm font-medium text-gray-800">
                    @error('telepon')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload Logo --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">
                        Ganti Logo Struk
                    </label>
                    <p class="text-xs text-gray-400 mb-2">Format: JPG, PNG, WebP — Maks. 2MB</p>

                    {{-- Preview Logo Saat Ini --}}
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-gray-200 bg-gray-50 flex items-center justify-center flex-shrink-0" id="logo-preview-wrapper">
                            @if($setting->logo_path)
                                <img src="{{ asset($setting->logo_path) }}" alt="Logo Saat Ini"
                                     class="w-full h-full object-cover" id="logo-preview-img"
                                     onerror="this.parentElement.innerHTML='<i class=\'fa-solid fa-store text-gray-300 text-2xl\'></i>'">
                            @else
                                <i class="fa-solid fa-store text-gray-300 text-2xl"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label for="logo-input" class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                                <i class="fa-solid fa-upload text-teal-500"></i>
                                Pilih Gambar Baru
                            </label>
                            <input type="file" name="logo" id="logo-input" accept="image/*"
                                   class="hidden" onchange="previewLogo(this)">
                            <p class="text-xs text-gray-400 mt-1" id="logo-filename">Belum ada file dipilih</p>
                        </div>
                    </div>

                    {{-- Reset ke Logo Default --}}
                    @if($setting->logo_path && $setting->logo_path !== 'img/shanghaejo.jpg')
                    <label class="flex items-center gap-2 cursor-pointer w-fit">
                        <input type="checkbox" name="reset_logo" value="1"
                               class="rounded accent-red-500">
                        <span class="text-xs text-red-500 font-semibold">Reset ke logo default (shanghaejo.jpg)</span>
                    </label>
                    @endif
                </div>

                <button type="submit"
                        class="w-full py-3 bg-teal-600 text-white font-bold rounded-xl shadow-sm hover:bg-teal-700 transition-all active:scale-95 text-sm">
                    Simpan Pengaturan
                </button>
            </form>
        </div>

        {{-- PREVIEW STRUK --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-bold text-gray-700 mb-5 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-teal-600"></i>
                Preview Tampilan di Struk
            </h2>

            {{-- Mini receipt preview --}}
            <div class="flex justify-center">
                <div class="bg-white border-2 border-dashed border-gray-200 rounded-xl p-4 w-60 font-mono text-[11px] text-black shadow-inner">

                    {{-- Logo Preview --}}
                    <div class="flex flex-col items-center mb-2">
                        <div class="w-12 h-12 rounded-full overflow-hidden border border-gray-300 mb-1 bg-gray-100">
                            @if($setting->logo_path)
                                <img src="{{ asset($setting->logo_path) }}" alt="Logo"
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'">
                            @endif
                        </div>
                        <div class="font-black text-[14px] text-center tracking-wide">{{ $setting->brand_name }}</div>
                        <div class="text-[9.5px] text-center text-gray-600 mt-0.5">Cabang Utama</div>
                        <div class="text-[9.5px] text-center text-gray-500">Jl. Contoh Alamat No. 1</div>
                    </div>

                    <div class="border-t border-dashed border-black my-1.5"></div>

                    <div class="flex justify-between mb-0.5">
                        <span>No:</span>
                        <span>SHJ-131025-001</span>
                    </div>
                    <div class="flex justify-between mb-0.5">
                        <span>13/10/25 14:00</span>
                        <span>Kasir: Admin</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pelanggan:</span>
                        <span>Umum</span>
                    </div>

                    <div class="border-t border-dashed border-black my-1.5"></div>

                    <div class="mb-1.5">
                        <div class="flex justify-between font-bold"><span>Matcha Latte</span><span>2</span></div>
                        <div class="text-right text-gray-700">Rp 50.000</div>
                    </div>

                    <div class="border-t border-dashed border-black my-1.5"></div>

                    <div class="flex justify-between"><span>Subtotal</span><span>Rp 50.000</span></div>
                    <div class="flex justify-between font-black text-[13px] border-t border-b border-dashed border-black py-1 my-1">
                        <span>Total</span><span>Rp 50.000</span>
                    </div>
                    <div class="flex justify-between"><span>Bayar (Cash)</span><span>Rp 50.000</span></div>
                    <div class="flex justify-between"><span>Kembali</span><span>Rp 0</span></div>

                    <div class="border-t border-dashed border-black my-1.5"></div>

                    <div class="text-center font-black">TERIMA KASIH</div>
                    <div class="text-center text-gray-600 text-[9.5px]">Selamat Menikmati, Sampai Jumpa.</div>
                    <div class="text-center font-bold text-[9.5px] mt-1">
                        IG: {{ $setting->instagram ?? '-' }}<br>
                        {{ $setting->telepon ?? '-' }}
                    </div>
                </div>
            </div>

            <p class="text-xs text-gray-400 text-center mt-3">
                <i class="fa-solid fa-circle-info mr-1"></i>
                Preview ini menampilkan informasi struk terkini.
            </p>
        </div>
    </div>
</div>

<script>
    function previewLogo(input) {
        const file = input.files[0];
        const filenameEl = document.getElementById('logo-filename');
        const imgEl = document.getElementById('logo-preview-img');
        const wrapperEl = document.getElementById('logo-preview-wrapper');

        if (!file) {
            filenameEl.textContent = 'Belum ada file dipilih';
            return;
        }

        filenameEl.textContent = file.name;

        const reader = new FileReader();
        reader.onload = function(e) {
            if (imgEl) {
                imgEl.src = e.target.result;
            } else {
                wrapperEl.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover" id="logo-preview-img">`;
            }
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection
