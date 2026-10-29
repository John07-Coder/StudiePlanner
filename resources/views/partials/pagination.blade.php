@if($paginator->hasPages())
<nav class="pagination" aria-label="Pagina's met taken">
    @if($paginator->onFirstPage())<span class="button secondary disabled">← Vorige</span>@else<a class="button secondary" href="{{ $paginator->previousPageUrl() }}" rel="prev">← Vorige</a>@endif
    <span>Pagina {{ $paginator->currentPage() }} van {{ $paginator->lastPage() }}</span>
    @if($paginator->hasMorePages())<a class="button secondary" href="{{ $paginator->nextPageUrl() }}" rel="next">Volgende →</a>@else<span class="button secondary disabled">Volgende →</span>@endif
</nav>
@endif

