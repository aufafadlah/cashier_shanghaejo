@extends('layouts.kasir')
@section('content')

{{-- Alpine data disimpan di script tag agar tidak ada masalah parsing JSON di HTML attribute --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('posApp', () => ({
            openDetail: false,
            searchQuery: "",
            qty: 1,
            activeMenu: {id: "", nama: "", harga: 0},
            selectedToppings: [],
            toppingsList: @json($toppings),
            cart: @json($cart),
            activeTab: "menu",

            getFreeToppingIds() {
                let eligibleSelected = this.toppingsList
                    .filter(t => this.selectedToppings.some(val => val == t.id_toping) && t.bisa_gratis)
                    .sort((a, b) => (parseFloat(a.harga_toping) || 0) - (parseFloat(b.harga_toping) || 0));
                return eligibleSelected.slice(0, 2).map(t => t.id_toping);
            },

            getToppingLabel(topId) {
                let top = this.toppingsList.find(t => t.id_toping == topId);
                if (!top) return "";
                let isSelected = this.selectedToppings.some(val => val == topId);
                if (isSelected && top.bisa_gratis) {
                    let freeIds = this.getFreeToppingIds();
                    if (freeIds.some(fid => fid == topId)) return "Gratis (Promo)";
                }
                return "Rp " + parseInt(top.harga_toping).toLocaleString("id-ID");
            },

            calculateTotal() {
                let base = parseFloat(this.activeMenu.harga) || 0;
                let freeIds = this.getFreeToppingIds();
                let selected = this.toppingsList.filter(t => this.selectedToppings.some(val => val == t.id_toping));
                let toppingsTotal = 0;
                selected.forEach((t) => {
                    if (!freeIds.some(fid => fid == t.id_toping)) {
                        toppingsTotal += parseFloat(t.harga_toping) || 0;
                    }
                });
                return (base + toppingsTotal) * this.qty;
            },

            getCartSubtotal() {
                return this.cart.reduce((sum, item) => sum + (parseFloat(item.subtotal) || 0), 0);
            },

            async addMenuToCart(e) {
                const form = e.target;
                const formData = new FormData(form);
                try {
                    const res = await fetch(form.action, {
                        method: "POST", body: formData,
                        headers: { "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content, "Accept": "application/json" }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.cart = data.cart;
                        this.openDetail = false;
                        Swal.mixin({ toast: true, position: "top-end", showConfirmButton: false, timer: 1500 }).fire({ icon: "success", title: data.message });
                    } else {
                        Swal.fire("Gagal", data.message || "Gagal menambahkan menu", "error");
                    }
                } catch (err) { console.error(err); Swal.fire("Error", "Koneksi bermasalah", "error"); }
            },

            async changeQty(index, action) {
                try {
                    const res = await fetch(`/kasir/pesan/update-qty/${index}/${action}`, {
                        method: "POST",
                        headers: { "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content, "Accept": "application/json" }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.cart = data.cart;
                        setTimeout(() => { let el = document.getElementById("input_uang_tunai"); if (el) el.dispatchEvent(new Event("input")); }, 50);
                    } else { Swal.fire("Gagal", data.message || "Gagal mengubah jumlah", "error"); }
                } catch (err) { console.error(err); Swal.fire("Error", "Koneksi bermasalah", "error"); }
            },

            async removeMenu(index) {
                try {
                    const res = await fetch(`/kasir/pesan/remove/${index}`, {
                        method: "POST",
                        headers: { "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content, "Accept": "application/json" }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.cart = data.cart;
                        Swal.mixin({ toast: true, position: "top-end", showConfirmButton: false, timer: 1500 }).fire({ icon: "success", title: data.message });
                        setTimeout(() => { let el = document.getElementById("input_uang_tunai"); if (el) el.dispatchEvent(new Event("input")); }, 50);
                    } else { Swal.fire("Gagal", data.message || "Gagal menghapus menu", "error"); }
                } catch (err) { console.error(err); Swal.fire("Error", "Koneksi bermasalah", "error"); }
            },

            async clearEntireCart() {
                const result = await Swal.fire({ title: 'Kosongkan Pesanan?', text: 'Semua item dalam keranjang akan dihapus.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Ya, Kosongkan', cancelButtonText: 'Batal' });
                if (!result.isConfirmed) return;
                try {
                    const res = await fetch('{{ route("kasir.pesan.clear") }}', {
                        method: "POST",
                        headers: { "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content, "Accept": "application/json" }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.cart = [];
                        Swal.mixin({ toast: true, position: "top-end", showConfirmButton: false, timer: 1500 }).fire({ icon: "success", title: data.message });
                    }
                } catch (err) { console.error(err); Swal.fire("Error", "Koneksi bermasalah", "error"); }
            }
        }));
    });
</script>

<div x-data="posApp" class="h-full bg-slate-50 p-2 sm:p-4 font-sans lg-overflow-hidden">

    <!-- Tab Bar untuk Mobile / Tablet -->
    <div class="flex lg:hidden bg-slate-100 p-1 rounded-xl mb-3 shadow-inner">
        <button @click="activeTab = 'menu'" 
                :class="activeTab === 'menu' ? 'bg-white text-teal-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                class="flex-1 py-2.5 text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-utensils"></i> Katalog Menu
        </button>
        <button @click="activeTab = 'cart'" 
                :class="activeTab === 'cart' ? 'bg-white text-teal-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                class="flex-1 py-2.5 text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-2 relative">
            <i class="fa-solid fa-cart-shopping"></i> Detail Pesanan
            <template x-if="cart.length > 0">
                <span class="absolute top-1 right-2 bg-red-500 text-white text-[9px] w-4.5 h-4.5 rounded-full flex items-center justify-center font-bold" x-text="cart.length"></span>
            </template>
        </button>
    </div>

    <div class="flex flex-col lg:flex-row gap-4 max-w-[1600px] mx-auto h-[calc(100vh-140px)]">
        
        <!-- Kolom Katalog Menu -->
        <div class="w-full lg:w-7/12 bg-white rounded-2xl shadow-sm border border-slate-200 flex-col overflow-hidden"
             :class="activeTab === 'menu' ? 'flex h-[calc(100vh-210px)] lg:h-full' : 'hidden lg:flex'">
            
            <div class="p-5 border-b border-slate-50 flex justify-between items-center">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-800 uppercase tracking-tight">Katalog Menu</h2>
                    <span class="text-[10px] text-slate-400 font-medium">
                        <i class="fa-solid fa-store mr-1"></i>{{ $cabang->nama_cabang ?? 'Cabang Aktif' }}
                    </span>
                </div>
                <span class="text-[10px] bg-teal-50 text-teal-700 px-2 py-1 rounded-md font-bold uppercase">Tersedia: {{ count($menus) }}</span>
            </div>

            <div class="px-5 py-4">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" x-model="searchQuery" placeholder="Cari menu..." 
                        class="w-full bg-slate-50 border border-slate-200 focus:border-teal-500 rounded-lg pl-10 pr-4 py-2 text-sm outline-none transition-all shadow-inner">
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto px-5 pb-5 custom-scrollbar space-y-2">
                @forelse($menus as $menu)
                <div x-show="'{{ addslashes(strtolower($menu->nama_menu)) }}'.includes(searchQuery.toLowerCase())"
                     @click="activeMenu = {id: '{{ $menu->id_menu }}', nama: '{{ addslashes($menu->nama_menu) }}', harga: {{ (float)$menu->harga_menu }} }; openDetail = true; qty = 1; selectedToppings = [];" 
                     class="cursor-pointer group bg-white border border-slate-100 rounded-xl p-3 flex justify-between items-center hover:border-teal-500 hover:shadow-md transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center group-hover:bg-teal-50 transition-colors">
                            <i class="fa-solid fa-utensils text-slate-300 group-hover:text-teal-600"></i>
                        </div>
                        <div>
                            <span class="block font-bold text-sm text-slate-800">{{ $menu->nama_menu }}</span>
                            <span class="text-teal-700 font-bold text-xs">Rp {{ number_format($menu->harga_menu) }}</span>
                        </div>
                    </div>
                    <i class="fa-solid fa-circle-plus text-slate-200 text-xl group-hover:text-teal-600 transition-colors"></i>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center h-48 opacity-60">
                    <i class="fa-solid fa-triangle-exclamation text-3xl text-amber-400 mb-3"></i>
                    <p class="text-sm font-bold text-slate-500 text-center">Belum ada menu aktif</p>
                    <p class="text-xs text-slate-400 text-center mt-1">Hubungi Admin untuk mengaktifkan menu di cabang ini.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Kolom Detail Pesanan -->
        <div class="w-full lg:w-5/12 bg-white rounded-2xl shadow-sm border border-slate-200 flex-col overflow-hidden"
             :class="activeTab === 'cart' ? 'flex h-[calc(100vh-210px)] lg:h-full' : 'hidden lg:flex'">
            
            <div class="p-5 border-b border-slate-50 flex justify-between items-center">
                <div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-800 uppercase tracking-tight">Detail Pesanan</h2>
                    <span class="text-[10px] text-teal-600 font-bold uppercase"><i class="fa-solid fa-store mr-1"></i>{{ Auth::user()->cabang->nama_cabang ?? 'Pusat' }}</span>
                </div>
                <template x-if="cart.length > 0">
                    <button type="button" @click="clearEntireCart()" class="text-[10px] font-bold text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-2.5 py-1.5 rounded-lg transition-colors uppercase flex items-center gap-1">
                        <i class="fa-solid fa-broom"></i> Kosongkan
                    </button>
                </template>
            </div>
            
            <form id="formCheckout" action="{{ route('kasir.pesan.checkout') }}" method="POST" class="flex-1 flex flex-col overflow-hidden">
                @csrf
                
                <div class="flex-1 overflow-y-auto px-5 py-4 custom-scrollbar bg-slate-50/30">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="flex justify-between items-center bg-white p-3 rounded-xl border border-slate-100 shadow-sm mb-2">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-xs font-bold text-slate-800 truncate uppercase" x-text="item.nama_menu"></h3>
                                <p class="text-[9px] text-slate-500 font-medium truncate" 
                                   x-text="item.toppings.length > 0 ? item.toppings.map(t => t.nama_toping).join(', ') : 'ORIGINAL'"></p>
                                <p class="text-teal-700 font-bold text-[11px] mt-0.5" x-text="'Rp ' + parseInt(item.subtotal).toLocaleString('id-ID')"></p>
                            </div>
                            
                            <div class="flex items-center gap-2 ml-3">
                                <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 px-2 py-1 rounded-lg text-[10px]">
                                    <button type="button" @click="changeQty(index, 'minus')" class="text-slate-400 font-bold hover:text-red-500 transition-colors px-1">-</button>
                                    <span class="font-bold text-slate-800 w-4 text-center" x-text="item.qty"></span>
                                    <button type="button" @click="changeQty(index, 'plus')" class="text-slate-400 font-bold hover:text-teal-600 transition-colors px-1">+</button>
                                </div>
                                <button type="button" @click="removeMenu(index)" class="text-slate-300 hover:text-red-500 transition-colors">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                    
                    <template x-if="cart.length === 0">
                        <div class="h-full flex flex-col items-center justify-center opacity-35 py-12">
                            <i class="fa-solid fa-cart-shopping text-3xl text-slate-300 mb-2"></i>
                            <p class="text-[10px] font-bold uppercase text-slate-400">Keranjang Kosong</p>
                        </div>
                    </template>
                </div>

                <div x-show="cart.length > 0" class="p-5 border-t border-slate-100 bg-white space-y-3 shadow-[0_-4px_10px_-5px_rgba(0,0,0,0.1)]">
                    <input type="text" name="nama_pelanggan" placeholder="NAMA PELANGGAN" required 
                        class="w-full bg-slate-50 border border-slate-200 focus:border-teal-500 rounded-lg px-3 py-2 text-xs font-bold outline-none transition-all uppercase">
                    
                    <div class="grid grid-cols-2 gap-2" x-data="{ metode: '' }">
                        @foreach(['QRIS', 'CASH'] as $metode)
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis_pembayaran" value="{{ $metode }}" x-model="metode" class="hidden peer" required>
                            <div class="py-2 border border-slate-200 rounded-lg text-center font-bold text-[10px] text-slate-400 peer-checked:border-teal-600 peer-checked:bg-teal-600 peer-checked:text-white transition-all uppercase shadow-sm">
                                {{ $metode }}
                            </div>
                        </label>
                        @endforeach

                        <div x-show="metode === 'CASH'" x-transition class="col-span-2 space-y-2 mt-1">
                            <!-- 1. Input Tampilan -->
                            <input type="text" 
                                   id="display_uang_tunai" 
                                   placeholder="NOMINAL UANG..." 
                                   class="w-full bg-white border border-teal-200 rounded-lg px-3 py-2 text-xs font-bold outline-none"
                                   oninput="
                                       let val = this.value.replace(/\D/g, '');
                                       this.value = new Intl.NumberFormat('id-ID').format(val);
                                       let realInput = document.getElementById('input_uang_tunai');
                                       realInput.value = val;
                                       realInput.dispatchEvent(new Event('input'));
                                   ">

                            <!-- 2. Input Hidden -->
                            <input type="hidden" name="uang_tunai" id="input_uang_tunai">

                            <div class="flex justify-between px-1 text-[9px] font-bold uppercase">
                                <span class="text-slate-400">Kembalian</span>
                                <span id="text_kembalian" class="text-green-600 font-bold">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-500 font-bold text-[10px] uppercase">Grand Total</span>
                        <span id="grand_total_val" :data-total="getCartSubtotal()" class="text-slate-900 font-bold text-xl tracking-tighter" x-text="'Rp ' + getCartSubtotal().toLocaleString('id-ID')"></span>
                    </div>

                    <button type="submit" class="w-full bg-teal-600 text-white font-bold text-sm py-3 rounded-xl hover:bg-teal-700 shadow-lg shadow-teal-100 uppercase tracking-wider transition-all active:scale-[0.98]">
                        Proses Checkout
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail & Toppings -->
    <template x-if="openDetail">
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-[2px]" @click="openDetail = false"></div>
            <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 overflow-hidden">
                <form action="{{ route('kasir.pesan.add') }}" method="POST" @submit.prevent="addMenuToCart">
                    @csrf
                    <input type="hidden" name="menu_id" :value="activeMenu.id">
                    <div class="flex justify-between items-center mb-5 border-b pb-4">
                        <h2 class="text-lg font-bold text-slate-800" x-text="activeMenu.nama"></h2>
                        <button type="button" @click="openDetail = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-times"></i></button>
                    </div>

                    <div class="max-h-[35vh] overflow-y-auto pr-1 custom-scrollbar mb-6">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-3 tracking-widest">Pilih Topping</p>
                        <div class="grid grid-cols-1 gap-2">
                            @foreach($toppings as $top)
                            <label class="relative flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-slate-50 cursor-pointer has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50 transition-all shadow-sm">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="toppings[]" value="{{ $top->id_toping }}" x-model="selectedToppings" class="w-4 h-4 rounded text-teal-600 border-slate-300">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="text-xs font-bold text-slate-700 uppercase">{{ $top->nama_toping }}</span>
                                        @if($top->bisa_gratis)
                                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-teal-100 text-teal-700">Bisa Promo</span>
                                        @endif
                                    </div>
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
                        <button type="submit" class="flex-[2] bg-teal-600 text-white font-bold py-3 rounded-xl flex justify-center items-center hover:bg-teal-700 transition-all shadow-lg text-xs uppercase tracking-wider">
                            TAMBAH - <span class="ml-1" x-text="'Rp ' + calculateTotal().toLocaleString('id-ID')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

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

                // Konfirmasi & Kirim
                Swal.fire({
                    title: 'Konfirmasi Bayar',
                    text: "Proses transaksi sekarang?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d9488', // teal-600
                    confirmButtonText: 'Ya, Checkout!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
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
                                // Cetak struk di tab yang sama
                                window.location.href = `/kasir/pesan/print/${data.id_penjualan}`;
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

    // Clear State saat user keluar dan kembali lagi (Back / Forward / bfcache)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
@endsection