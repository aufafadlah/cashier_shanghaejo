@extends('layouts.app')

@section('content')
<div class="container-fluid p-10 bg-[#F8FAFC] min-h-screen flex flex-col items-center">
    <h1 class="text-3xl font-bold text-gray-800 self-start mb-8">Profile</h1>

    {{-- CARD PROFILE --}}
    <div class="bg-white w-full max-w-5xl rounded-[3rem] p-12 shadow-[0_35px_60px_-15px_rgba(0,0,0,0.1)] flex justify-between items-center border border-gray-50">
        
        {{-- FORM SECTION (KIRI) --}}
        <form action="{{ route('admin.profile.update') }}" method="POST" class="w-1/2 space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">Nama</label>
                <input type="text" name="name" value="{{ $user->name }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-400 outline-none transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">Username</label>
                <input type="text" name="username" value="{{ $user->username }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-400 outline-none transition-all">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-600 mb-2">Jabatan/Posisi</label>
                <input type="text" value="{{ $user->role }}" disabled class="w-full px-4 py-3 border border-gray-200 bg-gray-50 text-gray-400 rounded-xl cursor-not-allowed">
            </div>

            <div class="pt-4">
                <a href="javascript:void(0)" onclick="openPasswordModal()" class="text-blue-600 text-sm hover:underline">Lupa password? <span class="font-bold">Password Baru</span></a>
            </div>

            <button type="submit" class="w-full py-4 bg-[#7AB465] text-white font-bold rounded-2xl shadow-lg hover:bg-green-600 transition-all transform hover:scale-[1.02]">
                Simpan
            </button>
        </form>

        {{-- ICON SECTION (KANAN) --}}
        <div class="w-1/3 flex flex-col items-center">
            <div class="w-64 h-64 bg-gray-100 rounded-full flex items-center justify-center border-8 border-gray-50 shadow-inner">
                {{-- Icon User --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-40 h-40 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- MODAL UBAH PASSWORD (image_9f2e1e) --}}
<div id="passwordModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex justify-center items-center">
    <div class="bg-white w-[400px] rounded-[2rem] p-8 shadow-2xl">
        <h2 class="text-2xl font-bold mb-6">Password Baru</h2>
        <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Password Baru</label>
                <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-xl outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Konfirmasi</label>
                <input type="password" name="password_confirmation" required class="w-full px-4 py-3 border border-gray-300 rounded-xl outline-none">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closePasswordModal()" class="flex-1 py-3 bg-red-500 text-white font-bold rounded-xl">Batal</button>
                <button type="submit" class="flex-1 py-3 bg-[#7AB465] text-white font-bold rounded-xl">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPasswordModal() { document.getElementById('passwordModal').classList.remove('hidden'); }
    function closePasswordModal() { document.getElementById('passwordModal').classList.add('hidden'); }
</script>
@endsection