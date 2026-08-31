@extends('backoffice.layouts.app')

@section('title', $context['menu_name'])

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('js/backoffice/about-pages.js') }}"></script>
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success board-hidden-alert">{{ session('success') }}</div>
@endif

<div class="board-container" data-about-page-list data-entity-name="{{ $context['entity_name'] }}">
    <div class="board-page-header">
        <div class="board-page-buttons">
            <button type="button" id="btnDeleteMultiple" class="btn btn-danger" data-url="{{ route($context['route'].'.delete-multiple') }}">
                <i class="fas fa-trash"></i> 선택 삭제
            </button>
            <a href="{{ route($context['route'].'.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> 신규등록</a>
        </div>
    </div>

    <div class="board-card">
        <div class="board-card-body">
            <div class="board-filter">
                <form method="GET" action="{{ route($context['route'].'.index') }}" class="filter-form" id="searchForm">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="is_linked" class="filter-label">Main Page 연결</label>
                            <select id="is_linked" name="is_linked" class="filter-select">
                                <option value="" @selected(($filters['is_linked'] ?? '') === '')>전체</option>
                                <option value="1" @selected(($filters['is_linked'] ?? '') === '1')>Y</option>
                                <option value="0" @selected(($filters['is_linked'] ?? '') === '0')>N</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="created_from" class="filter-label">등록일 시작</label>
                            <input type="date" id="created_from" name="created_from" class="filter-input" value="{{ $filters['created_from'] ?? '' }}">
                        </div>
                        <div class="filter-group">
                            <label for="created_to" class="filter-label">등록일 끝</label>
                            <input type="date" id="created_to" name="created_to" class="filter-input" value="{{ $filters['created_to'] ?? '' }}">
                        </div>
                        <div class="filter-group">
                            <label for="keyword" class="filter-label">검색어</label>
                            <input type="text" id="keyword" name="keyword" class="filter-input" placeholder="제목, 폴더명 또는 행사명을 입력하세요" value="{{ $filters['keyword'] ?? '' }}">
                        </div>
                        <div class="filter-group">
                            <div class="filter-buttons">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 검색</button>
                                <a href="{{ route($context['route'].'.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> 초기화</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="board-list-header">
                <div class="list-info"><span class="list-count">Total : {{ $pages->total() }}</span></div>
                <div class="list-controls">
                    <form method="GET" action="{{ route($context['route'].'.index') }}" class="per-page-form">
                        <input type="hidden" name="is_linked" value="{{ $filters['is_linked'] ?? '' }}">
                        <input type="hidden" name="created_from" value="{{ $filters['created_from'] ?? '' }}">
                        <input type="hidden" name="created_to" value="{{ $filters['created_to'] ?? '' }}">
                        <input type="hidden" name="keyword" value="{{ $filters['keyword'] ?? '' }}">
                        <label for="perPageSelect" class="per-page-label">표시 개수:</label>
                        <select name="per_page" id="perPageSelect" class="per-page-select" data-auto-submit>
                            @foreach ([10, 20, 50] as $size)
                                <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}개</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="board-table">
                    <thead><tr><th class="w5 board-checkbox-column"><input type="checkbox" id="select-all" class="form-check-input"></th><th class="w5">번호</th><th class="w15">제목(폴더명)</th><th class="w30">제목</th><th class="w10">Main Page 연결</th><th class="w10">작성자</th><th class="w10">등록일</th><th class="w15">관리</th></tr></thead>
                    <tbody>
                        @forelse ($pages as $index => $page)
                            @php($linkedMainPage = $page->mainPageLink?->mainPage)
                            <tr>
                                <td><input type="checkbox" value="{{ $page->id }}" class="form-check-input bo-row-checkbox"></td>
                                <td>{{ $pages->total() - ($pages->currentPage() - 1) * $pages->perPage() - $index }}</td>
                                <td>{{ $linkedMainPage ? $linkedMainPage->folder_name.' '.$context['entity_name'] : '' }}</td>
                                <td>{{ $page->page_title ?: '-' }}</td>
                                <td>{{ $linkedMainPage ? 'Y' : 'N' }}</td>
                                <td>{{ $page->creator?->name ?? '-' }}</td>
                                <td>{{ $page->created_at->format('Y.m.d') }}</td>
                                <td><div class="board-btn-group">
                                    <a href="{{ route($context['route'].'.edit', ['aboutPage' => $page, 'return_url' => request()->fullUrl()]) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> 수정</a>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete-about-page" data-url="{{ route($context['route'].'.destroy', $page) }}"><i class="fas fa-trash"></i> 삭제</button>
                                </div></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">등록된 {{ $context['entity_name'] }} 항목이 없습니다.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination :paginator="$pages" />
        </div>
    </div>
</div>
@endsection
