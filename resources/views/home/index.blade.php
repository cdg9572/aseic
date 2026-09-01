@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/main.css') !!}
{!! \App\Helpers\CssHelper::minTag('/css/popup.css') !!}
@endsection

@section('content')
@php
    $programmeItems = $mainPage->programme_item_list;
    $programmeBackground = $mainPage->programme_background_path
        ? (str_starts_with($mainPage->programme_background_path, '/') ? asset(ltrim($mainPage->programme_background_path, '/')) : asset('storage/'.$mainPage->programme_background_path))
        : null;
    $registerBackground = $mainPage->register_background_path
        ? (str_starts_with($mainPage->register_background_path, '/') ? asset(ltrim($mainPage->register_background_path, '/')) : asset('storage/'.$mainPage->register_background_path))
        : null;
@endphp
<h1 class="sound_only">ASEIC 메인 페이지</h1>

<x-main.main-visual :banners="$banners" :main-page="$mainPage" />
<x-main.speaker-slider :speakers="$speakers" />

@if ($programmeItems !== [])
<section class="mcon main_programme">
	@if ($programmeBackground)
		<div class="main-section-background imgfit" aria-hidden="true"><img src="{{ $programmeBackground }}" alt=""></div>
	@endif
	<div class="inner flex">
		<div class="mtit"><h2>PROGRAMME</h2><a href="{{ route('programme.list') }}" class="btn_link btn_bg_gra">More Info</a></div>
		<ul class="timetable">
			@foreach ($programmeItems as $item)
				<li>
					<div class="time">{{ $item['time'] }}</div>
					<div class="cont">
						@if ($item['subject'] !== '')<strong>{{ $item['subject'] }}</strong>@endif
						@if ($item['content'] !== '')<p>{{ $item['content'] }}</p>@endif
					</div>
				</li>
			@endforeach
		</ul>
	</div>
</section>
@endif

<section class="mcon main_register">
	@if ($registerBackground)
		<div class="main-section-background imgfit" aria-hidden="true"><img src="{{ $registerBackground }}" alt=""></div>
	@endif
	<div class="inner">
		<div class="flex">
			<div class="mtit"><h2>REGISTER</h2></div>
			<p>Join the forum and be part of the conversation on sustainable innovation <br/>Complete your registration to participate in the programme and connect with experts from around the world.</p>
			<a href="{{ route('registration.index') }}" class="btn_link btn_more">Register Now</a>
		</div>
	</div>
</section>

<section class="mcon main_board">
	<div class="flex inner">
		<div @class(['box', 'main-board-full' => ! $mainPage->past_forum_video_url])>
			<div class="mtit"><h2>ANNOUNCEMENTS</h2><a href="{{ route('announcements.index') }}" class="btn_link btn_bg_gra">More Info</a></div>
			<ul class="list">
				@foreach ($noticePosts as $noticePost)
					<li><a href="{{ route('announcements.view') }}"><div class="tit">{{ $noticePost->title }}</div><div class="date">{{ \Illuminate\Support\Carbon::parse($noticePost->created_at)->format('Y.m.d') }}</div></a></li>
				@endforeach
			</ul>
		</div>
		@if ($mainPage->past_forum_video_url)
		<div class="box">
			<div class="mtit"><h2>PAST FORUM VIDEO</h2><a href="{{ route('media.youtube') }}" class="btn_link btn_bg_gra">More Info</a></div>
			<div class="video_box">
				<a href="{{ $mainPage->past_forum_video_url }}" target="_blank" rel="noopener"><img src="/images/img_sample_video.avif" alt="{{ $mainPage->event_name }} Past Forum Video"></a>
			</div>
		</div>
		@endif
	</div>
</section>

@endsection

@push('scripts')
<link rel="stylesheet" href="/css/swiper.css" media="all">
<script src="/js/swiper.js"></script>
<script src="{{ asset('js/popup.js') }}?v={{ filemtime(public_path('js/popup.js')) }}"></script>
@endpush

@section('popups')
@if($popups->count() > 0)
    @foreach($popups as $popup)
        @if($popup->popup_display_type === 'normal')
            <div hidden
                 data-popup-window
                 data-popup-id="{{ $popup->id }}"
                 data-popup-url="{{ route('popup.show', $popup->id) }}"
                 data-popup-name="popup_{{ $popup->id }}"
                 data-popup-features="width={{ $popup->width }},height={{ $popup->height }},left={{ $popup->position_left ?? 100 }},top={{ $popup->position_top ?? 100 }},scrollbars=yes,resizable=yes,menubar=no,toolbar=no,location=no,status=no"></div>
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
