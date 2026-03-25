@props(['active' => null, 'role' => null]) {{-- Tambahkan default null di sini --}}

<div>
    {{-- Cek dulu apakah $active ada isinya, baru tampilkan --}}
    @if($active !== null) 
        @if($active)
            <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 uppercase">
                Aktif
            </span>
        @else
            <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-red-100 text-red-700 uppercase">
                Non-Aktif
            </span>
        @endif
    @endif

    {{-- Cek dulu apakah $role ada isinya --}}
    @if($role !== null)
        <span class="px-2 py-1 text-[10px] font-bold rounded-full {{ $role == 'admin' ? 'bg-slate-100 text-slate-700' : 'bg-blue-100 text-blue-700' }} uppercase">
            {{ $role }}
        </span>
    @endif
</div>