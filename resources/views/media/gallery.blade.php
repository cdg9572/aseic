@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/archive.css') !!}
{!! \App\Helpers\CssHelper::minTag('/css/board.css') !!}
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

		<h2 class="sound_only">Photo Gallery List</h2>
		<ul class="gallery_basic">
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title1"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title2"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title3"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title4"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title5"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title6"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title7"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title8"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title9"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title10"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title11"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title12"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title13"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title14"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title15"></span></button></li>
			<li><button type="button"><span class="imgfit"><img src="/images/img_sample_gallery.avif" data-large-src="/images/img_sample_gallery_large.avif" alt="Image Title16"></span></button></li>
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
@endsection

@push('scripts')
<script src="/js/script_archive.js"></script>
<script src="/js/script_pop_image.js"></script>
@endpush