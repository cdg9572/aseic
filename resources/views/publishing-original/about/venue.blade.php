@extends('publishing-original.layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('publishing-original-assets/css/about.css') }}" media="all">
@endsection

@section('content')
<div class="inner">
	<section class="scon venue_area" aria-labelledby="Venue-heading">
		<h2 id="Venue-heading" class="sound_only">Venue</h2>
		
		<div class="address_area">
			<div class="map">
				<!-- 첫 번째 메인 지도는 즉시 로드 -->
				<iframe src="https://maps.google.com/maps?q=33.240386,126.422205&amp;t=&amp;z=16&amp;ie=UTF8&amp;iwloc=&amp;output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="eager" referrerpolicy="no-referrer-when-downgrade" title="Map of International Convention Center Jeju"></iframe>
			</div>
			<div class="txt">
				<p class="name">2026 Global <br class="pc_vw">Eco-Innovation Forum</p>
				<dl class="info">
					<div class="i1">
						<dt>Venue</dt>
						<dd>
							<p>Halla Hall (3F), International Convention Center Jeju</p>
							<small>224 Jungmungwangwang-ro, Seogwipo-si, Jeju-do</small>
						</dd>
					</div>
					<div class="i2">
						<dt>Date</dt>
						<dd>
							<time datetime="2026-09-02T09:30">September 2, 2026 (9:30AM - 5:00PM)</time>
						</dd>
					</div>
					<div class="i3">
						<dt>Format</dt>
						<dd>Offline &amp; Online</dd>
					</div>
				</dl>
			</div>
		</div>
		
		<div class="tabs_area">
			<ul class="tabs" role="tablist" aria-label="Transportation Options">
				<li role="presentation"><button type="button" role="tab" id="tab-bus" aria-selected="true" aria-controls="panel-bus">BUS</button></li>
				<li role="presentation"><button type="button" role="tab" id="tab-subway" aria-selected="false" aria-controls="panel-subway" tabindex="-1">SUBWAY</button></li>
				<li role="presentation"><button type="button" role="tab" id="tab-taxi" aria-selected="false" aria-controls="panel-taxi" tabindex="-1">TAXI</button></li>
			</ul>
			<div class="cont">
				<!-- BUS Tab Panel -->
				<div id="panel-bus" class="con" role="tabpanel" aria-labelledby="tab-bus">
					<div class="txt">
						<h3 class="tit">From Jeju International Airport (Limousine Bus #600)</h3>
						<p>Please take Airport Limousine Bus #600 at Gate 5 (Bus Stop 5) on the 1st floor of Jeju International Airport and get off at the ICC Jeju stop.<br/>It takes approximately 50-60 minutes.</p>
					</div>
					<div class="map_area">
						<!-- data-src로 지연 로딩 처리 -->
						<iframe data-src="https://maps.google.com/maps?q=33.506511,126.492067&amp;t=&amp;z=17&amp;ie=UTF8&amp;iwloc=&amp;output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map of Jeju Airport Limousine Bus Stop"></iframe>
					</div>
				</div>

				<!-- SUBWAY Tab Panel -->
				<div id="panel-subway" class="con" role="tabpanel" aria-labelledby="tab-subway" hidden>
					<div class="txt">
						<h3 class="tit">Subway Information</h3>
						<p>Please note that Jeju Island does not operate a subway system. Please use Bus #600 or a Taxi from Jeju International Airport to arrive at ICC Jeju.</p>
					</div>
					<div class="map_area">
						<!-- data-src로 지연 로딩 처리 -->
						<iframe data-src="https://maps.google.com/maps?q=33.240386,126.422205&amp;t=&amp;z=17&amp;ie=UTF8&amp;iwloc=&amp;output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map of Destination - ICC Jeju"></iframe>
					</div>
				</div>

				<!-- TAXI Tab Panel -->
				<div id="panel-taxi" class="con" role="tabpanel" aria-labelledby="tab-taxi" hidden>
					<div class="txt">
						<h3 class="tit">From Jeju International Airport (Taxi)</h3>
						<p>Board a taxi at the designated Taxi Stand located across the crosswalk from Gate 3 on the 1st floor of Jeju International Airport. Tell the driver "ICC Jeju in Jungmun". Travel time is approximately 40-50 minutes.</p>
					</div>
					<div class="map_area">
						<!-- data-src로 지연 로딩 처리 -->
						<iframe data-src="https://maps.google.com/maps?q=33.506940,126.493200&amp;t=&amp;z=17&amp;ie=UTF8&amp;iwloc=&amp;output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map of Jeju Airport Taxi Stand"></iframe>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
	// 1. 순차적 iframe 지연 로딩 (메인 지도 우선 로드 후 300ms 간격 순차 할당)
	const lazyIframes = Array.from(document.querySelectorAll('iframe[data-src]'));
	
	function loadNextIframe(index) {
		if (index >= lazyIframes.length) return;
		const iframe = lazyIframes[index];
		iframe.setAttribute('src', iframe.getAttribute('data-src'));
		iframe.removeAttribute('data-src');

		iframe.addEventListener('load', function () {
			setTimeout(function () {
				loadNextIframe(index + 1);
			}, 200);
		}, { once: true });
	}

	// 페이지 로드 완료 후 500ms 뒤부터 하단 지도 순차 로딩 시작
	setTimeout(function () {
		loadNextIframe(0);
	}, 500);
});
</script>
<script src="/publishing-original-assets/js/script_tab.js"></script>
@endpush
