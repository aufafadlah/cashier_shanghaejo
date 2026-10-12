<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'POS Resto') | ShangHaejo</title>

    {{-- Libraries --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>

<body class="bg-gray-50 font-sans">

    <div class="flex">

        {{-- Sidebar --}}
        @if(auth()->check() && auth()->user()->role == 'admin')
            @include('components.admin.sidebar')
        @else
            @include('components.kasir.sidebar')
        @endif

        {{-- Main Content --}}
        <main class="flex-1 ml-60 min-h-screen flex flex-col">

            {{-- Navbar --}}
            @if(auth()->check() && auth()->user()->role == 'admin')
                @include('components.admin.navbar')
            @else
                @include('components.kasir.navbar')
            @endif

            {{-- Content --}}
            <div class="p-8 pt-16 flex-1 overflow-hidden">
                @yield('content')
            </div>

            {{-- Footer --}}
            <div class="p-2 pb-1">
                @include('components.footer')
            </div>

        </main>

    </div>

    @stack('scripts')

</body>
</html>