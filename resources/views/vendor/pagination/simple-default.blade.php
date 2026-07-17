@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Paginacja">
        @if ($paginator->onFirstPage())
            <span class="pagination__link is-disabled" aria-disabled="true">&laquo; Poprzednia</span>
        @else
            <a class="pagination__link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Poprzednia</a>
        @endif

        @if ($paginator->hasMorePages())
            <a class="pagination__link" href="{{ $paginator->nextPageUrl() }}" rel="next">Następna &raquo;</a>
        @else
            <span class="pagination__link is-disabled" aria-disabled="true">Następna &raquo;</span>
        @endif
    </nav>
@endif
