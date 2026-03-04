<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex">
    <aside class="w-64 bg-slate-900 h-screen fixed text-white">
        <nav class="p-4">
             <a href="{{ route('admin.dashboard') }}" class="block p-2 hover:bg-slate-700 rounded">📊 Dashboard</a>
             <a href="{{ route('cabang.index') }}" class="block p-2 hover:bg-slate-700 rounded">🏢 Kelola Cabang</a>
             </nav>
    </aside>

    <main class="flex-1 ml-64 p-10">
        @yield('content') 
    </main>
</body>
</html>