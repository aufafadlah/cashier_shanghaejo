@extends('layouts.app')

@section('content')
<div class="container-fluid p-6 bg-[#F8FAFC] min-h-screen">
    {{-- TITLE & ADD USER BUTTON --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola User</h1>
            <p class="text-sm text-gray-400 mt-1">Kelola anggota tim Anda dan izin akun mereka</p>
        </div>

        <a href="{{ route('user.create') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl shadow-sm hover:bg-gray-50 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-[#E15B5B]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            Add User
        </a>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 font-medium">Total User</p>
            <h3 class="text-4xl font-bold text-gray-800 mt-2">{{ $totalUsers ?? '0' }}</h3>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500 font-medium">Aktif</p>
                <h3 class="text-4xl font-bold text-gray-800 mt-2">{{ $totalAktif ?? '0' }}</h3>
            </div>
            <div class="text-[#7AB465]">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500 font-medium">Kasir</p>
                <h3 class="text-4xl font-bold text-gray-800 mt-2">{{ $totalKasir ?? '0' }}</h3>
            </div>
            <div class="text-[#7AB465]">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50/50">
                <tr class="text-left text-gray-600 font-semibold text-sm">
                    <th class="px-6 py-4">Nama</th>
                    <th class="px-6 py-4">Username</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-5 font-medium text-gray-700 text-lg">{{ $user->name }}</td>
                    <td class="px-6 py-5 text-gray-700 text-lg">{{ $user->username }}</td>
                    <td class="px-6 py-5 text-gray-600 text-lg capitalize">{{ $user->role ?? 'Kasir' }}</td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-700 text-lg">{{ $user->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                            <span class="w-3 h-3 inline-block {{ $user->is_active ? 'bg-[#7AB465]' : 'bg-red-500' }} rounded-full"></span>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-center relative">
                        {{-- Tombol Titik Tiga --}}
                        <button type="button" onclick="toggleMenu('{{ $user->id_user }}')" class="p-2 rounded-full hover:bg-gray-100 transition-colors focus:outline-none">
                            <span class="text-xl font-bold text-gray-600">⋮</span>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div id="menu-{{ $user->id_user }}" class="hidden absolute right-10 top-12 w-40 bg-white border border-gray-100 rounded-xl shadow-xl z-50 overflow-hidden">
                            <a href="{{ route('user.edit', $user->id_user) }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('user.destroy', $user->id_user) }}" method="POST" class="border-t border-gray-50">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin hapus user ini?')" class="flex items-center gap-3 w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    🗑 Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-20 text-center text-gray-400 text-lg">Tidak ada data user yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleMenu(id) {
        const menu = document.getElementById('menu-' + id);
        // Sembunyikan semua menu lain
        document.querySelectorAll('[id^="menu-"]').forEach(m => {
            if (m.id !== 'menu-' + id) m.classList.add('hidden');
        });
        // Buka/Tutup menu yang diklik
        menu.classList.toggle('hidden');
    }

    // Klik di luar menu untuk menutup secara otomatis
    window.onclick = function(event) {
        if (!event.target.matches('button') && !event.target.closest('button')) {
            document.querySelectorAll('[id^="menu-"]').forEach(m => m.classList.add('hidden'));
        }
    }
</script>
@endsection