@extends('publishing-original.layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('publishing-original-assets/css/archive.css') }}" media="all">
<link rel="stylesheet" href="{{ asset('publishing-original-assets/css/board.css') }}" media="all">
@endsection

@section('content')
<div class="inner">
	<section class="program_list">
		<div class="years_select_tab flex">
			<button type="button" class="arrow prev" aria-label="이전 연도">이전</button>
			<ul class="tabs mb0" role="tablist" aria-label="Year Selection">
				<li role="presentation"><button type="button" role="tab">2030</button></li>
				<li role="presentation"><button type="button" role="tab">2029</button></li>
				<li role="presentation"><button type="button" role="tab">2028</button></li>
				<li role="presentation"><button type="button" role="tab">2027</button></li>
				<li role="presentation"><button type="button" role="tab">2026</button></li>
				<li role="presentation"><button type="button" role="tab" class="active" aria-selected="true">2025</button></li>
			</ul>
			<button type="button" class="arrow next" aria-label="다음 연도">다음</button>
		</div>
		
		<h2 class="sound_only">News Clippings List</h2>
		
		<div class="board_top">
			<div class="title_area">
				<span class="total">Total <strong class="c_iden">11</strong></span>
				<span class="page">Page <strong class="c_iden">1</strong>/2</span>
			</div>
			
			<form action="{{ route('media.news') }}" method="get" class="search_wrap">
				<fieldset>
					<legend class="sound_only">Search posts</legend>
					
					<label for="search-condition" class="sound_only">Select search criteria</label>
					<select name="search_condition" id="search-condition" class="text">
						<option value="title">Title</option>
						<option value="content">Content</option>
					</select>
					
					<div class="search_area">
						<label for="search-keyword" class="sound_only">Enter search term</label>
						<input type="text" id="search-keyword" name="search_keyword" class="text" placeholder="Please enter a search term.">
						<button type="submit" class="btn">Search</button>
					</div>
				</fieldset>
			</form>
		</div>
		
		<ul class="news_list">
			<li>
				<article>
					<a href="{{ route('media.news.view') }}">
						<span class="type">NEWS</span>
						<h3 class="tit">Global Forum Highlights Innovation and International Cooperation</h3>
						<p>The 2026 Global Forum brought together government representatives, industry leaders, researchers, and international organizations to discuss emerging technologies and new opportunities for global cooperation.</p>
						<span class="date">2025.07.22</span>
						<span class="btn btn_wbb">View Original Article</span>
					</a>
				</article>
			</li>
			<li>
				<article>
					<a href="{{ route('media.news.view') }}">
						<span class="type">NEWS</span>
						<h3 class="tit">Global Forum Highlights Innovation and International Cooperation</h3>
						<p>The 2026 Global Forum brought together government representatives, industry leaders, researchers, and international organizations to discuss emerging technologies and new opportunities for global cooperation.</p>
						<span class="date">2025.07.22</span>
						<span class="btn btn_wbb">View Original Article</span>
					</a>
				</article>
			</li>
			<li>
				<article>
					<a href="{{ route('media.news.view') }}">
						<span class="type">NEWS</span>
						<h3 class="tit">Global Forum Highlights Innovation and International Cooperation</h3>
						<p>The 2026 Global Forum brought together government representatives, industry leaders, researchers, and international organizations to discuss emerging technologies and new opportunities for global cooperation.</p>
						<span class="date">2025.07.22</span>
						<span class="btn btn_wbb">View Original Article</span>
					</a>
				</article>
			</li>
			<li>
				<article>
					<a href="{{ route('media.news.view') }}">
						<span class="type">NEWS</span>
						<h3 class="tit">Global Forum Highlights Innovation and International Cooperation</h3>
						<p>The 2026 Global Forum brought together government representatives, industry leaders, researchers, and international organizations to discuss emerging technologies and new opportunities for global cooperation.</p>
						<span class="date">2025.07.22</span>
						<span class="btn btn_wbb">View Original Article</span>
					</a>
				</article>
			</li>
			<li>
				<article>
					<a href="{{ route('media.news.view') }}">
						<span class="type">NEWS</span>
						<h3 class="tit">Global Forum Highlights Innovation and International Cooperation</h3>
						<p>The 2026 Global Forum brought together government representatives, industry leaders, researchers, and international organizations to discuss emerging technologies and new opportunities for global cooperation.</p>
						<span class="date">2025.07.22</span>
						<span class="btn btn_wbb">View Original Article</span>
					</a>
				</article>
			</li>
			<li>
				<article>
					<a href="{{ route('media.news.view') }}">
						<span class="type">NEWS</span>
						<h3 class="tit">Global Forum Highlights Innovation and International Cooperation</h3>
						<p>The 2026 Global Forum brought together government representatives, industry leaders, researchers, and international organizations to discuss emerging technologies and new opportunities for global cooperation.</p>
						<span class="date">2025.07.22</span>
						<span class="btn btn_wbb">View Original Article</span>
					</a>
				</article>
			</li>
		</ul>

		<div class="board_bottom">
			<nav class="paging" aria-label="게시판 페이지 이동">
				<a href="?page=1" class="arrow two first" aria-label="첫 페이지로 이동">처음</a>
				<a href="?page=1" class="arrow one prev" aria-label="이전 페이지로 이동">이전</a>
				
				<a href="?page=1" class="on" aria-current="page" aria-label="현재 1페이지">1</a>
				<a href="?page=2" aria-label="2페이지로 이동">2</a>
				<a href="?page=3" aria-label="3페이지로 이동">3</a>
				<a href="?page=4" aria-label="4페이지로 이동">4</a>
				<a href="?page=5" aria-label="5페이지로 이동">5</a>
				
				<a href="?page=2" class="arrow one next" aria-label="다음 페이지로 이동">다음</a>
				<a href="?page=2" class="arrow two last" aria-label="마지막 페이지로 이동">맨끝</a>
			</nav>
		</div>
	</section>
</div>
@endsection

@push('scripts')
<script src="/publishing-original-assets/js/script_archive.js"></script>
<script>

</script>
@endpush
