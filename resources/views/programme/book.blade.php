@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/board.css') !!}
{!! \App\Helpers\CssHelper::minTag('/css/programme.css') !!}
@endsection

@section('content')
@if($programmePage ?? null)
<div class="inner">
	<section class="theme_info_area" aria-labelledby="programme-book-heading">
		<div class="board_top">
			<h2 id="programme-book-heading" class="board_tit">Programme Book</h2>
			<form action="{{ route('programme.book') }}" method="get" class="search_wrap">
				<fieldset>
					<legend class="sound_only">Search programme books</legend>

					<label for="search-condition" class="sound_only">Select search criteria</label>
					<select name="search_condition" id="search-condition" class="text">
						<option value="title" @selected($searchCondition === 'title')>Title</option>
						<option value="file_name" @selected($searchCondition === 'file_name')>File name</option>
					</select>

					<div class="search_area">
						<label for="search-keyword" class="sound_only">Enter search term</label>
						<input type="text" id="search-keyword" name="search_keyword" class="text" value="{{ $searchKeyword }}" placeholder="please enter a search term.">
						<button type="submit" class="btn">Search</button>
					</div>
				</fieldset>
			</form>
		</div>

		<ul class="download_list">
			@forelse($books as $book)
			@php
				$fileUrl = $book->file_path
					? (str_starts_with($book->file_path, '/') ? asset(ltrim($book->file_path, '/')) : asset('storage/'.$book->file_path))
					: null;
				$actionUrl = $fileUrl ?: $book->link;
				$bookTitle = $book->title ?: $book->file_name ?: 'Programme Book';
			@endphp
			<li>
				<p>{{ $bookTitle }}</p>
				@if($actionUrl)
				<a href="{{ $actionUrl }}" class="btn_download" @if($fileUrl) download="{{ $book->file_name ?: basename($book->file_path) }}" @else target="_blank" rel="noopener noreferrer" @endif>{{ $fileUrl ? 'Download' : 'View' }}</a>
				@endif
			</li>
			@empty
			<li><p>No programme books found.</p></li>
			@endforelse
		</ul>

		@include('components.frontend-pagination', ['paginator' => $books])
	</section>
</div>
@elseif(($mainPage?->folder_name ?? null) === 'publishing-original')
<div class="inner">

	<section class="theme_info_area" aria-labelledby="programme-book-heading">
		<div class="board_top">
			<h2 id="programme-book-heading" class="board_tit">Programme Book</h2>
			<form action="" method="get" class="search_wrap">
				<fieldset>
					<legend class="sound_only">Search posts</legend>

					<label for="search-condition" class="sound_only">Select search criteria</label>
					<select name="search_condition" id="search-condition" class="text">
						<option value="title">Title</option>
						<option value="content">content</option>
					</select>

					<div class="search_area">
						<label for="search-keyword" class="sound_only">Enter search term</label>
						<input type="text" id="search-keyword" name="search_keyword" class="text" placeholder="please enter a search term.">
						<button type="submit" class="btn">Search</button>
					</div>
				</fieldset>
			</form>
		</div>
		<ul class="download_list">
			<li><p>Global Eco-Innovation Forum 2026_programme Book</p> <a href="#this" class="btn_download">Download</a></li>
			<li><p>Global Eco-Innovation Forum 2026_programme Book</p> <a href="#this" class="btn_download">Download</a></li>
			<li><p>Global Eco-Innovation Forum 2026_programme Book</p> <a href="#this" class="btn_download">Download</a></li>
			<li><p>Global Eco-Innovation Forum 2026_programme Book</p> <a href="#this" class="btn_download">Download</a></li>
			<li><p>Global Eco-Innovation Forum 2026_programme Book</p> <a href="#this" class="btn_download">Download</a></li>
			<li><p>Global Eco-Innovation Forum 2026_programme Book</p> <a href="#this" class="btn_download">Download</a></li>
			<li><p>Global Eco-Innovation Forum 2026_programme Book</p> <a href="#this" class="btn_download">Download</a></li>
		</ul>

		<div class="board_bottom">
			<nav class="paging" aria-label="게시판 페이지 이동">
				<a href="#this" class="arrow two first" aria-label="첫 페이지로 이동">처음</a>
				<a href="#this" class="arrow one prev" aria-label="이전 페이지로 이동">이전</a>

				<a href="#this" class="on" aria-current="page" aria-label="현재 1페이지">1</a>
				<a href="#this" aria-label="2페이지로 이동">2</a>
				<a href="#this" aria-label="3페이지로 이동">3</a>
				<a href="#this" aria-label="4페이지로 이동">4</a>
				<a href="#this" aria-label="5페이지로 이동">5</a>

				<a href="#this" class="arrow one next" aria-label="다음 페이지로 이동">다음</a>
				<a href="#this" class="arrow two last" aria-label="마지막 페이지로 이동">맨끝</a>
			</nav>
		</div>
	</section>

</div>
@endif
@endsection
