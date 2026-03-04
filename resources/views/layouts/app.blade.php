<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Judul Halaman (berubah tiap halaman) -->
    <title>@yield('title', 'POS Resto')</title>
    
    <!-- Tailwind CSS (untuk styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js (untuk interaksi JavaScript ringan) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js (untuk grafik) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- SweetAlert2 (untuk notifikasi cantik) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <!-- Tempat untuk CSS tambahan per halaman -->
    @stack('styles')
</head>
<body class="bg-gray-100">
    
    <!-- Tempat untuk content per halaman -->
    @yield('content')
    
    <!-- Tempat untuk JavaScript tambahan per halaman -->
    @stack('scripts')

    
</body>
</html>