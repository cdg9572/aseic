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
        <a href="{{ request('return_url', route('backoffice.main-pages.index')) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> 목록으로
        </a>
    </div>

    <div class="board-card">
        <div class="board-card-body">
            <form action="{{ route('backoffice.main-pages.update', $mainPage) }}" method="POST" enctype="multipart/form-data" id="mainPageForm" class="bo-compact-form bo-main-page-form">
                @csrf
                @method('PUT')
                @if (request('return_url'))
                    <input type="hidden" name="return_url" value="{{ request('return_url') }}">
                @endif
                @include('backoffice.main-pages._form')
                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 저장</button>
                    <a href="{{ request('return_url', route('backoffice.main-pages.index')) }}" class="btn btn-secondary">취소</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/backoffice/main-pages.js') }}"></script>
@endsection
