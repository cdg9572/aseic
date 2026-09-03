@extends('layouts.app')
@section('has_h1', true)

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/archive.css') !!}
{!! \App\Helpers\CssHelper::minTag('/css/board.css') !!}
@endsection

@section('content')
@if($newsItem ?? null)
<div class="inner">
	<section class="board_view">
		<article>
			<div class="tit_area">
				<h1 class="tit">{{ $newsItem->title }}</h1>
				<dl class="info">
					@if($newsItem->published_date)<div class="info_item"><dt>Date</dt><dd>{{ $newsItem->published_date->format('Y.m.d') }}</dd></div>@endif
					<div class="info_item"><dt>Views</dt><dd>{{ $newsItem->view_count }}</dd></div>
				</dl>
			</div>
			@if(filled(strip_tags((string) $newsItem->content)) || filled($newsItem->image_path))
			<div class="cont">
				@if(filled($newsItem->image_path))
				@php
					$newsImageUrl = str_starts_with($newsItem->image_path, '/')
						? asset(ltrim($newsItem->image_path, '/'))
						: asset('storage/'.$newsItem->image_path);
				@endphp
				<div class="tac"><img src="{{ $newsImageUrl }}" alt="{{ $newsItem->title }}"></div>
				@endif
				{!! $newsItem->content !!}
			</div>
			@endif
		</article>
		<div class="board_bottom flex_center">
			<a href="{{ route('media.news', ['category_id' => $newsItem->category_id]) }}" class="btn btn_wbb">Back to List</a>
		</div>
	</section>
</div>
@elseif(($mainPage?->folder_name ?? null) === 'publishing-original')
<div class="inner">
	<section class="board_view">
		<article>
			<div class="tit_area">
				<h1 class="tit">Global Forum Highlights Innovation and International Cooperatio</h1>
				<dl class="info">
					<div class="info_item"><dt>Date</dt><dd>2025.07.22</dd></div>
					<div class="info_item"><dt>Views</dt><dd>100</dd></div>
				</dl>
			</div>

			<div class="cont">
				<div class="tac"><img src="/images/img_sample_news_large.avif" alt="사진 설명"></div>
				<br/>
				The 2026 Global Forum brought together government representatives, industry leaders, researchers, <br/>and international organizations to discuss emerging technologies and new opportunities for global cooperation.
			</div>

			<ul class="img_area">
				<li><button type="button"><span class="imgfit"><img src="/images/img_sample_news.avif" data-large-src="/images/img_sample_news_large.avif" alt="Image Title1"></span></button></li>
				<li><button type="button"><span class="imgfit"><img src="/images/img_sample_news.avif" data-large-src="/images/img_sample_news_large.avif" alt="Image Title2"></span></button></li>
				<li><button type="button"><span class="imgfit"><img src="/images/img_sample_news.avif" data-large-src="/images/img_sample_news_large.avif" alt="Image Title3"></span></button></li>
			</ul>
		</article>

		<div class="board_bottom flex_center">
			<a href="{{ route('media.news') }}" class="btn btn_wbb">Back to List</a>
		</div>
	</section>
</div>

<!-- 모달 팝업 -->
<div id="modal-pop-image" class="popup popup_pop_image" role="dialog" aria-modal="true" aria-labelledby="modal-title" hidden>
	<div class="dm" data-close="true"></div>
	<div class="inbox" tabindex="-1">
		<h3 id="modal-title" class="sound_only">Image Title</h3>
		<button type="button" class="btn_close" aria-label="Close modal">&times;</button>

		<button type="button" class="arrow showPrev" aria-label="Previous member">이전</button>
		<button type="button" class="arrow showNext" aria-label="Next member">다음</button>

		<div class="imgfit"><img id="modal-img" src="/images/img_sample_gallery_large.avif" alt="Image Title"></div>
	</div>
</div>
@endif
@endsection

@if(($mainPage?->folder_name ?? null) === 'publishing-original')
@push('scripts')
<script src="/js/script_pop_image.js"></script>
@endpush
@endif
