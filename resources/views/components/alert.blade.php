@props([
    'type' => 'modal', // Defaultnya modal
    'id' => null,
    'name' => null,
    'action' => null,
    'title' => null,
    'value' => null,
    'svgPath' => null,
    'iconColor' => '#7AB465'
])

@if($type == 'stats')
    {{-- Tampilan STATISTIK --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative">
        <p class="text-sm text-gray-500 font-medium">{{ $title }}</p>
        <div class="flex items-center justify-between">
            <h3 class="text-3xl font-bold mt-1 text-gray-800">{{ $value }}</h3>
            <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" 
                 fill="none" stroke="{{ $iconColor }}" stroke-width="2" 
                 stroke-linecap="round" stroke-linejoin="round">
                {!! $svgPath !!}
            </svg>
        </div>
    </div>

@else
    {{-- Tampilan MODAL HAPUS --}}
    <x-modal id="hapus-{{ $id }}" title="Konfirmasi Hapus">
        <p class="text-gray-600">
            Apakah Anda yakin ingin menghapus <b>{{ $name }}</b>?
        </p>
        <x-slot name="footer">
            <button @click="open = false" class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50">
                Batal
            </button>
            
            <form action="{{ $action }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-red-700">
                    Hapus Sekarang
                </button>
            </form>
        </x-slot>
    </x-modal>
@endif