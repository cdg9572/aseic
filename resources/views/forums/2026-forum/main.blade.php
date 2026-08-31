@extends('forums.'.($mainPage->folder_name ?? 'default').'.layouts.app')

@section('title', $mainPage->event_name ?? 'Main Page')

@section('content')
<main>
    <h1>{{ $mainPage->event_name ?? 'Main Page' }}</h1>
</main>
@endsection
