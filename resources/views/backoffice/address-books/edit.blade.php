@extends('backoffice.layouts.app')

@section('title', '주소록 정보 수정')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
<link rel="stylesheet" href="{{ asset('css/backoffice/about-pages.css') }}">
@endsection

@section('content')
<div class="board-container">
    <div class="board-header">
        <a href="{{ $returnUrl }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> 목록으로
        </a>
    </div>
    <div class="board-card">
        <div class="board-card-body">
            <form action="{{ route('backoffice.address-books.update', $addressBook) }}" method="POST" enctype="multipart/form-data" id="address-book-form" class="bo-compact-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="return_url" value="{{ $returnUrl }}">
                @include('backoffice.address-books._form')
            </form>

            @include('backoffice.address-books._contact_management')

            <div class="board-form-actions">
                <button type="submit" class="btn btn-primary" form="address-book-form" data-skip-button><i class="fas fa-save"></i> 저장</button>
                <a href="{{ $returnUrl }}" class="btn btn-secondary">취소</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/backoffice/speakers.js') }}?v={{ filemtime(public_path('js/backoffice/speakers.js')) }}"></script>
<script src="{{ asset('js/backoffice/address-books.js') }}?v={{ filemtime(public_path('js/backoffice/address-books.js')) }}"></script>
@endsection
