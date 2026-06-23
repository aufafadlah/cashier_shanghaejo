@extends('layouts.kasir')
@section('title', 'Profile')
@section('content')
<div class="container-fluid p-6 bg-[#F8FAFC] min-h-screen flex flex-col items-center">
    {{-- Header --}}
    <div class="w-full max-w-4xl mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Profile</h1>
    </div>

    {{-- CARD PROFILE --}}
    <div class="bg-white w-full max-w-4xl rounded-[2rem] p-8 shadow-md flex justify-between items-center border border-gray-100">
        
        {{-- FORM SECTION (KIRI) --}}
        <form action="{{ route('admin.profile.update') }}" method="POST" class="w-1/2 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nama</label>
                <input type="text" name="name" value="{{ $user->name }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400 outline-none transition-all text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Username</label>
                <input type="text" name="username" value="{{ $user->username }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400 outline-none transition-all text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jabatan/Posisi</label>
                <input type="text" value="{{ $user->role }}" disabled class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 text-gray-400 rounded-lg cursor-not-allowed text-sm">
            </div>

            <div class="pt-2">
                <a href="javascript:void(0)" onclick="openPasswordModal()" class="text-teal-600 text-xs hover:underline decoration-2">
                    Lupa password? <span class="font-bold">Ubah Password Baru</span>
                </a>
            </div>

            <button type="submit" class="w-full py-3 bg-[#7AB465] text-white font-bold rounded-xl shadow-md hover:bg-green-600 transition-all transform active:scale-95 text-sm">
                Simpan Perubahan
            </button>
        </form>

        {{-- ICON SECTION (KANAN) --}}
        <div class="w-1/3 flex flex-col items-center">
            <div class="w-48 h-48 bg-gray-50 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                {{-- Icon User --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-gray-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <p class="mt-4 text-gray-400 text-xs font-medium">Administrator System</p>
        </div>
    </div>
</div>

{{-- MODAL UBAH PASSWORD (Lebih Kecil) --}}
<div id="passwordModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex justify-center items-center">
    <div class="bg-white w-[350px] rounded-2xl p-6 shadow-xl">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Password Baru</h2>
        <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Password Baru</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none text-sm focus:border-teal-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none text-sm focus:border-teal-500">
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closePasswordModal()" class="flex-1 py-2 text-gray-500 font-semibold text-sm hover:bg-gray-100 rounded-lg">Batal</button>
                <button type="submit" class="flex-1 py-2 bg-[#7AB465] text-white font-bold rounded-lg text-sm shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPasswordModal() { document.getElementById('passwordModal').classList.remove('hidden'); }
    function closePasswordModal() { document.getElementById('passwordModal').classList.add('hidden'); }
</script>
@endsection