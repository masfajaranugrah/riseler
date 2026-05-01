@php
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator */
    $paginator = $paginator->appends(request()->query());
    $totalItems = $paginator->total();
    $firstItem = $paginator->firstItem() ?? 0;
    $lastItem = $paginator->lastItem() ?? 0;
    $currentPage = max($paginator->currentPage(), 1);
    $lastPage = max($paginator->lastPage(), 1);

    $window = 2;
    $startPage = max(1, $currentPage - $window);
    $endPage = min($lastPage, $currentPage + $window);

    if (($endPage - $startPage) < 4) {
        $startPage = max(1, $endPage - 4);
        $endPage = min($lastPage, $startPage + 4);
    }

    $prevPage = max(1, $currentPage - 1);
    $nextPage = min($lastPage, $currentPage + 1);
@endphp

<div class="pagination-wrapper">
    <div class="pagination-info">
        Menampilkan <strong>{{ $firstItem }}</strong> - <strong>{{ $lastItem }}</strong> dari <strong>{{ $totalItems }}</strong> data
    </div>

    <nav aria-label="Pagination">
        <ul class="pagination mb-0">
            <li class="page-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
                @if($currentPage <= 1)
                    <span class="page-link" aria-hidden="true">
                        <i class="ri-arrow-left-s-line"></i>
                    </span>
                @else
                    <a class="page-link" href="{{ $paginator->url($prevPage) }}" rel="prev" aria-label="Previous">
                        <i class="ri-arrow-left-s-line"></i>
                    </a>
                @endif
            </li>

            @if($startPage > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                </li>
                @if($startPage > 2)
                    <li class="page-item disabled">
                        <span class="page-link">…</span>
                    </li>
                @endif
            @endif

            @for($page = $startPage; $page <= $endPage; $page++)
                <li class="page-item {{ $page === $currentPage ? 'active' : '' }}">
                    @if($page === $currentPage)
                        <span class="page-link">{{ $page }}</span>
                    @else
                        <a class="page-link" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                    @endif
                </li>
            @endfor

            @if($endPage < $lastPage)
                @if($endPage < ($lastPage - 1))
                    <li class="page-item disabled">
                        <span class="page-link">…</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
                </li>
            @endif

            <li class="page-item {{ $currentPage >= $lastPage ? 'disabled' : '' }}">
                @if($currentPage >= $lastPage)
                    <span class="page-link" aria-hidden="true">
                        <i class="ri-arrow-right-s-line"></i>
                    </span>
                @else
                    <a class="page-link" href="{{ $paginator->url($nextPage) }}" rel="next" aria-label="Next">
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                @endif
            </li>
        </ul>
    </nav>
</div>
