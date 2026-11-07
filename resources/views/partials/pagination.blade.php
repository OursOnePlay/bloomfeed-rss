@if ($paginator->hasPages())
    <nav class="pagination" aria-label="{{ __('pagination.label') }}">
        @if ($paginator->onFirstPage())
            <span class="pagination-control" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">&lsaquo;</span>
        @else
            <a class="pagination-control" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}">&lsaquo;</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pagination-ellipsis">{{ $element }}</span>
            @else
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination-control" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="pagination-control" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="pagination-control" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}">&rsaquo;</a>
        @else
            <span class="pagination-control" aria-disabled="true" aria-label="{{ __('pagination.next') }}">&rsaquo;</span>
        @endif
    </nav>
@endif
