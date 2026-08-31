@extends('backoffice.layouts.app')

@section('title', $context['entity_name'] . ' 신규 등록')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}">
@endsection

@section('content')
@if ($errors->any())
    <div class="alert alert-danger board-hidden-alert"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<div class="board-container">
    <div class="board-header">
        <a href="{{ route($context['route'] . '.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a>
    </div>
    <div class="board-card">
        <div class="board-card-body">
            <form action="{{ route($context['route'] . '.store') }}" method="POST" enctype="multipart/form-data" class="bo-compact-form">
                @csrf
                @include('backoffice.homepage-partners._form')
                <div class="board-form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 저장</button>
                    <a href="{{ route($context['route'] . '.index') }}" class="btn btn-secondary">취소</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<x-backoffice-ckeditor-assets />
<script src="{{ asset('js/backoffice/homepage-partners.js') }}"></script>
@endsection
