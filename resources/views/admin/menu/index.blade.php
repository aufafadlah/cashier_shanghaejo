@extends('layouts.app') {{-- Memanggil Sidebar & Navbar --}}

@section('content')
<div class="container-fluid p-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Menu</h1>
            <p class="text-xs text-gray-400 mt-1">Daftar produk Pisang Ijo Shang Haejo Cicio</p>
        </div>
@if(session('success'))
    <div id="alert-success" class="flex items-center p-4 mb-6 text-green-800 rounded-2xl bg-green-50 border border-green-100 shadow-sm animate-bounce-short" role="alert">
        <svg class="flex-shrink-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
        </svg>
        <div class="ms-3 text-sm font-bold">
            {{ session('success') }}
        </div>
        <button type="button" class="ms-auto bg-green-50 text-green-500 rounded-lg p-1.5 hover:bg-green-100 h-8 w-8 inline-flex items-center justify-center" onclick="document.getElementById('alert-success').remove()">
            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    </div>
    @endif
        <button class="inline-flex items-center px-4 py-2 bg-white text-black text-sm font-medium rounded-lg shadow-sm border border-gray-200">
            <span class="mr-2 text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" 
                fill="none" stroke="#CE484A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <line x1="19" y1="8" x2="19" y2="14"></line>
                    <line x1="22" y1="11" x2="16" y2="11"></line>
                </svg>
            </span> 
            <a href="{{ route('menu.create') }}" class="btn btn-primary">Add Menu</a>
        </button>
    </div>

    {{-- BAGIAN HEADER STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        {{-- Total Menu --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative">
            <p class="text-sm text-gray-500 font-medium">Total Menu</p>
            <div class="flex items-center gap-3">
                <h3 class="text-3xl font-bold mt-1 justify-between w-full">{{ $totalMenu }}</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" 
                fill="none" stroke="#7AB465" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
        </div>

        {{-- Aktif --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative">
            <p class="text-sm text-gray-500 font-medium text-green-600">Aktif</p>
            <div class="flex items-center gap-3">
                <h3 class="text-3xl font-bold mt-1 w-full">{{ $aktif }}</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none" 
                stroke="#7AB465" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
        </div>

        {{-- Non Aktif --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative">
            <p class="text-sm text-gray-500 font-medium text-red-500">Non Aktif</p>
            <div class="flex items-center gap-3">
                <h3 class="text-3xl font-bold mt-1 w-full">{{ $nonAktif }}</h3>
                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="#CE484A" 
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="15" y1="9" x2="9" y2="15"></line>
                    <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>
            </div>
        </div>
    </div>

    {{-- TABLE MENU --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative">
        <table class="w-full text-sm text-left text-body">
            <thead class="bg-gray-50 border-b border-t border-gray-200">
                <tr>
                    <th class="px-6 py-3 font-medium">No</th>
                    <th class="px-6 py-3 font-medium">Nama Menu</th>
                    <th class="px-6 py-3 font-medium">Harga</th>
                    <th class="px-6 py-3 font-medium text-center">Status</th>
                    <th class="px-6 py-3 font-medium">Tanggal Terdaftar</th>
                    <th class="px-6 py-3 font-medium text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($menus as $index => $menu)
                <tr class="bg-white border-b hover:bg-gray-50 transition">
                    
                    <td class="px-6 py-4">{{ $index + 1 }}</td>

                    <td class="px-6 py-4 font-medium text-gray-900">
                        {{ $menu->nama_menu }}
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        Rp {{ number_format($menu->harga_menu, 0, ',', '.') }}
                    </td>

                    <td class="px-6 py-4 text-center">
                        @if($menu->is_active == 1)
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                Aktif
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                Non-Aktif
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($menu->created_at)->translatedFormat('d F Y') }}
                    </td>

                    <td class="px-6 py-4 text-center relative">
                        <button type="button" onclick="toggleMenu({{ $menu->id_menu }})" class="p-2 rounded hover:bg-gray-200 focus:outline-none">
                            ⋮
                        </button>

                        <div id="menu-{{ $menu->id_menu }}" class="hidden absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow-xl z-[100]">
                            <a href="{{ route('menu.edit', $menu->id_menu) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left">
                                ✏️ Edit Data
                            </a>

                            <div class="border-t border-gray-100"></div>

                            <form action="{{ route('menu.destroy', $menu->id_menu) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus menu ini?')" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    🗑 Hapus Menu
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-400 font-medium">
                        <div class="flex flex-col items-center">
                            <span>Belum ada data menu tersedia.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<script>
let currentDeleteId = null;

// Fungsi Dropdown ⋮
function toggleMenu(id) {
    const menu = document.getElementById('menu-' + id);
    document.querySelectorAll('[id^="menu-"]').forEach(el => {
        if (el !== menu) el.classList.add('hidden');
    });
    menu.classList.toggle('hidden');
}

// Fungsi Modal Hapus
function openDeleteModal(id) {
    currentDeleteId = id;
    document.getElementById('delete-modal').classList.remove('hidden');
    // Tutup dropdown setelah klik hapus
    document.querySelectorAll('[id^="menu-"]').forEach(el => el.classList.add('hidden'));
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    currentDeleteId = null;
}

document.getElementById('confirm-delete-btn').addEventListener('click', function() {
    if (currentDeleteId) {
        document.getElementById('delete-form-' + currentDeleteId).submit();
    }
});

// Klik luar untuk tutup dropdown
document.addEventListener('click', function (e) {
    if (!e.target.closest('td')) {
        document.querySelectorAll('[id^="menu-"]').forEach(el => el.classList.add('hidden'));
    }
});

</script>


@endsection