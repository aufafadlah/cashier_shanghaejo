@extends('layouts.app') {{-- Memanggil Sidebar & Navbar --}}

@section('content')
{{-- dec --}}
<div class="container-fluid p-4">
    <div class="flex justify-between items-center mb-6">
  <div>
    <h1 class="text-2xl font-bold text-gray-800">Kelola Cabang</h1>
    
    <p class="text-xs text-gray-400 mt-1">Kelola Cabang Bisnis Anda</p>
  </div>
{{-- add cabang + ui --}}
  <button class="inline-flex items-center px-4 py-2 bg-white text-black text-sm font-medium rounded-lg">
    <span class="mr-2 text-lg"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" 
    fill="none" stroke="#CE484A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle>
    <line x1="19" x2="19" y1="8" y2="14"></line><line x1="22" x2="16" y1="11" y2="11">
    </line></svg></span> <a href="{{ route('cabang.create') }}" class="btn btn-primary">
    <i class="fas fa-plus"></i>Add Cabang</a>
  </button>
</div>
{{-- 1. SIMPAN NOTIFIKASI DI SINI --}}
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
    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none" stroke="#CE484A" 
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
            {{-- SELECT ALL --}}
            <thead class="bg-neutral-secondary-medium border-b border-t border-default-medium">
            <tr>
                {{-- <th class="p-4 w-4">
                    <input id="checkbox-all"
                        type="checkbox"
                        class="w-4 h-4 cursor-pointer">
                </th> --}}
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
                {{-- <td class="p-4 w-4">
                    <input type="checkbox"
                        id="checkbox-all"
                        name="selected_cabangs[]"
                        value="{{ $cabang->id_cabang }}"
                        class="row-checkbox w-4 h-4 cursor-pointer">
                </td> --}}

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
    class="hidden absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow z-50
           divide-y divide-gray-100 overflow-hidden">

    <!-- DETAIL -->
    <a href="{{ route('cabang.show', $cabang->id_cabang) }}"
       class="block px-4 py-2 text-sm hover:bg-gray-100
              transition duration-150">
        👁 Detail
    </a>

    <!-- EDIT -->
    <a href="{{ route('cabang.edit', $cabang->id_cabang) }}"
       class="block px-4 py-2 text-sm hover:bg-gray-100
              transition duration-150">
        ✏️ Edit
    </a>

    <!-- HAPUS -->
    <form action="{{ route('cabang.destroy', $cabang->id_cabang) }}"
          method="POST">
        @csrf
        @method('DELETE')

        <button
            type="button"
            onclick="openDeleteModal('{{ $cabang->id_cabang }}')"
            class="w-full items-center px-4 py-2 text-sm text-red-600
                   hover:bg-red-50 transition duration-150 font-medium">
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
{{-- untuk select all --}}
{{-- <script>
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
</script> --}}
<div id="delete-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
    
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Hapus Cabang?</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                <button type="button" id="confirm-delete-btn" class="inline-flex w-full justify-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto">Hapus Sekarang</button>
                <button type="button" onclick="closeDeleteModal()" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentDeleteId = null;

function openDeleteModal(id) {
    currentDeleteId = id;
    const modal = document.getElementById('delete-modal');
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    const modal = document.getElementById('delete-modal');
    modal.classList.add('hidden');
    currentDeleteId = null;
}

// Eksekusi hapus saat tombol di modal diklik
document.getElementById('confirm-delete-btn').addEventListener('click', function() {
    if (currentDeleteId) {
        // Cari form berdasarkan ID dan submit
        const form = document.querySelector(`form[action*="${currentDeleteId}"]`);
        if (form) form.submit();
    }
});</script>



@endsection('content')