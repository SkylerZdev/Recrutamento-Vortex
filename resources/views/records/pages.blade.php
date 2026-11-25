<nav class="pages" aria-label="Paginação">
@if($page->previousPageUrl())<a href="{{ $page->previousPageUrl() }}">Anterior</a>@endif
<span>Página {{ $page->currentPage() }} de {{ $page->lastPage() }}</span>
@if($page->nextPageUrl())<a href="{{ $page->nextPageUrl() }}">Próxima</a>@endif</nav>
