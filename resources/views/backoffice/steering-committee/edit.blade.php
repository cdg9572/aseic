@extends('backoffice.layouts.app')
@section('title', 'Steering Committee 정보 수정')
@section('styles')<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}"><link rel="stylesheet" href="{{ asset('css/backoffice/about-pages.css') }}">@endsection
@section('content')
@if($errors->any())<div class="alert alert-danger board-hidden-alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="board-container"><div class="board-header"><a href="{{ $returnUrl }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a></div><div class="board-card"><div class="board-card-body">
<form action="{{ route('backoffice.steering-committee.update', $aboutPage) }}" method="POST" class="bo-compact-form">@csrf @method('PUT')<input type="hidden" name="return_url" value="{{ $returnUrl }}">@include('backoffice.steering-committee._form')<div class="board-form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 저장</button><a href="{{ $returnUrl }}" class="btn btn-secondary">취소</a></div></form>
</div></div></div>
@endsection
@section('scripts')<x-backoffice-ckeditor-assets /><script src="{{ asset('js/backoffice/about-detail.js') }}"></script>@endsection
