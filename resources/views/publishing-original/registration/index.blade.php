@extends('publishing-original.layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('publishing-original-assets/css/registration.css') }}" media="all">
@endsection

@section('content')
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
@endsection
