@extends('backoffice.layouts.app')

@section('title', 'Main Page 정보 수정')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/main-pages.css') }}">
@endsection

@section('content')
@if ($errors->any())
    <div class="alert alert-danger board-hidden-alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="board-container">
    <div class="board-header">
        <div class="board-buttons">
            <a href="{{ $returnUrl }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> 목록으로
            </a>
            @if ($mainPage->is_visible)
                <a href="{{ route('home', ['mainPage' => $mainPage->folder_name]) }}" target="_blank" rel="noopener" class="btn btn-success btn-sm">
                    <i class="fas fa-external-link-alt"></i> 사용자 페이지
                </a>
            @endif
        </div>
    </div>

    <div class="board-card">
        <div class="board-card-body">
            <form action="{{ route('backoffice.main-pages.update', $mainPage) }}" method="POST" enctype="multipart/form-data" id="mainPageForm" class="bo-compact-form bo-main-page-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="return_url" value="{{ $returnUrl }}">
                @include('backoffice.main-pages._form')
                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 저장</button>
                    <a href="{{ $returnUrl }}" class="btn btn-secondary">취소</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/backoffice/main-pages.js') }}"></script>
@endsection
