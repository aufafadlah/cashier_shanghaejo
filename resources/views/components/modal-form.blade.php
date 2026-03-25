@props(['title', 'action', 'method' => 'POST', 'cancelUrl'])

{{-- Overlay: Menggunakan fixed dan bg-black/40 sesuai kode manual kamu --}}
<div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    
    {{-- Card Container: w-[420px] dan rounded-xl sesuai keinginanmu --}}
    <div class="bg-white w-full max-w-[420px] rounded-xl shadow-2xl overflow-hidden border border-gray-100">
        
        {{-- Header: Konsisten dengan header tabel (bg-gray-50) --}}
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 text-center">
            <h2 class="text-lg font-semibold text-gray-800 uppercase tracking-wide">
                {{ $title }}
            </h2>
        </div>

        <div class="p-6">
            <form action="{{ $action }}" method="POST" class="space-y-4">
                @csrf
                @if($method !== 'POST')
                    @method($method)
                @endif

                {{-- Slot Utama untuk Input-input kamu --}}
                <div class="space-y-4">
                    {{ $slot }}
                </div>

                {{-- Footer Buttons: Rapih dan Konsisten --}}
                <div class="flex justify-end gap-2 pt-6 border-t mt-6">
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
</div>