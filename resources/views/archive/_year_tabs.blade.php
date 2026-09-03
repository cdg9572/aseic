@if(($categories ?? collect())->isNotEmpty())
<div class="years_select_tab flex">
	<button type="button" class="arrow prev" aria-label="이전 연도">이전</button>
	<ul class="tabs mb0" role="tablist" aria-label="Year Selection">
		@foreach($categories as $category)
		<li role="presentation">
			<form action="{{ route($routeName, ['mainPage' => $mainPage->folder_name]) }}" method="get">
				<button type="submit" name="category_id" value="{{ $category->id }}" role="tab" aria-selected="{{ $selectedCategoryId === $category->id ? 'true' : 'false' }}">{{ $category->name }}</button>
			</form>
		</li>
		@endforeach
	</ul>
	<button type="button" class="arrow next" aria-label="다음 연도">다음</button>
</div>
@endif
