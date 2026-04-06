<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'POS Resto') | ShangHaejo</title>
    
    {{-- Libraries --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>

<body class="bg-gray-50 font-sans">
    <div class="flex">
        {{-- Sidebar Kiri --}}
        @include('components.sidebar')

        {{-- Area Utama --}}
        <main class="flex-1 ml-64 min-h-screen flex flex-col">
            {{-- Navbar Atas --}}
            @include('components.navbar')

            {{-- Isi Konten Halaman --}}
            <div class="p-8 flex-1">
                @yield('content')
            </div>

            {{-- Footer Bawah --}}
            @include('components.footer')
        </main>
    </div>

    @stack('scripts')
</body>
</html>