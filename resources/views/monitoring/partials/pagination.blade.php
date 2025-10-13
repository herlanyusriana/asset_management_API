@if ($paginator->hasPages())
    <nav role="navigation" class="flex items-center justify-between" aria-label="Pagination">
        <div class="flex flex-1 justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center rounded-md border border-slate-200 px-4 py-2 text-sm font-medium text-slate-400">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center rounded-md border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="ml-3 inline-flex items-center rounded-md border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Berikutnya
                </a>
            @else
                <span class="ml-3 inline-flex items-center rounded-md border border-slate-200 px-4 py-2 text-sm font-medium text-slate-400">
                    Berikutnya
                </span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <p class="text-sm text-slate-500">
                Menampilkan
                <span class="font-medium text-slate-700">{{ $paginator->firstItem() }}</span>
                -
                <span class="font-medium text-slate-700">{{ $paginator->lastItem() }}</span>
                dari
                <span class="font-medium text-slate-700">{{ $paginator->total() }}</span>
                aset
            </p>

            <div>
                <span class="relative z-0 inline-flex rounded-md shadow-sm">
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-400">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="relative inline-flex items-center border border-primary bg-primary px-4 py-2 text-sm font-semibold text-white focus:z-20">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 focus:z-20">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </span>
            </div>
        </div>
    </nav>
@endif
