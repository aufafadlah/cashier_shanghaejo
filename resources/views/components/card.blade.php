@props(['title', 'value', 'type' => 'total'])

@php
    // Konfigurasi warna dan icon otomatis
    $config = [
        'total' => [
            'color' => '#4B5563', 
            'path' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>'
        ],
        'aktif' => [
            'color' => '#7AB465', 
            'path' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>'
        ],
        'non-aktif' => [
            'color' => '#CE484A', 
            'path' => '<circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>'
        ],
    ];

    $style = $config[$type] ?? $config['total'];
@endphp

<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative overflow-hidden hover:shadow-md transition-all duration-300">
    {{-- Garis aksen vertikal di samping --}}
    <div class="absolute top-0 left-0 w-1 h-full" style="background-color: {{ $style['color'] }}"></div>
    
    <p class="text-sm text-gray-500 font-medium">{{ $title }}</p>
    <div class="flex items-center justify-between mt-1">
        <h3 class="text-3xl font-bold text-gray-800">{{ $value }}</h3>
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="{{ $style['color'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            {!! $style['path'] !!}
        </svg>
    </div>
</div>