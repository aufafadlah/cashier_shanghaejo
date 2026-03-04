@extends('layouts.app') {{-- Memanggil Sidebar & Navbar --}}

@section('content')
<div class="container-fluid p-4">
    <div class="flex justify-between items-center mb-6">
  <div>
    <h1 class="text-2xl font-bold text-gray-800">Kelola Cabang</h1>
    
    <p class="text-xs text-gray-400 mt-1">Kelola Cabang Bisnis Anda</p>
  </div>

  <button class="inline-flex items-center px-4 py-2 bg-white text-black text-sm font-medium rounded-lg">
    <span class="mr-2 text-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" 
    fill="none" stroke="#CE484A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle>
    <line x1="19" x2="19" y1="8" y2="14"></line><line x1="22" x2="16" y1="11" y2="11">
    </line></svg></span> <a href="{{ route('cabang.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i>Add Cabang</a>
  </button>
</div>
    {{-- BAGIAN HEADER (Sesuai Figma) --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative">
    <p class="text-sm text-gray-500 font-medium">Total Cabang</p>
    <div class="flex items-center gap-3">
    <h3 class="text-3xl font-bold mt-1 justify-between w-full">{{ $totalCabang }}</h3>
    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" 
    fill="none" stroke="#7AB465" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M10 9h4"></path><path d="M12 7v5"></path><path d="M14 22v-4a2 2 0 0 0-4 0v4"></path><path
     d="M18 22V5.618a1 1 0 0 0-.553-.894l-4.553-2.277a2 2 0 0 0-1.788 0L6.553 4.724A1 1 0 0 0 6 5.618V22">
    </path><path d="m18 7 3.447 1.724a1 1 0 0 1 .553.894V20a2 2 0 0 1-2 
    2H4a2 2 0 0 1-2-2V9.618a1 1 0 0 1 .553-.894L6 7"></path></svg>
    </div>
    </div>


    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative">
    <p class="text-sm text-gray-500 font-medium">Aktif</p>
    <div class="flex items-center gap-3">
    <h3 class="text-3xl font-bold mt-1 w-full">{{ $aktif }}</h3>
    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none" 
    stroke="#7AB465" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 3H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-3">
    </path><path d="M8 21h8"></path><path d="M12 17v4"></path><path d="m17 8 5-5"></path><path d="M17 3h5v5"></path></svg>
    <span class="absolute bottom-4 right-4 text-green-500">
    </span>
    </div>
    </div>

  <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative">
    <p class="text-sm text-gray-500 font-medium text-blue-400">Non Aktif</p>
    <div class= "flex items-center gap-3">
    <h3 class="text-3xl font-bold mt-1 w-full">{{ $nonAktif }}</h3>
    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="#7AB465" 
    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 3H4a2 2 0
    0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-3"></path><path d="M8 21h8"></path><path d="M12 17v4"></path><path d="m22 3-5 5"></path><path d="m17 3 5 5"></path></svg>
    <span class="absolute bottom-4 right-4 text-green-500">
       </span>
    </div>
  </div>
</div>

    </div>

    
    {{-- TABLE --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative">
    <table class="w-full text-sm text-left text-body ">
        <thead class="bg-neutral-secondary-medium border-b border-t border-default-medium">
            <tr>
                {{-- SELECT ALL --}}
                <th class="p-4 w-4">
                    <input id="checkbox-all"
                        type="checkbox"
                        class="w-4 h-4 cursor-pointer">
                </th>
                <th class="px-6 py-3 font-medium">No</th>
                <th class="px-6 py-3 font-medium">Nama Cabang</th>
                <th class="px-6 py-3 font-medium">Alamat</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium">Tanggal Terdaftar</th>
                <th class="px-6 py-3 font-medium text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($cabangs as $cabang)
            <tr class="bg-neutral-primary-soft border-b border-default hover:bg-neutral-secondary-medium">
                
                {{-- CHECKBOX ROW --}}
                <td class="p-4 w-4">
                    <input type="checkbox"
                        id="checkbox-all"
                        name="selected_cabangs[]"
                        value="{{ $cabang->id_cabang }}"
                        class="row-checkbox w-4 h-4 cursor-pointer">
                </td>

                <td class="px-6 py-4">{{ $loop->iteration }}</td>

                <td class="px-6 py-4 font-medium text-heading">
                    {{ $cabang->nama_cabang }}
                </td>

                <td class="px-6 py-4">
                    {{ $cabang->alamat }}
                </td>

                <td class="px-6 py-4">
                    @if($cabang->is_active)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                            Aktif
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                            Non-Aktif
                        </span>
                    @endif
                </td>

                <td class="px-6 py-4 text-sm text-body">
                    {{ \Carbon\Carbon::parse($cabang->created_at)->translatedFormat('d F Y') }}
                </td>

<td class="px-6 py-4 text-center relative">

    <!-- TOMBOL TITIK 3 -->
    <button
        type="button"
        onclick="toggleMenu({{ $cabang->id_cabang }})"
        class="p-2 rounded hover:bg-gray-200">
        ⋮
    </button>

    <!-- DROPDOWN MENU -->
    <div
        id="menu-{{ $cabang->id_cabang }}"
        class="hidden absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow z-50">

        <!-- DETAIL -->
        <a href="{{ route('cabang.show', $cabang->id_cabang) }}"
           class="block px-4 py-2 text-sm hover:bg-gray-100">
            👁 Detail
        </a>

        <!-- EDIT -->
        <a href="{{ route('cabang.edit', $cabang->id_cabang) }}"
           class="block px-4 py-2 text-sm hover:bg-gray-100">
            ✏️ Edit
        </a>

        <!-- HAPUS -->
        <form action="{{ route('cabang.destroy', $cabang->id_cabang) }}"
              method="POST">
            @csrf
            @method('DELETE')
            <button
                type="submit"
                onclick="return confirm('Yakin hapus cabang ini?')"
                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                🗑 Hapus
            </button>
        </form>
    </div>

</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-6 text-body">
                    Belum ada data cabang.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<script>
function toggleMenu(id) {
    const menu = document.getElementById('menu-' + id);

    // Tutup semua menu lain
    document.querySelectorAll('[id^="menu-"]').forEach(el => {
        if (el !== menu) el.classList.add('hidden');
    });

    // Toggle menu yg diklik
    menu.classList.toggle('hidden');
}

// Klik di luar → tutup semua menu
document.addEventListener('click', function (e) {
    if (!e.target.closest('td')) {
        document.querySelectorAll('[id^="menu-"]').forEach(el => {
            el.classList.add('hidden');
        });
    }
});



</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const checkAll = document.getElementById('checkbox-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    if (!checkAll) return;

    checkAll.addEventListener('change', function () {
        rowCheckboxes.forEach(cb => {
            cb.checked = this.checked;
        });
    });

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (!this.checked) {
                checkAll.checked = false;
            }
        });
    });

});
</script>
@endsection('content')