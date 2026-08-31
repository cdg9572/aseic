@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/main.css') !!}
@endsection

@section('content')
<h1 class="sound_only">ASEIC 메인 페이지</h1>

<x-main.main-visual />
<x-main.speaker-slider />

<section class="mcon main_programme">
	<div class="inner flex">
		<div class="mtit"><h2>PROGRAMME</h2><a href="/programme" class="btn_link btn_bg_gra">More Info</a></div>
		<ul class="timetable">
			<li>
				<div class="time">09:00 AM ~ 09:30 AM</div>
				<div class="cont">
					<strong>Registration</strong>
				</div>
			</li>
			<li>
				<div class="time">09:30 AM ~ 09:50 AM</div>
				<div class="cont">
					<strong>Opening Ceremony</strong>
					<p>Opening Remarks, Welcome Remarks, Congratulatory Remarks </p>
				</div>
			</li>
			<li>
				<div class="time">09:50 AM ~ 10:30 AM</div>
				<div class="cont">
					<strong>Keynote Speeches</strong>
					<p>Global Policy Directions for Green & Inclusive SME Growth</p>
				</div>
			</li>
			<li>
				<div class="time">10:30 AM ~ 10:50 AM</div>
				<div class="cont">
					<strong>Networking Break</strong>
				</div>
			</li>
		</ul>
	</div>
</section>

<section class="mcon main_register">
	<div class="inner">
		<div class="flex">
			<div class="mtit"><h2>REGISTER</h2></div>
			<p>Join the forum and be part of the conversation on sustainable innovation <br/>Complete your registration to participate in the programme and connect with experts from around the world.</p>
			<a href="/registration" class="btn_link btn_more">Register Now</a>
		</div>
	</div>
</section>

<section class="mcon main_board">
	<div class="flex inner">
		<div class="box">
			<div class="mtit"><h2>ANNOUNCEMENTS</h2><a href="/announcements" class="btn_link btn_bg_gra">More Info</a></div>
			<ul class="list">
				<li><a href="/announcements/view"><div class="tit">Event Information</div><div class="date">2026.07.13</div></a></li>
				<li><a href="/announcements/view"><div class="tit">frequently asked question(FAQ)</div><div class="date">2026.07.13</div></a></li>
				<li><a href="/announcements/view"><div class="tit">frequently asked question(FAQ)</div><div class="date">2026.07.13</div></a></li>
				<li><a href="/announcements/view"><div class="tit">Pre-registration information</div><div class="date">2026.07.13</div></a></li>
			</ul>
		</div>
		<div class="box">
			<div class="mtit"><h2>PAST FORUM VIDEO</h2><a href="/media/youtube" class="btn_link btn_bg_gra">More Info</a></div>
			<div class="video_box">
				<a href="https://www.youtube.com/shorts/CCpPwCRE-f4" target="_blank"><img src="/images/img_sample_video.avif" alt="2025 글로벌 에코 이노베이션 포럼 영상"></a>
			</div>
		</div>
	</div>
</section>

@endsection

@push('scripts')
<link rel="stylesheet" href="/css/swiper.css" media="all">
<script src="/js/swiper.js"></script>
@endpush

@section('popups')
@if($popups->count() > 0)
    @foreach($popups as $popup)
        @if($popup->popup_display_type === 'normal')
            {{-- 일반팝업 (새창) --}}
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const popupUrl = '{{ route("popup.show", $popup->id) }}';
                    const popupFeatures = 'width={{ $popup->width }},height={{ $popup->height }},left={{ $popup->position_left ?? 100 }},top={{ $popup->position_top ?? 100 }},scrollbars=yes,resizable=yes,menubar=no,toolbar=no,location=no,status=no';
                    window.open(popupUrl, 'popup_{{ $popup->id }}', popupFeatures);
                });
            </script>
        @else
            {{-- 레이어팝업 (오버레이) --}}
            <div class="popup-layer popup-fixed"
                 id="popup-{{ $popup->id }}"
                 data-popup-id="{{ $popup->id }}"
                 data-display-type="layer"
                 style="position:absolute !important; width:{{ $popup->width }}px; height:auto; top:{{ $popup->position_top }}px; left:{{ $popup->position_left }}px; z-index:99999;">

                <div class="popup-body">
                    @if($popup->popup_type === 'image' && $popup->popup_image)
                        @if($popup->url)
                            <a href="{{ $popup->url }}" target="{{ $popup->url_target }}">
                                <img src="{{ asset('storage/' . $popup->popup_image) }}" alt="{{ $popup->title }}">
                            </a>
                        @else
                            <img src="{{ asset('storage/' . $popup->popup_image) }}" alt="{{ $popup->title }}">
                        @endif
                    @elseif($popup->popup_type === 'html' && $popup->popup_content)
                        {!! $popup->popup_content !!}
                    @endif
                </div>

                <div class="popup-footer">
                    <label class="popup-today-label" data-popup-id="{{ $popup->id }}">
                        <input type="checkbox" class="popup-today-close" data-popup-id="{{ $popup->id }}">
                        1일 동안 보지 않음
                    </label>
                    <button type="button" class="popup-footer-close-btn" data-popup-id="{{ $popup->id }}">닫기</button>
                </div>
            </div>
        @endif
    @endforeach
@endif
@endsection