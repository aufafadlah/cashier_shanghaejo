{{-- TABEL  --}}
<div class="relative overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
            {{ $header }}
        </thead>
        <tbody class="divide-y divide-gray-200">
            {{ $slot }}
        </tbody>
    </table>
</div>