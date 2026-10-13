{{-- TABEL  --}}
<div class="relative rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
    {{-- Wrapper untuk Scroll Horizontal & Vertikal --}}
    <div class="overflow-x-auto overflow-y-auto max-h-[560px] relative custom-scrollbar">
        <table class="w-full text-sm text-left text-gray-500 border-collapse min-w-[640px] md:min-w-full">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200 sticky top-0 z-10 shadow-sm">
                {{ $header }}
            </thead>
            <tbody class="divide-y divide-gray-200">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    @if(isset($pagination) || isset($footer))
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $pagination ?? $footer }}
        </div>
    @endif
</div>

<style>
    /* Agar scrollbar terlihat lebih modern dan rapi */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f9fafb;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>