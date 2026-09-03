@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/registration.css') !!}
@endsection

@section('content')
@if($registrationPage ?? null)
<div class="inner">
	<section class="registration_wrap">
		<div class="flex_center"><h2 class="stit">{{ $registrationPage->page_title }}</h2></div>
		@if($registrationPage->participation_mode === \App\Models\RegistrationPage::MODE_NOT_PARTICIPATING)
			@if(filled(strip_tags((string) $registrationPage->closed_notice)))
			<div class="scon wbox">{!! $registrationPage->closed_notice !!}</div>
			@endif
		@else
			@if(filled($registrationPage->period_text))
			<article class="scon">
				<h3 class="btit">Pre-registration Period</h3>
				<div class="Periodbox round_box">{{ $registrationPage->period_text }}</div>
			</article>
			@endif
			@php
				$guideSteps = collect([$registrationPage->guide_step_1, $registrationPage->guide_step_2, $registrationPage->guide_step_3])->filter(fn ($step) => filled($step))->values();
			@endphp
			@if($guideSteps->isNotEmpty())
			<article class="scon">
				<h3 class="btit">Registration Process Guide</h3>
				<ol class="flex guide_area">
					@foreach($guideSteps as $step)
					<li class="i{{ $loop->iteration }} round_box"><span class="num">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><strong>{{ $step }}</strong></li>
					@endforeach
				</ol>
			</article>
			@endif
			@if(!$registrationOpen && $registrationPage->use_custom_end_text && filled($registrationPage->registration_end_text))
			<div class="scon wbox tac">{{ $registrationPage->registration_end_text }}</div>
			@endif
			<div class="flex_center btns_btm">
				<a href="{{ route('registration.confirm') }}" class="btn btn_kwg">Confirm Registration</a>
				@if($registrationOpen)<a href="{{ route('registration.register') }}" class="btn btn_wbb">Register Now</a>@endif
			</div>
		@endif
	</section>
</div>
@elseif(($mainPage?->folder_name ?? null) === 'publishing-original')
<div class="inner">
	<section class="registration_wrap">
		<div class="flex_center"><h2 class="stit">Registration Information</h2></div>
		<article class="scon">
			<h3 class="btit">Pre-registration Period</h3>
			<div class="Periodbox round_box"><time datetime="2026-06-01">June 1 (Mon)</time> ~ <time datetime="2026-07-10">July 10 (Fri), 2026</time></div>
		</article>
		<article class="scon">
			<h3 class="btit">Registration Process Guide</h3>
			<ol class="flex guide_area">
				<li class="i1 round_box"><span class="num">01</span><strong>Click [Register Now]</strong></li>
				<li class="i2 round_box"><span class="num">02</span><strong>Enter registration information</strong></li>
				<li class="i3 round_box"><span class="num">03</span><strong>Complete registration</strong></li>
			</ol>
		</article>
		<div class="flex_center btns_btm">
			<a href="{{ route('registration.confirm') }}" class="btn btn_kwg">Confirm Registration</a>
			<a href="{{ route('registration.register') }}" class="btn btn_wbb">Register Now</a>
		</div>
	</section>
</div>
@endif
@endsection
