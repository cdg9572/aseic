@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/archive.css') !!}
{!! \App\Helpers\CssHelper::minTag('/css/board.css') !!}
@endsection

@section('content')
@if(isset($newsItems))
<div class="inner">
	<section class="program_list">
		@if($categories->isNotEmpty())
		<div class="years_select_tab flex">
			<button type="button" class="arrow prev" aria-label="이전 연도">이전</button>
			<ul class="tabs mb0" role="tablist" aria-label="Year Selection">
				@foreach($categories as $category)
				<li role="presentation">
					<form action="{{ route('media.news') }}" method="get">
						@if($searchKeyword !== '')<input type="hidden" name="search_condition" value="{{ $searchCondition }}"><input type="hidden" name="search_keyword" value="{{ $searchKeyword }}">@endif
						<button type="submit" name="category_id" value="{{ $category->id }}" role="tab" aria-selected="{{ $selectedCategoryId === $category->id ? 'true' : 'false' }}">{{ $category->name }}</button>
					</form>
				</li>
				@endforeach
			</ul>
			<button type="button" class="arrow next" aria-label="다음 연도">다음</button>
		</div>
		@endif

		<div class="board_top">
			<div class="title_area">
				<span class="total">Total <strong class="c_iden">{{ $newsItems->total() }}</strong></span>
				<span class="page">Page <strong class="c_iden">{{ $newsItems->currentPage() }}</strong>/{{ max(1, $newsItems->lastPage()) }}</span>
			</div>
			<form action="{{ route('media.news') }}" method="get" class="search_wrap">
				<fieldset>
					<legend class="sound_only">Search posts</legend>
					@if($selectedCategoryId !== null)<input type="hidden" name="category_id" value="{{ $selectedCategoryId }}">@endif
					<label for="search-condition" class="sound_only">Select search criteria</label>
					<select name="search_condition" id="search-condition" class="text">
						<option value="title" @selected($searchCondition === 'title')>Title</option>
						<option value="content" @selected($searchCondition === 'content')>Content</option>
					</select>
					<div class="search_area">
						<label for="search-keyword" class="sound_only">Enter search term</label>
						<input type="text" id="search-keyword" name="search_keyword" value="{{ $searchKeyword }}" class="text" placeholder="Please enter a search term.">
						<button type="submit" class="btn">Search</button>
					</div>
				</fieldset>
			</form>
		</div>

		@if($newsItems->isNotEmpty())
		<h2 class="sound_only">News Clippings List</h2>
		<ul class="news_list">
			@foreach($newsItems as $newsItem)
			<li>
				<article>
					<a href="{{ route('media.news.view', ['mediaContent' => $newsItem->id]) }}">
						<span class="type">NEWS</span>
						<h3 class="tit">{{ $newsItem->title }}</h3>
						@if(filled(strip_tags((string) $newsItem->content)))<p>{{ \Illuminate\Support\Str::limit(strip_tags($newsItem->content), 180) }}</p>@endif
						@if($newsItem->published_date)<span class="date">{{ $newsItem->published_date->format('Y.m.d') }}</span>@endif
						<span class="btn btn_wbb">View Details</span>
					</a>
				</article>
			</li>
		@endforeach
		</ul>
		@endif
		@include('components.frontend-pagination', ['paginator' => $newsItems])
	</section>
</div>
@elseif(($mainPage?->folder_name ?? null) === 'publishing-original')
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
@endif
@endsection

@if(isset($newsItems) || ($mainPage?->folder_name ?? null) === 'publishing-original')
@push('scripts')
<script src="/js/script_archive.js"></script>
@endpush
@endif
