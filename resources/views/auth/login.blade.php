<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cashier Shanghaejo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-10 rounded-3xl shadow-2xl w-full max-w-md border border-slate-200">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black text-green-600 tracking-tight">SHANGHAEJO</h1>
            <p class="text-slate-400 text-sm font-medium mt-1">Log in to your account"</p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 text-red-500 p-4 rounded-xl text-sm mb-6 border border-red-100 font-medium">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs uppercase tracking-widest font-bold text-slate-500 mb-2">Username</label>
                <input type="text" name="username" placeholder="Masukkan Username" 
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 focus:ring-4 focus:ring-green-100 focus:border-green-500 focus:outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs uppercase tracking-widest font-bold text-slate-500 mb-2">Password</label>
                <input type="password" name="password" placeholder="••••••••"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 focus:ring-4 focus:ring-green-100 focus:border-green-500 focus:outline-none transition-all">
            </div>

            <button type="submit" 
                class="w-full bg-green-600 text-white py-4 rounded-2xl font-bold shadow-lg ">
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