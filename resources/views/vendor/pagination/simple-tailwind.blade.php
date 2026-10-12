@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex gap-2 items-center justify-between text-xs">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed" aria-disabled="true">
                <i class="fa-solid fa-chevron-left text-[10px]"></i> Sebelumnya
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-teal-50 hover:text-teal-700 hover:border-teal-200 transition shadow-xs">
                <i class="fa-solid fa-chevron-left text-[10px]"></i> Sebelumnya
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-teal-50 hover:text-teal-700 hover:border-teal-200 transition shadow-xs">
                Berikutnya <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed" aria-disabled="true">
                Berikutnya <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </span>
        @endif
    </nav>
@endif

