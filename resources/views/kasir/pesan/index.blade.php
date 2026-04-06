@extends('layouts.app')

@section('content')
<div x-data="{ 
    openDetail: false, 
    searchQuery: '',
    searchToping: '',
    qty: 1, 
    activeMenu: {id: '', nama: '', harga: 0, deskripsi: ''} 
}" class="min-h-screen bg-[#f8fafc] p-6 font-sans">
    


<div class="flex flex-col lg:flex-row gap-8">
    
    <div class="w-full lg:w-1/2 bg-white rounded-[40px] p-10 shadow-sm border border-slate-100 h-[800px] flex flex-col">
                    <h2 class="text-4xl font-black mb-10 text-slate-900 uppercase tracking-tighter">Pesan</h2>

            <div class="mb-8">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                    <input type="text" x-model="searchQuery" placeholder="CARI MENU..." 
                        class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-500 rounded-2xl pl-14 pr-6 py-5 font-black text-sm outline-none transition-all uppercase tracking-widest">
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto pr-3 custom-scrollbar space-y-4">
                @foreach($menus as $menu)
                <div x-show="'{{ strtolower($menu->nama_menu) }}'.includes(searchQuery.toLowerCase())"
                     @click="
                        activeMenu = {
                            id: '{{ $menu->id_menu }}', 
                            nama: '{{ $menu->nama_menu }}', 
                            harga: {{ $menu->harga_menu }},
                            deskripsi: '{{ $menu->deskripsi_menu ?? 'Deskripsi menu belum tersedia.' }}'
                        }; 
                        openDetail = true; 
                        qty = 1;
                    " class="cursor-pointer group bg-slate-50 border-2 border-transparent rounded-[25px] p-8 flex justify-between items-center  hover:bg-white  hover:border-blue-100">
                    <div class="flex items-center gap-6">
                        <div>
                            <span class="block font-black text-2xl text-slate-900 tracking-tight mb-1">{{ $menu->nama_menu }}</span>
                            <span class="text-[#71b34a] font-black text-lg">Rp {{ number_format($menu->harga_menu) }}</span>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-300 text-xl group-hover:text-blue-600 transition-colors"></i>
                </div>
                @endforeach
            </div>
        </div>

        <template x-if="openDetail">
            <div class="fixed inset-0 z-[60] flex items-center justify-center p-6" x-transition x-cloak>
                <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md" @click="openDetail = false"></div>
                
                <div class="relative bg-white w-full max-w-3xl rounded-[50px] shadow-2xl p-12 overflow-hidden">
                    <form action="{{ route('kasir.pesan.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="menu_id" :value="activeMenu.id">
                        
                        <div class="flex justify-between items-start mb-10 pb-9 border-b-2 border-slate-50">
                            <div>
                                <p class="text-xs font-black text-blue-600 uppercase tracking-[0.3em] mb-2">Konfigurasi Pesanan</p>
                                <h2 class="text-5xl font-black text-slate-900 tracking-tighter" x-text="activeMenu.nama"></h2>
                            </div>
                            <button type="button" @click="openDetail = false" class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:text-red-600 transition-all shadow-sm">
                                <i class="fa-solid fa-xmark text-2xl"></i>
                            </button>
                        </div>

                        <div class="space-y-8 overflow-y-auto max-h-[500px] pr-4 custom-scrollbar">
                            <div class="space-y-5">
                                <div class="flex items-center justify-between px-2">
                                    <label class="text-xs font-black text-slate-500 uppercase tracking-widest">Pilihan Topping</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                        <input type="text" x-model="searchToping" placeholder="CARI..." 
                                            class="bg-slate-50 border-2 border-slate-100 rounded-xl pl-10 pr-4 py-2 text-xs font-black outline-none focus:border-blue-500 transition-all">
                                    </div>
                                </div>
                                
                                <div class="bg-slate-50 p-6 rounded-[35px] border-2 border-slate-100">
                                    <div class="grid grid-cols-3 gap-4 max-h-[280px] overflow-y-auto pr-2 custom-scrollbar">
                                        @foreach($toppings as $top)
                                        <label x-show="'{{ strtolower($top->nama_toping) }}'.includes(searchToping.toLowerCase())"
                                               class="relative flex flex-col items-center justify-center p-5 rounded-[25px] border-2 border-white bg-white cursor-pointer hover:border-blue-200 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 group shadow-sm">
                                            
                                            <input type="checkbox" name="toppings[]" value="{{ $top->id_toping }}" 
                                                   class="absolute top-3 right-3 w-5 h-5 rounded-full border-slate-300 text-blue-600 focus:ring-blue-500 transition-all">
                                            
                                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center mb-3 group-hover:bg-blue-100 transition-colors">
                                                <span class="text-[12px] font-black text-slate-500 group-hover:text-blue-700">
                                                    {{ strtoupper(substr($top->nama_toping, 0, 2)) }}
                                                </span>
                                            </div>

                                            <span class="font-black text-slate-800 text-sm text-center leading-tight uppercase mb-1">
                                                {{ $top->nama_toping }}
                                            </span>
                                            <span class="text-xs font-black text-[#65a34a]">
                                                +{{ number_format($top->harga_toping ?? 0) }}
                                            </span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

{{-- FOOTER MODAL: JUMLAH (KIRI) & KONFIRMASI (KANAN) --}}
<div class="mt-8 flex items-center gap-4">
    
    {{-- JUMLAH PESANAN (SISI KIRI) --}}
    <div class="flex-1 flex items-center justify-between p-5 bg-slate-900 rounded-[25px]">
        <span class="font-black text-white text-[15px] uppercase tracking-widest ml-2 opacity-50">Jumlah</span>
        <div class="flex items-center gap-6">
            <button type="button" @click="if(qty > 1) qty--" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-white font-black hover:bg-white/20 transition-all">-</button>
            <input type="number" name="qty" x-model="qty" class="w-8 bg-transparent text-center font-black text-xl outline-none text-white" readonly>
            <button type="button" @click="qty++" class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-slate-900 font-black transition-all">+</button>
        </div>
    </div>

    {{-- TOMBOL KONFIRMASI (SISI KANAN) --}}
    <button type="submit" class="flex-[1.5] bg-blue-600 text-white font-black py-5 rounded-[25px] hover:bg-blue-700 transition-all flex justify-between px-8 items-center shadow-lg shadow-blue-100 group">
        <span class="uppercase tracking-widest text-[12px]">Tambahkan ke Pesanan</span>
        {{-- Live Price --}}
        <span class="text-xl tracking-tighter" x-text="'Rp ' + (activeMenu.harga * qty).toLocaleString('id-ID')"></span>
    </button>
</div>
                        </div>

                        
                    </form>
                </div>
            </div>
        </template>

        <div class="w-full lg:w-1/2 bg-white rounded-[40px] p-10 shadow-sm border border-slate-100 h-[800px] flex flex-col">
            <h2 class="text-4xl font-black mb-10 text-slate-900 uppercase tracking-tighter">Keranjang</h2>
            
            <form action="{{ route('kasir.pesan.checkout') }}" method="POST" class="flex-1 flex flex-col overflow-hidden">
                @csrf
                <div class="flex-1 overflow-y-auto pr-3 custom-scrollbar space-y-5">
                    @forelse($cart as $index => $item)
                        <div class="flex justify-between items-center bg-slate-50 p-6 rounded-[25px] border-2 border-slate-50">
                            <div class="flex-1">
                                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">{{ $item['nama_menu'] }}</h3>
                                <p class="text-[11px] text-slate-500 font-black mt-1 tracking-wider">
                                    TOP: {{ count($item['toppings']) > 0 ? implode(', ', array_column($item['toppings'], 'nama_toping')) : 'ORIGINAL' }}
                                </p>
                                <p class="text-[#65a34a] font-black text-lg mt-1">Rp {{ number_format($item['harga_menu'] ?? $item['harga'] ?? 0) }}</p>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-4 bg-white border-2 border-slate-100 p-2 rounded-2xl shadow-sm">
                                    <a href="{{ route('kasir.pesan.update-qty', ['index' => $index, 'action' => 'minus']) }}" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-red-600 font-black text-xl transition-all">-</a>
                                    <span class="text-lg font-black text-slate-900 w-6 text-center">{{ $item['qty'] }}</span>
                                    <a href="{{ route('kasir.pesan.update-qty', ['index' => $index, 'action' => 'plus']) }}" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-blue-600 font-black text-xl transition-all">+</a>
                                </div>
                                <a href="{{ route('kasir.pesan.remove', $index) }}" class="text-slate-300 hover:text-red-600 ml-2 transition-colors">
                                    <i class="fa-solid fa-circle-xmark text-3xl"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center border-4 border-dashed border-slate-50 rounded-[40px] opacity-40">
                            <i class="fa-solid fa-cart-shopping text-5xl text-slate-200 mb-4"></i>
                            <p class="text-lg font-black uppercase tracking-widest text-slate-300">Belum Ada Pesanan</p>
                        </div>
                    @endforelse
                </div>

                @if(count($cart) > 0)
                <div class="mt-8 pt-8 border-t-4 border-slate-50 space-y-6">
                    <input type="text" name="nama_pelanggan" placeholder="NAMA PELANGGAN" 
                        class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-500 rounded-2xl px-6 py-5 text-sm font-black outline-none transition-all tracking-widest" required>
                    
                    <div class="grid grid-cols-2 gap-4">
                        @foreach(['QRIS', 'CASH'] as $metode)
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis_pembayaran" value="{{ $metode }}" class="hidden peer" required>
                            <div class="py-5 border-2 border-slate-100 rounded-2xl text-center font-black text-xs text-slate-400 peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white transition-all uppercase tracking-[0.2em] shadow-sm">
                                {{ $metode }}
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="flex justify-between items-end py-4">
                        <span class="text-slate-400 font-black text-xs uppercase tracking-[0.3em]">Total Bayar</span>
                        <span class="text-[#65a34a] font-black text-4xl tracking-tighter">Rp {{ number_format(array_sum(array_map(fn($i) => ($i['harga_menu'] ?? $i['harga'] ?? 0) * $i['qty'], $cart))) }}</span>
                    </div>

                    <button type="submit" class="w-full bg-[#71b34a] text-white font-black text-xl py-6 rounded-[25px] hover:bg-green-600 shadow-xl shadow-green-100 uppercase tracking-widest transition-all transform active:scale-[0.98]">
                        Proses Checkout
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 20px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    [x-cloak] { display: none !important; }
</style>
@endsection