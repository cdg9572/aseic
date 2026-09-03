@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/theme.css') !!}
@endsection

@section('content')

@if($programmePage ?? null)
<!-- 관리자 Theme 연동 -->
<section class="theme_info_area" aria-labelledby="programme-theme-overview-heading">
	<div class="inner">
		<h2 id="programme-theme-overview-heading" class="sound_only">Programme Theme Overview</h2>
		<div class="infobox">
			<p class="forum_subtitle">{{ $mainPage->event_name }}</p>
			@if(filled(strip_tags((string) $programmePage->title)))
			<div class="tit">{!! $programmePage->title !!}</div>
			@endif
			@if(filled($programmePage->event_date) || filled($programmePage->location))
			<ul class="info_list">
				@if(filled($programmePage->event_date))
				<li class="i1"><span class="sound_only">Date and Time: </span>{{ $programmePage->event_date }}</li>
				@endif
				@if(filled($programmePage->location))
				<li class="i2"><span class="sound_only">Venue: </span>{{ $programmePage->location }}</li>
				@endif
			</ul>
			@endif
		</div>
	</div>
</section>

@if(filled(strip_tags((string) $programmePage->content)))
<section class="theme_detail_area" aria-labelledby="theme-details-heading">
	<div class="inner">
		<h2 id="theme-details-heading" class="sound_only">Programme Details</h2>
		<div class="wbox">{!! $programmePage->content !!}</div>
	</div>
</section>
@endif
@elseif(($mainPage?->folder_name ?? null) === 'publishing-original')

<!-- 포럼 핵심 정보 (Hero Section) -->
<section class="theme_info_area" aria-labelledby="programme-theme-overview-heading">
	<div class="inner">
		<h2 id="programme-theme-overview-heading" class="sound_only">Programme Theme Overview</h2>
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

@endif

@endsection
