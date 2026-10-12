<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cashier Shanghaejo</title>
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen font-sans">

    <div class="bg-white p-10 rounded-3xl shadow-xl w-full max-w-md border border-slate-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black text-teal-600 tracking-tight">SHANGHAEJO</h1>
            <p class="text-slate-400 text-sm font-medium mt-1">Log in to your account</p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm mb-6 border border-red-100 font-medium">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-6" x-data="{ showPassword: false }">
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-widest font-bold text-slate-400 mb-2">Username</label>
                <input type="text" name="username" placeholder="Masukkan Username" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 focus:ring-4 focus:ring-teal-50 focus:border-teal-500 focus:outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-widest font-bold text-slate-400 mb-2">Password</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" 
                           name="password" 
                           placeholder="••••••••" 
                           required
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-5 pr-12 py-3.5 focus:ring-4 focus:ring-teal-50 focus:border-teal-500 focus:outline-none transition-all">
                    <button type="button" 
                            @click="showPassword = !showPassword" 
                            tabindex="-1"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-teal-600 focus:outline-none p-1 transition-colors">
                        <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <button type="submit" 
                class="w-full bg-teal-600 hover:bg-teal-700 text-white py-4 rounded-2xl font-bold shadow-lg shadow-teal-600/10 hover:shadow-teal-600/20 active:scale-[0.98] transition-all duration-200">
                Masuk ke Sistem
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-slate-400 text-[10px] uppercase tracking-tighter italic">
                Authorized Personnel Only &copy; 2026
            </p>
        </div>
    </div>

</body>
</html>