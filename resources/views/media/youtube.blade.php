@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/archive.css') !!}
@endsection

@section('content')
@if(isset($youtubeItems))
<div class="inner">
	<section class="program_list">
		@if($categories->isNotEmpty())
		<div class="years_select_tab flex">
			<button type="button" class="arrow prev" aria-label="이전 연도">이전</button>
			<ul class="tabs mb0" role="tablist" aria-label="Year Selection">
				@foreach($categories as $category)
				<li role="presentation">
					<form action="{{ route('media.youtube', ['mainPage' => $mainPage->folder_name]) }}" method="get">
						<button type="submit" name="category_id" value="{{ $category->id }}" role="tab" aria-selected="{{ $selectedCategoryId === $category->id ? 'true' : 'false' }}">{{ $category->name }}</button>
					</form>
				</li>
				@endforeach
			</ul>
			<button type="button" class="arrow next" aria-label="다음 연도">다음</button>
		</div>
		@endif

		@foreach($youtubeItems as $youtubeItem)
		<div class="youtube">
			@if(filled($youtubeItem->title))<h2 class="ctit">{{ $youtubeItem->title }}</h2>@endif
			@if($youtubeItem->embed_url)
			<div class="youtube_area flex_center"><iframe width="1280" height="670" src="{{ $youtubeItem->embed_url }}" title="{{ $youtubeItem->title ?: 'YouTube video player' }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>
			@endif
		</div>
		@endforeach
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

		<div class="youtube">
			<h2 class="ctit">2025 YouTube</h2>
			<div class="youtube_area flex_center"><iframe width="1280" height="670" src="https://www.youtube.com/embed/iYpl9ExsFjg?si=KYUIc0SdR_Bj5t9L" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>
		</div>

	</section>
</div>

@endif
@endsection

@if(isset($youtubeItems))
@push('scripts')
<script src="/js/script_archive.js"></script>
@endpush
@elseif(($mainPage?->folder_name ?? null) === 'publishing-original')
@push('scripts')
<script src="/js/script_archive.js"></script>
@endpush
@endif
