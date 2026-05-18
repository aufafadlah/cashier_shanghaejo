<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') | ShangHaejo</title>
     <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="bg-gray-50 font-sans">
    <div class="flex">

        @include('components.admin.sidebar')

        <main class="flex-1 ml-60 min-h-screen flex flex-col">

            @include('components.admin.navbar')

            <div class="p-8 pt-16 flex-1 overflow-hidden">
                @yield('content')
            </div>

            @include('components.footer')

        </main>
    </div>
</body>
</html>