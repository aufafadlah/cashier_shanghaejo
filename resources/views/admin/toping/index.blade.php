@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Topping Pisang Ijo</h1>
            <p class="text-xs text-gray-400 mt-1">Kelola Topping</p>
        </div>
        <a href="{{ route('toping.create') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-xl shadow-sm border border-gray-200 hover:bg-gray-50">
            <span class="mr-2 text-red-500 text-lg">+</span> Add Topping
        </a>
    </div>

    {{-- STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-sm text-gray-500 mb-2">Total Topping</p>
            <h3 class="text-3xl font-bold">{{ $totalTopping }}</h3>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative">
            <p class="text-sm text-gray-500 mb-2">Topping Aktif</p>
            <div class="flex justify-between items-center">
                <h3 class="text-3xl font-bold">{{ $aktif }}</h3>
                <div class="p-2 bg-green-50 rounded-lg text-green-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative">
            <p class="text-sm text-gray-500 mb-2">Topping Non Aktif</p>
            <div class="flex justify-between items-center">
                <h3 class="text-3xl font-bold">{{ $nonAktif }}</h3>
                <div class="p-2 bg-red-50 rounded-lg text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0112 3c4.143 0 7.72 2.515 9.247 6.138a7.002 7.002 0 01-.405 10.342" /></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-6">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr class="text-gray-600 text-sm">
                    <th class="px-4 py-3 font-semibold">No</th>
                    <th class="px-4 py-3 font-semibold">Nama</th>
                    <th class="px-4 py-3 font-semibold">Harga</th>
                    <th class="px-4 py-3 font-semibold">Status</th>
                    <th class="px-4 py-3 font-semibold text-center">Act</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($toppings as $index => $top)
                <tr class="text-sm text-gray-700">
                    <td class="px-4 py-4">{{ $index + 1 }}</td>
                    <td class="px-4 py-4 flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-red-400"></div> {{ $top->nama_toping }}
                    </td>
                    <td class="px-4 py-4 font-medium">Rp. {{ number_format($top->harga_toping, 0, ',', '.') }}</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-2">
                            <span>{{ $top->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                            <div class="w-3 h-3 rounded-full {{ $top->is_active ? 'bg-green-500' : 'bg-red-500' }}"></div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-center flex justify-center gap-2">
                        <a href="{{ route('toping.edit', $top->id_toping) }}" class="bg-green-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-green-600">Update</a>
                        
                        {{-- Tombol Delete Sekarang Memanggil Fungsi openDeleteModal --}}
                        <button type="button" onclick="openDeleteModal('{{ $top->id_toping }}')" class="bg-red-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-red-600">Delete</button>
                        
                        <form id="delete-form-{{ $top->id_toping }}" action="{{ route('toping.destroy', $top->id_toping) }}" method="POST" class="hidden">
                            @csrf @method('DELETE')
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL HAPUS --}}
<div id="delete-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-lg">
            <div class="bg-white p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-bold text-gray-900">Apakah anda yakin ingin menghapus Topping ini?</h3>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3">
                <button type="button" id="confirm-delete-btn" class="inline-flex w-full justify-center rounded-xl bg-red-600 px-6 py-2 text-sm font-semibold text-white hover:bg-red-500 sm:w-auto">Hapus</button>
                <button type="button" onclick="closeDeleteModal()" class="mt-3 inline-flex w-full justify-center rounded-xl bg-green-500 px-6 py-2 text-sm font-semibold text-white hover:bg-green-600 sm:mt-0 sm:w-auto">Batal</button>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT UNTUK MODAL --}}
<script>
    let currentDeleteId = null;

    function openDeleteModal(id) {
        currentDeleteId = id;
        document.getElementById('delete-modal').classList.remove('hidden');
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
</script>

@endsection