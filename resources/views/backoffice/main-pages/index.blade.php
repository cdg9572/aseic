@extends('backoffice.layouts.app')

@section('title', 'Main Page')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/main-pages.css') }}">
@endsection

@section('scripts')
<script src="{{ asset('js/backoffice/main-pages.js') }}"></script>
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success board-hidden-alert">{{ session('success') }}</div>
@endif

<div class="board-container" data-main-page-list>
    <div class="board-page-header">
        <div class="board-page-buttons">
            <button type="button" id="btnDeleteMultiple" class="btn btn-danger" data-url="{{ route('backoffice.main-pages.delete-multiple') }}">
                <i class="fas fa-trash"></i> 선택 삭제
            </button>
            <a href="{{ route('backoffice.main-pages.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> 신규등록
            </a>
        </div>
    </div>

    <div class="board-card">
        <div class="board-card-body">
            <div class="board-filter">
                <form method="GET" action="{{ route('backoffice.main-pages.index') }}" class="filter-form" id="searchForm">
                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="is_visible" class="filter-label">노출 여부</label>
                            <select id="is_visible" name="is_visible" class="filter-select">
                                <option value="" @selected(($filters['is_visible'] ?? '') === '')>전체</option>
                                <option value="1" @selected(($filters['is_visible'] ?? '') === '1')>Y</option>
                                <option value="0" @selected(($filters['is_visible'] ?? '') === '0')>N</option>
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
                            <input type="text" id="keyword" name="keyword" class="filter-input" placeholder="폴더명 또는 행사명을 입력하세요" value="{{ $filters['keyword'] ?? '' }}">
                        </div>
                        <div class="filter-group">
                            <div class="filter-buttons">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 검색</button>
                                <a href="{{ route('backoffice.main-pages.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> 초기화</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="board-list-header">
                <div class="list-info"><span class="list-count">Total : {{ $mainPages->total() }}</span></div>
                <div class="list-controls">
                    <form method="GET" action="{{ route('backoffice.main-pages.index') }}" class="per-page-form">
                        <input type="hidden" name="is_visible" value="{{ $filters['is_visible'] ?? '' }}">
                        <input type="hidden" name="created_from" value="{{ $filters['created_from'] ?? '' }}">
                        <input type="hidden" name="created_to" value="{{ $filters['created_to'] ?? '' }}">
                        <input type="hidden" name="keyword" value="{{ $filters['keyword'] ?? '' }}">
                        <label for="per_page" class="per-page-label">표시 개수:</label>
                        <select id="per_page" name="per_page" class="per-page-select" data-auto-submit>
                            <option value="10" @selected($perPage === 10)>10개</option>
                            <option value="20" @selected($perPage === 20)>20개</option>
                            <option value="50" @selected($perPage === 50)>50개</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="board-table">
                    <thead>
                        <tr>
                            <th class="w5 board-checkbox-column"><input type="checkbox" id="select-all" class="form-check-input"></th>
                            <th class="w6">번호</th>
                            <th>제목(폴더명)</th>
                            <th>행사명</th>
                            <th class="w18">행사 기간</th>
                            <th class="w8">노출여부</th>
                            <th class="w10">작성자</th>
                            <th class="w10">등록일</th>
                            <th class="w12">관리</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mainPages as $index => $mainPage)
                            <tr>
                                <td><input type="checkbox" value="{{ $mainPage->id }}" class="form-check-input bo-row-checkbox"></td>
                                <td>{{ $mainPages->total() - ($mainPages->currentPage() - 1) * $mainPages->perPage() - $index }}</td>
                                <td>{{ $mainPage->folder_name }}</td>
                                <td>{{ $mainPage->event_name }}</td>
                                <td>
                                    @if ($mainPage->use_custom_event_date)
                                        {{ $mainPage->event_date_text ?: '-' }}
                                    @elseif ($mainPage->event_start_date && $mainPage->event_end_date)
                                        {{ $mainPage->event_start_date->format('Y-m-d') }} ~ {{ $mainPage->event_end_date->format('Y-m-d') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $mainPage->is_visible ? 'Y' : 'N' }}</td>
                                <td>{{ $mainPage->creator?->name ?? '-' }}</td>
                                <td>{{ $mainPage->created_at->format('Y.m.d') }}</td>
                                <td>
                                    <div class="board-btn-group">
                                        <a href="{{ route('backoffice.main-pages.edit', ['mainPage' => $mainPage, 'return_url' => request()->fullUrl()]) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> 수정
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm btn-delete-main-page" data-url="{{ route('backoffice.main-pages.destroy', $mainPage) }}">
                                            <i class="fas fa-trash"></i> 삭제
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center">등록된 Main Page가 없습니다.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$mainPages" />
        </div>
    </div>
</div>
@endsection
