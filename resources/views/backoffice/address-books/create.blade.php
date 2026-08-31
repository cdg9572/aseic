@extends('backoffice.layouts.app')

@section('title', '주소록 신규 등록')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/about-pages.css') }}">
@endsection

@section('content')
<div class="board-container">
    <div class="board-header">
        <a href="{{ route('backoffice.address-books.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> 목록으로
        </a>
    </div>
    <div class="board-card">
        <div class="board-card-body">
            <form action="{{ route('backoffice.address-books.store') }}" method="POST" enctype="multipart/form-data" class="bo-compact-form">
                @csrf
                @include('backoffice.address-books._form')
                @include('backoffice.address-books._contact_management')
                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 저장</button>
                    <a href="{{ route('backoffice.address-books.index') }}" class="btn btn-secondary">취소</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/backoffice/speakers.js') }}?v={{ filemtime(public_path('js/backoffice/speakers.js')) }}"></script>
<script src="{{ asset('js/backoffice/address-books.js') }}?v={{ filemtime(public_path('js/backoffice/address-books.js')) }}"></script>
@endsection
