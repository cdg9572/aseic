<div class="board_bottom">
	<nav class="paging" aria-label="게시판 페이지 이동">
		<a href="{{ $paginator->url(1) }}" class="arrow two first" aria-label="첫 페이지로 이동">처음</a>
		<a href="{{ $paginator->previousPageUrl() ?: $paginator->url(1) }}" class="arrow one prev" aria-label="이전 페이지로 이동">이전</a>

		@foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
		<a href="{{ $url }}" @class(['on' => $page === $paginator->currentPage()]) @if($page === $paginator->currentPage()) aria-current="page" @endif aria-label="{{ $page }}페이지로 이동">{{ $page }}</a>
		@endforeach

		<a href="{{ $paginator->nextPageUrl() ?: $paginator->url($paginator->lastPage()) }}" class="arrow one next" aria-label="다음 페이지로 이동">다음</a>
		<a href="{{ $paginator->url($paginator->lastPage()) }}" class="arrow two last" aria-label="마지막 페이지로 이동">맨끝</a>
	</nav>
</div>
