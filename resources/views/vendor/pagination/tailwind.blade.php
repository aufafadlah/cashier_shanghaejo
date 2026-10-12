@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">

        {{-- Mobile View: Simple Prev / Next --}}
        <div class="flex items-center justify-between w-full sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i> Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-teal-50 hover:text-teal-700 hover:border-teal-200 transition shadow-xs">
                    <i class="fa-solid fa-chevron-left text-[10px]"></i> Sebelumnya
                </a>
            @endif

            <span class="text-xs text-gray-500 font-medium">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-teal-50 hover:text-teal-700 hover:border-teal-200 transition shadow-xs">
                    Berikutnya <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed">
                    Berikutnya <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </span>
            @endif
        </div>

        {{-- Desktop View --}}
        <div class="hidden sm:flex items-center justify-between w-full gap-4">
            {{-- Results Counter --}}
            <div class="text-xs text-gray-500 font-medium">
                Menampilkan
                @if ($paginator->firstItem())
                    <span class="font-bold text-gray-700">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="font-bold text-gray-700">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                dari
                <span class="font-bold text-gray-700">{{ $paginator->total() }}</span>
                data
            </div>

            {{-- Navigation Buttons --}}
            <div class="inline-flex items-center gap-1">
                {{-- Previous Button --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed" aria-disabled="true">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        <span>Sebelumnya</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-teal-50 hover:text-teal-700 hover:border-teal-200 transition shadow-xs" aria-label="Halaman Sebelumnya">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        <span>Sebelumnya</span>
                    </a>
                @endif

                {{-- Numbered Page Links --}}
                @foreach ($elements as $element)
                    {{-- Three Dots --}}
                    @if (is_string($element))
                        <span class="inline-flex items-center px-2 py-1 text-xs text-gray-400 font-bold">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Page Links Array --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 text-xs font-bold text-white bg-teal-600 rounded-lg shadow-sm" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-teal-50 hover:text-teal-700 hover:border-teal-200 transition shadow-xs" aria-label="Ke halaman {{ $page }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Button --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-teal-50 hover:text-teal-700 hover:border-teal-200 transition shadow-xs" aria-label="Halaman Berikutnya">
                        <span>Berikutnya</span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-gray-400 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed" aria-disabled="true">
                        <span>Berikutnya</span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </span>
                @endif
            </div>
        </div>

    </nav>
@endif
