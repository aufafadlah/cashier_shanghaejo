<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Masuk ke sistem Kasir ShangHaejo. Aplikasi Point of Sale (POS) dan pengelolaan transaksi restoran terintegrasi.">
    <title>Login - Cashier Shanghaejo</title>
    
    <!-- Premium Fonts dengan display=swap -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen font-sans p-4">

    <main class="bg-white p-8 sm:p-10 rounded-3xl shadow-xl w-full max-w-md border border-slate-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black text-teal-700 tracking-tight">SHANGHAEJO</h1>
            <p class="text-slate-600 text-sm font-medium mt-1">Masuk ke akun kasir & admin</p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 text-red-700 p-4 rounded-xl text-sm mb-6 border border-red-100 font-medium">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-6" x-data="{ showPassword: false }">
            @csrf
            <div>
                <label for="username" class="block text-xs uppercase tracking-widest font-bold text-slate-700 mb-2">Username</label>
                <input type="text" id="username" name="username" placeholder="Masukkan Username" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 focus:ring-4 focus:ring-teal-50 focus:border-teal-500 focus:outline-none transition-all text-slate-900 font-medium">
            </div>

            <div>
                <label for="password" class="block text-xs uppercase tracking-widest font-bold text-slate-700 mb-2">Password</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" 
                           id="password"
                           name="password" 
                           placeholder="••••••••" 
                           required
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-5 pr-12 py-3.5 focus:ring-4 focus:ring-teal-50 focus:border-teal-500 focus:outline-none transition-all text-slate-900 font-medium">
                    
                    <button type="button" 
                            @click="showPassword = !showPassword" 
                            aria-label="Tampilkan atau sembunyikan password"
                            :aria-label="showPassword ? 'Sembunyikan password' : 'Lihat password'"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 rounded-lg p-1.5 transition-colors">
                        {{-- Icon Eye SVG: Sangat ringan, 0 network blocking, tanpa cookie pihak ketiga --}}
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" x-cloak>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-teal-700 hover:bg-teal-800 text-white py-4 rounded-2xl font-bold shadow-lg shadow-teal-700/20 active:scale-[0.98] transition-all duration-200">
                Masuk ke Sistem
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-slate-500 text-xs font-medium">
                Authorized Personnel Only &copy; 2026 ShangHaejo
            </p>
        </div>
    </main>

</body>
</html>