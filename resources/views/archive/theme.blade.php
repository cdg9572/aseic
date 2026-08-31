@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/archive.css') !!}
{!! \App\Helpers\CssHelper::minTag('/css/theme.css') !!}
@endsection

@section('content')

<!-- 포럼 핵심 정보 (Hero Section) -->
<section class="theme_info_area" aria-labelledby="archive-theme-overview-heading">
	<div class="inner">
		<h2 id="archive-theme-overview-heading" class="sound_only">Programme Theme Overview</h2>

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

		<div class="infobox">
			<p class="forum_subtitle">2026 Global Eco-Innovation Forum</p>
			<h3 class="tit">Climate-Smart Innovations for Sustainable Local Economies</h3>
			<ul class="info_list">
				<li class="i1">
					<span class="sound_only">Date and Time: </span>
					<time datetime="2026-09-02T09:30/17:00">September 2, 2026 · 9:30 AM–5:00 PM</time>
				</li>
				<li class="i2">
					<span class="sound_only">Venue: </span>
					Halla Hall, International Convention Center Jeju · Hybrid Forum
				</li>
			</ul>
		</div>
	</div>
</section>

<!-- 포럼 상세 소개 (Details Section) -->
<section class="theme_detail_area" aria-labelledby="theme-details-heading">
	<div class="inner">
		<h2 id="theme-details-heading" class="sound_only">Programme Details</h2>
		<div class="wbox">
			<p>The 2026 Global Eco-Innovation Forum will be held on <time datetime="2026-09-02">September 2, 2026</time>, at the International Convention Center Jeju.</p>
			<p>Hosted by the Ministry of SMEs and Startups of Korea and organized by ASEIC, the Forum is an official workshop of the 31st APEC SME Ministerial Meeting.</p>
			<p>With the theme <strong>"Climate-smart Innovations for Sustainable Local Economies"</strong>, the Forum will bring together international experts, policymakers, and business leaders to discuss practical strategies for addressing climate change and fostering sustainable growth.</p>
			<p>Key sessions will explore green technologies, policy and institutional innovation, and emerging business models, providing SMEs with insights and opportunities to thrive in the green transition.</p>
			<p>We warmly invite you to join us in shaping a more sustainable and resilient future.</p>
		</div>
	</div>
</section>

@endsection

@push('scripts')
<script src="/js/script_archive.js"></script>
@endpush