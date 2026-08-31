@props([
    'paginator' => null,
    'writeUrl' => null,
])

{{-- $writeUrl이 전달되었을 때 inbtn 클래스 추가 --}}
<div @class(['board_bottom', 'inbtn' => $writeUrl])>
    @if($paginator)
        {{-- 라라벨 동적 페이지네이션 --}}
        {{ $paginator->links() }}
    @else
        {{-- 하드코딩 마크업 유지 시 --}}
        <div class="paging">
            <a href="#this" class="arrow two first">맨끝</a>
            <a href="#this" class="arrow one prev">이전</a>
            <a href="#this" class="on">1</a>
            <a href="#this">2</a>
            <a href="#this">3</a>
            <a href="#this">4</a>
            <a href="#this">5</a>
            <a href="#this" class="arrow one next">다음</a>
            <a href="#this" class="arrow two last">맨끝</a>
        </div>
    @endif

    {{-- 글쓰기 URL이 전달된 경우에만 노출 --}}
    @if($writeUrl)
        <a href="{{ $writeUrl }}" class="btn btn_wbb btn_abso">글쓰기</a>
    @endif
</div>