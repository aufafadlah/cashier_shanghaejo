@props(['title', 'action', 'method' => 'POST', 'cancelUrl'])

{{-- Overlay --}}
<div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    
    {{-- Card Container: Diubah dari max-w-[420px] ke max-w-[500px] agar input harga muat, 
         ditambahkan max-h-[90vh] dan flex-col agar tingginya pas dengan layar browser --}}
    <div class="bg-white w-full max-w-[500px] max-h-[90vh] rounded-xl shadow-2xl overflow-hidden border border-gray-100 flex flex-col">
        
        {{-- Header (Tetap diam di atas) --}}
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 text-center flex-shrink-0">
            <h2 class="text-lg font-semibold text-gray-800 uppercase tracking-wide">
                {{ $title }}
            </h2>
        </div>

        {{-- Form Area: Kita bagi menjadi area scrollable --}}
        <form action="{{ $action }}" method="POST" class="flex flex-col flex-grow overflow-hidden">
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif

            {{-- Body Utama: KUNCI SUKSES SCROLL ADA DI SINI (overflow-y-auto) --}}
            <div class="p-6 space-y-4 overflow-y-auto flex-grow max-h-[calc(90vh-140px)]">
                {{-- Slot Utama untuk Input-input kamu --}}
                <div class="space-y-4">
                    {{ $slot }}
                </div>
            </div>

            {{-- Footer Buttons (Tetap diam di bawah, tidak ikut ter-scroll) --}}
            <div class="flex justify-end gap-2 px-6 py-4 bg-gray-50 border-t flex-shrink-0">
                {{-- Tombol Batal --}}
                <a href="{{ $cancelUrl }}" 
                   class="px-4 py-2 bg-red-500 text-white text-sm font-bold rounded-md hover:bg-red-600 transition-all text-center">
                    Batal
                </a>
                
                {{-- Tombol Simpan/Update --}}
                <button type="submit" 
                        class="px-4 py-2 bg-green-500 text-white text-sm font-bold rounded-md hover:bg-green-600 transition-all shadow-md">
                    {{ $method === 'POST' ? 'Simpan' : 'Update' }}
                </button>
            </div>
        </form>
        
    </div>
</div>