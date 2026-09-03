@extends('publishing-original.layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('publishing-original-assets/css/board.css') }}" media="all">
<link rel="stylesheet" href="{{ asset('publishing-original-assets/css/programme.css') }}" media="all">
@endsection

@section('content')
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
@endsection
