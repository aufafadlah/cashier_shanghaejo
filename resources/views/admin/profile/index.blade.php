@extends('layouts.admin')
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
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400 outline-none transition-all text-sm">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400 outline-none transition-all text-sm">
                @error('username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Jabatan/Posisi</label>
                <input type="text" value="{{ strtoupper($user->role) }}" disabled
                       class="w-full px-4 py-2.5 border border-gray-200 bg-gray-50 text-gray-400 rounded-lg cursor-not-allowed text-sm">
            </div>

            <div class="pt-2">
                <a href="javascript:void(0)" onclick="openPasswordModal()" class="text-teal-600 text-xs hover:underline decoration-2">
                    Ganti password? <span class="font-bold">Ubah Password Sekarang</span>
                </a>
            </div>

            @if(session('success'))
            <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-xs text-green-700 font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-green-500"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i> {{ session('error') }}
            </div>
            @endif

            <button type="submit" class="w-full py-3 bg-[#7AB465] text-white font-bold rounded-xl shadow-md hover:bg-green-600 transition-all transform active:scale-95 text-sm">
                Simpan Perubahan
            </button>
        </form>

        {{-- ICON SECTION (KANAN) --}}
        <div class="w-1/3 flex flex-col items-center">
            <div class="w-48 h-48 bg-gray-50 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 text-gray-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <p class="mt-4 text-slate-700 text-sm font-bold">{{ $user->name }}</p>
            <span class="mt-1 text-xs bg-teal-50 text-teal-700 px-3 py-1 rounded-full font-bold uppercase tracking-wider border border-teal-100">
                {{ strtoupper($user->role) }}
            </span>
        </div>
    </div>
</div>

{{-- MODAL UBAH PASSWORD ADMIN --}}
<div id="passwordModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex justify-center items-center p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-xl" x-data="{ showOld: false, showNew: false, showConfirm: false }">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-xl font-bold text-gray-800">Ubah Password</h2>
            <button type="button" onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
        </div>
        <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')

            {{-- Password Lama --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Password Saat Ini</label>
                <div class="relative">
                    <input :type="showOld ? 'text' : 'password'" name="current_password" required
                           class="w-full px-3 py-2.5 pr-10 border border-gray-300 rounded-lg outline-none text-sm focus:border-teal-500 transition-colors">
                    <button type="button" @click="showOld = !showOld"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-600 transition-colors">
                        <i class="fa-solid" :class="showOld ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Password Baru --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Password Baru</label>
                <div class="relative">
                    <input :type="showNew ? 'text' : 'password'" name="password" required minlength="6"
                           class="w-full px-3 py-2.5 pr-10 border border-gray-300 rounded-lg outline-none text-sm focus:border-teal-500 transition-colors">
                    <button type="button" @click="showNew = !showNew"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-600 transition-colors">
                        <i class="fa-solid" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Konfirmasi --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Konfirmasi Password</label>
                <div class="relative">
                    <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required minlength="6"
                           class="w-full px-3 py-2.5 pr-10 border border-gray-300 rounded-lg outline-none text-sm focus:border-teal-500 transition-colors">
                    <button type="button" @click="showConfirm = !showConfirm"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-600 transition-colors">
                        <i class="fa-solid" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>
            <p class="text-[10px] text-gray-400">Minimal 6 karakter</p>

            <div class="flex gap-2 pt-1">
                <button type="button" onclick="closePasswordModal()"
                        class="flex-1 py-2.5 text-gray-500 font-semibold text-sm hover:bg-gray-100 rounded-lg transition-colors border border-gray-200">Batal</button>
                <button type="submit"
                        class="flex-1 py-2.5 bg-teal-600 text-white font-bold rounded-lg text-sm shadow-sm hover:bg-teal-700 transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPasswordModal() { document.getElementById('passwordModal').classList.remove('hidden'); }
    function closePasswordModal() { document.getElementById('passwordModal').classList.add('hidden'); }
    @if($errors->has('password') || $errors->has('current_password'))
        document.addEventListener('DOMContentLoaded', function() { openPasswordModal(); });
    @endif
</script>
@endsection