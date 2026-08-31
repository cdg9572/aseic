@extends('backoffice.layouts.app')
@section('title', $context['entity_name'].' 신규 등록')
@section('styles')<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}"><link rel="stylesheet" href="{{ asset('css/backoffice/about-pages.css') }}">@endsection
@section('content')
@php
    $routeParameters = $parent ? [$parent] : [];
@endphp
<div class="board-container"><div class="board-header"><a href="{{ route($context['route'].'.index', $routeParameters) }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a></div><div class="board-card"><div class="board-card-body"><form action="{{ route($context['route'].'.store', $routeParameters) }}" method="POST" enctype="multipart/form-data" class="bo-compact-form">@csrf @include('backoffice.media-contents._form')<div class="board-form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 저장</button><a href="{{ route($context['route'].'.index', $routeParameters) }}" class="btn btn-secondary">취소</a></div></form></div></div></div>
@endsection
@section('scripts')<x-backoffice-ckeditor-assets /><script src="{{ asset('js/backoffice/speakers.js') }}?v={{ filemtime(public_path('js/backoffice/speakers.js')) }}"></script>@endsection
