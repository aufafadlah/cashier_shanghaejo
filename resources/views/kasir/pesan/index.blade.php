@extends('layouts.kasir')
@section('content')
<div x-data='{ 
    openDetail: false, 
    searchQuery: "",
    qty: 1, 
    activeMenu: {id: "", nama: "", harga: 0},
    selectedToppings: [],
    toppingsList: @json($toppings),
    
    getToppingLabel(topId) {
        let top = this.toppingsList.find(t => t.id_toping == topId);
        if (!top) return "";
        
        let isSelected = this.selectedToppings.some(val => val == topId);
        if (isSelected) {
            let selectedInOrder = this.toppingsList.filter(t => this.selectedToppings.some(val => val == t.id_toping));
            let index = selectedInOrder.findIndex(t => t.id_toping == topId);
            if (index < 2) {
                return "Gratis";
            }
        }
        return "Rp " + parseInt(top.harga_toping).toLocaleString("id-ID");
    },
    
    calculateTotal() {
        let base = parseFloat(this.activeMenu.harga) || 0;
        let selectedInOrder = this.toppingsList.filter(t => this.selectedToppings.some(val => val == t.id_toping));
        let toppingsTotal = 0;
        selectedInOrder.forEach((t, index) => {
            if (index >= 2) {
                toppingsTotal += parseFloat(t.harga_toping) || 0;
            }
        });
        return (base + toppingsTotal) * this.qty;
    }
}' class="h-screen bg-[#f1f5f9] p-3 font-sans overflow-hidden" x-cloak>

    <div class="flex flex-col lg:flex-row gap-4 max-w-[1600px] mx-auto h-[calc(100vh-140px)]">
        <div class="w-full lg:w-7/12 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col overflow-hidden">
            <div class="p-5 border-b border-slate-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-800 uppercase tracking-tight">Katalog Menu</h2>
                <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-1 rounded-md font-bold uppercase">Tersedia: {{ count($menus) }}</span>
            </div>

            <div class="px-5 py-4">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" x-model="searchQuery" placeholder="Cari menu..." 
                        class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-lg pl-10 pr-4 py-2 text-sm outline-none transition-all shadow-inner">
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto px-5 pb-5 custom-scrollbar space-y-2">
                @foreach($menus as $menu)
                <div x-show="'{{ strtolower($menu->nama_menu) }}'.includes(searchQuery.toLowerCase())"
                     @click="activeMenu = {id: '{{ $menu->id_menu }}', nama: '{{ $menu->nama_menu }}', harga: {{ $menu->harga_menu }} }; openDetail = true; qty = 1; selectedToppings = [];" 
                     class="cursor-pointer group bg-white border border-slate-100 rounded-xl p-3 flex justify-between items-center hover:border-blue-400 hover:shadow-md transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center group-hover:bg-blue-50 transition-colors">
                            <i class="fa-solid fa-utensils text-slate-300 group-hover:text-blue-500"></i>
                        </div>
                        <div>
                            <span class="block font-bold text-sm text-slate-800">{{ $menu->nama_menu }}</span>
                            <span class="text-blue-600 font-bold text-xs">Rp {{ number_format($menu->harga_menu) }}</span>
                        </div>
                    </div>
                    <i class="fa-solid fa-circle-plus text-slate-200 text-xl group-hover:text-blue-600 transition-colors"></i>
                </div>
                @endforeach
            </div>
        </div>

        <div class="w-full lg:w-5/12 bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col overflow-hidden">
            <div class="p-5 border-b border-slate-50">
                <h2 class="text-lg font-bold text-slate-800 uppercase tracking-tight">Detail Pesanan</h2>
            </div>
            
            <form id="formCheckout" action="{{ route('kasir.pesan.checkout') }}" method="POST" class="flex-1 flex flex-col overflow-hidden">
                @csrf
                
                <div class="flex-1 overflow-y-auto px-5 py-4 custom-scrollbar space-y-2 bg-slate-50/30">
                    @forelse($cart as $index => $item)
                        <div class="flex justify-between items-center bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-xs font-bold text-slate-800 truncate uppercase">{{ $item['nama_menu'] }}</h3>
                                <p class="text-[9px] text-slate-500 font-medium truncate">
                                    {{ count($item['toppings']) > 0 ? implode(', ', array_column($item['toppings'], 'nama_toping')) : 'ORIGINAL' }}
                                </p>
                                <p class="text-blue-600 font-bold text-[11px] mt-0.5">Rp {{ number_format($item['subtotal'] ?? 0) }}</p>
                            </div>
                            
                            <div class="flex items-center gap-2 ml-3">
                                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-2 py-1 rounded-lg text-[10px]">
                                    <a href="{{ route('kasir.pesan.update-qty', ['index' => $index, 'action' => 'minus']) }}" class="text-slate-400 font-bold hover:text-red-500 transition-colors">-</a>
                                    <span class="font-bold text-slate-800">{{ $item['qty'] }}</span>
                                    <a href="{{ route('kasir.pesan.update-qty', ['index' => $index, 'action' => 'plus']) }}" class="text-slate-400 font-bold hover:text-blue-500 transition-colors">+</a>
                                </div>
                                <a href="{{ route('kasir.pesan.remove', $index) }}" class="text-slate-300 hover:text-red-500 transition-colors">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center opacity-30">
                            <i class="fa-solid fa-cart-shopping text-3xl text-slate-300 mb-2"></i>
                            <p class="text-[10px] font-bold uppercase text-slate-400">Keranjang Kosong</p>
                        </div>
                    @endforelse
                </div>

                @if(count($cart) > 0)
                <div class="p-5 border-t border-slate-100 bg-white space-y-3 shadow-[0_-4px_10px_-5px_rgba(0,0,0,0.1)]">
                    <input type="text" name="nama_pelanggan" placeholder="NAMA PELANGGAN" required 
                        class="w-full bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-lg px-3 py-2 text-xs font-bold outline-none transition-all uppercase">
                    
                    <div class="grid grid-cols-2 gap-2" x-data="{ metode: '' }">
                        @foreach(['QRIS', 'CASH'] as $metode)
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis_pembayaran" value="{{ $metode }}" x-model="metode" class="hidden peer" required>
                            <div class="py-2 border border-slate-200 rounded-lg text-center font-bold text-[10px] text-slate-400 peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white transition-all uppercase shadow-sm">
                                {{ $metode }}
                            </div>
                        </label>
                        @endforeach

<div x-show="metode === 'CASH'" x-transition class="col-span-2 space-y-2 mt-1">
    <!-- 1. Input Tampilan (Harus type="text" supaya bisa muncul titik) -->
    <input type="text" 
           id="display_uang_tunai" 
           placeholder="NOMINAL UANG..." 
           class="w-full bg-white border border-blue-200 rounded-lg px-3 py-2 text-xs font-bold outline-none"
           oninput="
               let val = this.value.replace(/\D/g, '');
               this.value = new Intl.NumberFormat('id-ID').format(val);
               let realInput = document.getElementById('input_uang_tunai');
               realInput.value = val;
               // Trigger event supaya script kembalian tetap jalan
               realInput.dispatchEvent(new Event('input'));
           ">

    <!-- 2. Input Hidden (Untuk kirim angka bersih ke Database) -->
    <input type="hidden" name="uang_tunai" id="input_uang_tunai">

    <div class="flex justify-between px-1 text-[9px] font-bold uppercase">
        <span class="text-slate-400">Kembalian</span>
        <span id="text_kembalian" class="text-green-600 font-bold">Rp 0</span>
    </div>
</div>

                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-500 font-bold text-[10px] uppercase">Grand Total</span>
                        @php $grandTotal = array_sum(array_column($cart, 'subtotal')); @endphp
                        <span id="grand_total_val" data-total="{{ $grandTotal }}" class="text-slate-900 font-bold text-xl tracking-tighter">
                            Rp {{ number_format($grandTotal) }}
                        </span>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white font-bold text-sm py-3 rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-100 uppercase tracking-wider transition-all active:scale-[0.98]">
                        Proses Checkout
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>

    <template x-if="openDetail">
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px]" @click="openDetail = false"></div>
            <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 overflow-hidden">
                <form action="{{ route('kasir.pesan.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="menu_id" :value="activeMenu.id">
                    <div class="flex justify-between items-center mb-5 border-b pb-4">
                        <h2 class="text-lg font-bold text-slate-800" x-text="activeMenu.nama"></h2>
                        <button type="button" @click="openDetail = false" class="text-slate-400"><i class="fa-solid fa-times"></i></button>
                    </div>

                    <div class="max-h-[35vh] overflow-y-auto pr-1 custom-scrollbar mb-6">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-3 tracking-widest">Pilih Topping</p>
                        <div class="grid grid-cols-1 gap-2">
                            @foreach($toppings as $top)
                            <label class="relative flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-slate-50 cursor-pointer has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 transition-all shadow-sm">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="toppings[]" value="{{ $top->id_toping }}" x-model="selectedToppings" class="w-4 h-4 rounded text-blue-600 border-slate-300">
                                    <span class="text-xs font-bold text-slate-700 uppercase">{{ $top->nama_toping }}</span>
                                </div>
                                <span class="text-[10px] font-bold text-green-600" x-text="getToppingLabel({{ $top->id_toping }})"></span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="flex-1 flex items-center justify-between p-2 bg-slate-100 rounded-xl">
                            <button type="button" @click="if(qty > 1) qty--" class="w-8 h-8 text-slate-600 font-bold hover:bg-white rounded-lg transition-all">-</button>
                            <span class="text-slate-900 font-bold text-sm" x-text="qty"></span>
                            <input type="hidden" name="qty" :value="qty">
                            <button type="button" @click="qty++" class="w-8 h-8 text-slate-600 font-bold hover:bg-white rounded-lg transition-all">+</button>
                        </div>
                        <button type="submit" class="flex-[2] bg-blue-600 text-white font-bold py-3 rounded-xl flex justify-center items-center hover:bg-blue-700 transition-all shadow-lg text-xs uppercase tracking-wider">
                            TAMBAH - <span class="ml-1" x-text="'Rp ' + calculateTotal().toLocaleString('id-ID')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

<style>
    /* Paksa body tidak scroll untuk feel aplikasi POS */
    body { overflow: hidden !important; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    [x-cloak] { display: none !important; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkoutForm = document.getElementById('formCheckout');
        const inputTunai = document.getElementById('input_uang_tunai');
        const textKembalian = document.getElementById('text_kembalian');
        const grandTotalVal = document.getElementById('grand_total_val');

        // Logika Hitung Kembalian
        if (inputTunai) {
            inputTunai.addEventListener('input', function() {
                const total = parseInt(grandTotalVal.getAttribute('data-total')) || 0;
                const bayar = parseInt(this.value) || 0;
                const kembali = bayar - total;

                if (kembali >= 0) {
                    textKembalian.innerText = 'Rp ' + kembali.toLocaleString('id-ID');
                    textKembalian.style.color = '#16a34a';
                } else {
                    textKembalian.innerText = 'Uang Kurang';
                    textKembalian.style.color = '#ef4444';
                }
            });
        }

        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault(); 

                const form = this;
                const formData = new FormData(form);
                const metode = formData.get('jenis_pembayaran');
                const uangTunai = parseInt(formData.get('uang_tunai')) || 0;
                const total = parseInt(grandTotalVal.getAttribute('data-total')) || 0;

                // Validasi Cepat
                if (metode === 'CASH' && uangTunai < total) {
                    Swal.fire('Perhatian', 'Uang tunai tidak mencukupi!', 'warning');
                    return;
                }

                // Langsung Konfirmasi & Kirim (Tanpa Loading Interstitial)
                Swal.fire({
                    title: 'Konfirmasi Bayar',
                    text: "Proses transaksi sekarang?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a',
                    confirmButtonText: 'Ya, Checkout!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Kirim data tanpa menampilkan modal "Memproses..."
                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Cetak struk di tab baru
                                window.open(`/kasir/pesan/print/${data.id_penjualan}`, '_blank');
                                // Refresh halaman instan
                                window.location.reload();
                            } else {
                                Swal.fire('Gagal!', data.message, 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Koneksi bermasalah', 'error');
                        });
                    }
                });
            });
        }
    });
</script>
@endsection