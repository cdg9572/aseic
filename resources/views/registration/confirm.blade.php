@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/registration.css') !!}
@endsection

@section('content')
@if($registrationPage ?? null)
<div class="inner">
	<section class="registration_wrap">
		<h2 class="stit">Registration Confirmation</h2>
		<form action="{{ route('registration.confirm.lookup') }}" method="POST">
			@csrf
			<div class="scon">
				<h3 class="rtit">Confirm Registration</h3>
				<div class="tbl"><table><tbody>
					<tr><th><label for="confirm-mobile">Mobile last 4 digits <span class="c_iden">*</span></label></th><td><input type="text" id="confirm-mobile" name="mobile_last_four" inputmode="numeric" maxlength="4" class="text wlong" required>@error('mobile_last_four')<p class="excl c_iden">{{ $message }}</p>@enderror</td></tr>
					<tr><th><label for="confirm-email">Email <span class="c_iden">*</span></label></th><td><input type="email" id="confirm-email" name="email" value="{{ old('email') }}" class="text wlong" required>@error('email')<p class="excl c_iden">{{ $message }}</p>@enderror</td></tr>
				</tbody></table></div>
				@if(session('registration_confirmation_error'))<p class="excl c_iden tac">{{ session('registration_confirmation_error') }}</p>@endif
			</div>
			<div class="flex_center btns_btm mt80"><button type="submit" class="btn btn_wbb">Confirm</button></div>
		</form>
	</section>
</div>

@if(session('registration_submitted') || session('registration_confirmed'))
<div id="registration-result-modal" class="popup popup_confirm is-active" role="dialog" aria-modal="true" aria-labelledby="registration-result-title" data-registration-result-modal>
	<div class="dm" data-close="true"></div>
	<div class="inbox" tabindex="-1">
		<h3 id="registration-result-title" class="tit">{{ session('registration_submitted') ? 'Registration Completed' : 'Registration Confirmed' }}</h3>
		<p>{{ session('registration_submitted') ? 'Your registration has been successfully completed.' : 'Your registration has been confirmed.' }}<br>Thank you for registering for the Forum.</p>
		<div class="flex_center btns_btm"><button type="button" class="btn btn_wbb" data-close="true">Confirm</button></div>
	</div>
</div>
@endif
@elseif(($mainPage?->folder_name ?? null) === 'publishing-original')


<div class="inner">
	<section class="registration_wrap">
		<h2 class="stit">Registration Confirmation</h2>

		<div class="scon">
			<h3 class="rtit">Confirm Registration</h3>
			<div class="tbl">
				<table>
					<tbody>
						<tr>
							<th>Mobile last 4 digits<span class="c_iden">*</span></th>
							<td><input type="text" class="text wlong" placeholder="Mobile last 4 digits"></td>
						</tr>
						<tr>
							<th>Email<span class="c_iden">*</span></th>
							<td><input type="text" class="text wlong" placeholder="Email"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="flex_center btns_btm mt80">
			<button type="button" class="btn btn_wbb btn_confirm">Confirm</button>
		</div>
	</section>
</div>

<!-- 모달 팝업 -->
<div id="modal-pop-image" class="popup popup_confirm" role="dialog" aria-modal="true" aria-labelledby="modal-title" hidden>
	<div class="dm" data-close="true"></div>
	<div class="inbox" tabindex="-1">
		<h3 id="modal-title" class="tit">Registration Completed</h3>
		<p>Your registration has been successfully completed.<br/>Thank you for registering for the Forum.</p>
		<div class="flex_center btns_btm">
			<button type="button" class="btn btn_wbb" data-close="true">Confirm</button>
		</div>
	</div>
</div>

@endif
@endsection

@if($registrationPage ?? null)
@push('scripts')
<script src="/js/registration.js"></script>
@endpush
@elseif(($mainPage?->folder_name ?? null) === 'publishing-original')
@push('scripts')
<script src="/js/script_pop_image.js"></script>
@endpush
@endif
