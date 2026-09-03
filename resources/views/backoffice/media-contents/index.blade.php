@extends('backoffice.layouts.app')

@section('title', $parent ? $parent->page_title.' '.$context['menu_name'] : $context['menu_name'])

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/boards.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@if(isset($context['category_group_code']))
<link rel="stylesheet" href="{{ asset('css/backoffice/categories.css') }}">
@endif
@endsection

@section('scripts')
<script src="{{ asset('js/backoffice/about-pages.js') }}?v={{ filemtime(public_path('js/backoffice/about-pages.js')) }}"></script>
@endsection

@section('content')
@if (session('success'))<div class="alert alert-success board-hidden-alert">{{ session('success') }}</div>@endif
@php
    $routeParameters = $parent ? [$parent] : [];
    $createParameters = $routeParameters;
    if (isset($context['category_group_code']) && !empty($filters['category_id'])) {
        $createParameters['category_id'] = $filters['category_id'];
    }
@endphp

<div class="board-container" data-about-page-list data-entity-name="{{ $context['entity_name'] }}">
    <div class="board-page-header">
        @if ($parent)<a href="{{ route(str_replace('-items', '', $context['route']).'.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> 폴더 목록</a>@endif
        <div class="board-page-buttons">
            <button type="button" id="btnDeleteMultiple" class="btn btn-danger" data-url="{{ route($context['route'].'.delete-multiple', $routeParameters) }}"><i class="fas fa-trash"></i> 선택 삭제</button>
            <a href="{{ route($context['route'].'.create', $createParameters) }}" class="btn btn-success"><i class="fas fa-plus"></i> 신규등록</a>
        </div>
    </div>

    <div class="board-card"><div class="board-card-body">
        @if(isset($context['category_group_code']))
            @php
                $tabQuery = request()->except(['category_id', 'page']);
            @endphp
            <div class="category-group-tabs">
                <div class="category-group-tabs-inner">
                    <a href="{{ route($context['route'].'.index', $tabQuery) }}" class="group-tab {{ empty($filters['category_id']) ? 'active' : '' }}">전체</a>
                    @foreach($categories as $category)
                        <a href="{{ route($context['route'].'.index', array_merge($tabQuery, ['category_id' => $category->id])) }}" class="group-tab {{ (string) $filters['category_id'] === (string) $category->id ? 'active' : '' }}">{{ $category->name }}</a>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="board-filter"><form method="GET" action="{{ route($context['route'].'.index', $routeParameters) }}" class="filter-form" id="searchForm">
            <input type="hidden" name="per_page" value="{{ $perPage }}">
            @if(isset($context['category_group_code']))
                <input type="hidden" name="category_id" value="{{ $filters['category_id'] }}">
            @endif
            <div class="filter-row">
                <div class="filter-group"><label for="is_visible" class="filter-label">노출 여부</label><select id="is_visible" name="is_visible" class="filter-select"><option value="" @selected(($filters['is_visible'] ?? '') === '')>전체</option><option value="1" @selected(($filters['is_visible'] ?? '') === '1')>보임</option><option value="0" @selected(($filters['is_visible'] ?? '') === '0')>숨김</option></select></div>
                <div class="filter-group"><label for="created_from" class="filter-label">등록일 시작</label><input type="date" id="created_from" name="created_from" class="filter-input" value="{{ $filters['created_from'] ?? '' }}"></div>
                <div class="filter-group"><label for="created_to" class="filter-label">등록일 끝</label><input type="date" id="created_to" name="created_to" class="filter-input" value="{{ $filters['created_to'] ?? '' }}"></div>
                <div class="filter-group"><label for="keyword" class="filter-label">검색어</label><input type="text" id="keyword" name="keyword" class="filter-input" placeholder="{{ isset($context['category_group_code']) ? '제목을 입력하세요' : '제목 또는 폴더명을 입력하세요' }}" value="{{ $filters['keyword'] ?? '' }}"></div>
                <div class="filter-group"><div class="filter-buttons"><button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 검색</button><a href="{{ route($context['route'].'.index', array_merge($routeParameters, $createParameters)) }}" class="btn btn-secondary"><i class="fas fa-undo"></i> 초기화</a></div></div>
            </div>
        </form></div>

        <div class="board-list-header"><div class="list-info"><span class="list-count">Total : {{ $contents->total() }}</span></div><div class="list-controls"><form method="GET" action="{{ route($context['route'].'.index', $routeParameters) }}" class="per-page-form">
            @foreach (['is_visible', 'created_from', 'created_to', 'keyword', 'category_id'] as $filterName)
                @if($filterName !== 'category_id' || isset($context['category_group_code']))
                    <input type="hidden" name="{{ $filterName }}" value="{{ $filters[$filterName] ?? '' }}">
                @endif
            @endforeach
            <label for="perPageSelect" class="per-page-label">표시 개수:</label><select name="per_page" id="perPageSelect" class="per-page-select" data-auto-submit>@foreach ([10, 20, 50] as $size)<option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}개</option>@endforeach</select>
        </form></div></div>

        <div class="table-responsive"><table class="board-table">
            <thead><tr><th class="w5 board-checkbox-column"><input type="checkbox" id="select-all" class="form-check-input"></th><th class="w5">번호</th><th class="w20">{{ isset($context['category_group_code']) ? ($context['category_column_label'] ?? '분류') : '제목(폴더명)' }}</th><th class="w25">제목</th><th class="w10">노출여부</th><th class="w10">작성자</th><th class="w10">등록일</th><th class="w15">관리</th></tr></thead>
            <tbody>
            @forelse ($contents as $index => $content)
                @php
                    $editParameters = $parent ? [$parent, $content, 'return_url' => request()->fullUrl()] : [$content, 'return_url' => request()->fullUrl()];
                    $destroyParameters = $parent ? [$parent, $content] : [$content];
                @endphp
                <tr>
                    <td><input type="checkbox" value="{{ $content->id }}" class="form-check-input bo-row-checkbox"></td>
                    <td>{{ $contents->total() - ($contents->currentPage() - 1) * $contents->perPage() - $index }}</td>
                    <td>
                        @if(isset($context['category_group_code']))
                            {{ $content->category?->name ?? '' }}
                        @elseif(isset($context['child_route']))
                            <a href="{{ route($context['child_route'].'.index', $content) }}">{{ $content->page_title }}</a>
                        @else
                            {{ $parent?->page_title ?? $content->page_title }}
                        @endif
                    </td>
                    <td>{{ $content->title ?: $content->page_title }}</td>
                    <td>{{ $content->is_visible ? '보임' : '숨김' }}</td>
                    <td>{{ $content->creator?->name ?? '-' }}</td>
                    <td>{{ $content->created_at->format('Y.m.d') }}</td>
                    <td><div class="board-btn-group"><a href="{{ route($context['route'].'.edit', $editParameters) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> 수정</a><button type="button" class="btn btn-danger btn-sm btn-delete-about-page" data-url="{{ route($context['route'].'.destroy', $destroyParameters) }}"><i class="fas fa-trash"></i> 삭제</button></div></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">등록된 {{ $context['entity_name'] }} 항목이 없습니다.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <x-pagination :paginator="$contents" />
    </div></div>
</div>
@endsection
