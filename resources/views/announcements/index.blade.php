@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/archive.css') !!}
{!! \App\Helpers\CssHelper::minTag('/css/board.css') !!}
@endsection

@section('content')
@if(isset($announcements))
<div class="inner">
	<section class="announcements_list">
		<div class="board_top">
			<h2 class="board_tit">Announcements</h2>
			<form action="{{ route('announcements.index') }}" method="get" class="search_wrap" role="search">
				<fieldset>
					<legend class="sound_only">Search announcements</legend>
					<label for="search-condition" class="sound_only">Select search criteria</label>
					<select name="search_condition" id="search-condition" class="text"><option value="title" @selected($searchCondition === 'title')>Title</option><option value="content" @selected($searchCondition === 'content')>Content</option></select>
					<div class="search_area"><label for="search-keyword" class="sound_only">Enter search term</label><input type="text" id="search-keyword" name="search_keyword" value="{{ $searchKeyword }}" class="text" placeholder="Please enter a search term."><button type="submit" class="btn">Search</button></div>
				</fieldset>
			</form>
		</div>

		@if($announcements->isNotEmpty())
		<div class="board_basic">
			<table>
				<caption class="sound_only">List of announcements, providing NO, Title, View, and Date information.</caption>
				<colgroup><col class="brd_num"><col class="brd_tit"><col class="brd_view"><col class="brd_date"></colgroup>
				<thead><tr><th scope="col">NO</th><th scope="col">Title</th><th scope="col">View</th><th scope="col">Date</th></tr></thead>
				<tbody>
					@foreach($announcements as $announcement)
					<tr @class(['notice' => $announcement->is_notice, 'new' => $announcement->created_at->isAfter(now()->subDays(7))])>
						<td class="brd_num">@if($announcement->is_notice)<span class="sound_only">Notice</span>Notice @else{{ $announcements->total() - $announcements->firstItem() - $loop->index + 1 }}@endif</td>
						<td class="brd_tit"><a href="{{ route('announcements.view', ['announcement' => $announcement->id]) }}">@if($announcement->created_at->isAfter(now()->subDays(7)))<span class="sound_only">[New post]</span>@endif{{ $announcement->title }}</a></td>
						<td class="brd_view"><span class="sound_only">Views: </span>{{ $announcement->view_count }}</td>
						<td class="brd_date"><span class="sound_only">Date: </span>{{ $announcement->created_at->format('Y.m.d') }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
		@endif
		@include('components.frontend-pagination', ['paginator' => $announcements])
	</section>
</div>
@elseif(($mainPage?->folder_name ?? null) === 'publishing-original')
<div class="inner">
	<section class="announcements_list">

		<div class="board_top">
			<h2 class="board_tit">Announcements</h2>

			<form action="{{ route('announcements.index') }}" method="get" class="search_wrap" role="search">
				<fieldset>
					<legend class="sound_only">Search announcements</legend>

					<label for="search-condition" class="sound_only">Select search criteria</label>
					<select name="search_condition" id="search-condition" class="text">
						<option value="title" {{ request('search_condition') === 'title' ? 'selected' : '' }}>Title</option>
						<option value="content" {{ request('search_condition') === 'content' ? 'selected' : '' }}>Content</option>
					</select>

					<div class="search_area">
						<label for="search-keyword" class="sound_only">Enter search term</label>
						<input type="text" id="search-keyword" name="search_keyword" value="{{ request('search_keyword') }}" class="text" placeholder="Please enter a search term.">
						<button type="submit" class="btn">Search</button>
					</div>
				</fieldset>
			</form>
		</div>

		<div class="board_basic">
			<table>
				<caption class="sound_only">List of announcements, providing NO, Title, View, and Date information.</caption>
				<colgroup>
					<col class="brd_num">
					<col class="brd_tit">
					<col class="brd_view">
					<col class="brd_date">
				</colgroup>
				<thead>
					<tr>
						<th scope="col">NO</th>
						<th scope="col">Title</th>
						<th scope="col">View</th>
						<th scope="col">Date</th>
					</tr>
				</thead>
				<tbody>
					<tr class="notice new">
						<td class="brd_num"><span class="sound_only">Notice</span>10</td>
						<td class="brd_tit"><a href="{{ route('announcements.view') }}"><span class="sound_only">[New post]</span>It’s the space where the title goes in.</a></td>
						<td class="brd_view"><span class="sound_only">Views: </span>100</td>
						<td class="brd_date"><span class="sound_only">Date: </span>2026.01.01</td>
					</tr>
					<tr class="notice">
						<td class="brd_num"><span class="sound_only">Notice</span>9</td>
						<td class="brd_tit"><a href="{{ route('announcements.view') }}"><span class="sound_only">[New post]</span>It’s the space where the title goes in.</a></td>
						<td class="brd_view"><span class="sound_only">Views: </span>100</td>
						<td class="brd_date"><span class="sound_only">Date: </span>2026.01.01</td>
					</tr>
					<tr class="new">
						<td class="brd_num">8</td>
						<td class="brd_tit"><a href="{{ route('announcements.view') }}"><span class="sound_only">[New post]</span>It’s the space where the title goes in.</a></td>
						<td class="brd_view"><span class="sound_only">Views: </span>100</td>
						<td class="brd_date"><span class="sound_only">Date: </span>2026.01.01</td>
					</tr>
					<tr>
						<td class="brd_num">7</td>
						<td class="brd_tit"><a href="{{ route('announcements.view') }}">It’s the space where the title goes in.</a></td>
						<td class="brd_view"><span class="sound_only">Views: </span>100</td>
						<td class="brd_date"><span class="sound_only">Date: </span>2026.01.01</td>
					</tr>
					<tr>
						<td class="brd_num">6</td>
						<td class="brd_tit"><a href="{{ route('announcements.view') }}">It’s the space where the title goes in.</a></td>
						<td class="brd_view"><span class="sound_only">Views: </span>100</td>
						<td class="brd_date"><span class="sound_only">Date: </span>2026.01.01</td>
					</tr>
					<tr>
						<td class="brd_num">5</td>
						<td class="brd_tit"><a href="{{ route('announcements.view') }}">It’s the space where the title goes in.</a></td>
						<td class="brd_view"><span class="sound_only">Views: </span>100</td>
						<td class="brd_date"><span class="sound_only">Date: </span>2026.01.01</td>
					</tr>
					<tr>
						<td class="brd_num">4</td>
						<td class="brd_tit"><a href="{{ route('announcements.view') }}">It’s the space where the title goes in.</a></td>
						<td class="brd_view"><span class="sound_only">Views: </span>100</td>
						<td class="brd_date"><span class="sound_only">Date: </span>2026.01.01</td>
					</tr>
					<tr>
						<td class="brd_num">3</td>
						<td class="brd_tit"><a href="{{ route('announcements.view') }}">It’s the space where the title goes in.</a></td>
						<td class="brd_view"><span class="sound_only">Views: </span>100</td>
						<td class="brd_date"><span class="sound_only">Date: </span>2026.01.01</td>
					</tr>
					<tr>
						<td class="brd_num">2</td>
						<td class="brd_tit"><a href="{{ route('announcements.view') }}">It’s the space where the title goes in.</a></td>
						<td class="brd_view"><span class="sound_only">Views: </span>100</td>
						<td class="brd_date"><span class="sound_only">Date: </span>2026.01.01</td>
					</tr>
					<tr>
						<td class="brd_num">1</td>
						<td class="brd_tit"><a href="{{ route('announcements.view') }}">It’s the space where the title goes in.</a></td>
						<td class="brd_view"><span class="sound_only">Views: </span>100</td>
						<td class="brd_date"><span class="sound_only">Date: </span>2026.01.01</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="board_bottom">
			<nav class="paging" aria-label="Board pagination">
				<a href="?page=1" class="arrow two first" aria-label="Go to first page">First</a>
				<a href="?page=1" class="arrow one prev" aria-label="Go to previous page">Previous</a>

				<a href="?page=1" class="on" aria-current="page" aria-label="Current page 1">1</a>
				<a href="?page=2" aria-label="Go to page 2">2</a>
				<a href="?page=3" aria-label="Go to page 3">3</a>
				<a href="?page=4" aria-label="Go to page 4">4</a>
				<a href="?page=5" aria-label="Go to page 5">5</a>

				<a href="?page=2" class="arrow one next" aria-label="Go to next page">Next</a>
				<a href="?page=2" class="arrow two last" aria-label="Go to last page">Last</a>
			</nav>
		</div>
	</section>
</div>
@endif
@endsection
