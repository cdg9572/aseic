@extends('backoffice.layouts.app')
@section('title', 'Co-Organizers 신규 등록')
@section('styles')<link rel="stylesheet" href="{{ asset('css/backoffice/backoffice-crud.css') }}"><link rel="stylesheet" href="{{ asset('css/backoffice/about-pages.css') }}">@endsection
@section('content')
@if($errors->any())<div class="alert alert-danger board-hidden-alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="board-container"><div class="board-header"><a href="{{ route('backoffice.co-organizers.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> 목록으로</a></div><div class="board-card"><div class="board-card-body"><form action="{{ route('backoffice.co-organizers.store') }}" method="POST" enctype="multipart/form-data" class="bo-compact-form">@csrf @include('backoffice.co-organizers._form')<div class="board-form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 저장</button><a href="{{ route('backoffice.co-organizers.index') }}" class="btn btn-secondary">취소</a></div></form></div></div></div>
@endsection
@section('scripts')<x-backoffice-ckeditor-assets /><script src="{{ asset('js/backoffice/about-detail.js') }}?v={{ filemtime(public_path('js/backoffice/about-detail.js')) }}"></script>@endsection
