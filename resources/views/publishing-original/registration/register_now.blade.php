@extends('publishing-original.layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('publishing-original-assets/css/registration.css') }}" media="all">
@endsection

@section('content')
<div class="inner">
	<section class="register_now">
		<h2 class="stit mb0">Register Now</h2>
		
		<form action="#" method="POST" id="form-registration">
			@csrf
			<div class="scon">
				<p class="tb">Please ensure that your name, affiliation, and title/position are entered accurately, as they will appear on your name badge.</p>
				<div class="excl tar c_iden">Fields marked with * are required.</div>
				<div class="tbl">
					<table>
						<tbody>
							<tr>
								<th><label for="reg-first-name">First Name <span class="c_iden">*</span></label></th>
								<td><input type="text" id="reg-first-name" name="first_name" class="text wlong" placeholder="please enter your first name" required></td>
							</tr>
							<tr>
								<th><label for="reg-last-name">Last Name <span class="c_iden">*</span></label></th>
								<td><input type="text" id="reg-last-name" name="last_name" class="text wlong" placeholder="please enter your last name" required></td>
							</tr>
							<tr>
								<th><label for="reg-affiliation">Affiliation <span class="c_iden">*</span></label></th>
								<td><input type="text" id="reg-affiliation" name="affiliation" class="text wlong" placeholder="please enter your affiliation" required></td>
							</tr>
							<tr>
								<th><label for="reg-position">Job Title / Position <span class="c_iden">*</span></label></th>
								<td><input type="text" id="reg-position" name="position" class="text wlong" placeholder="please enter your job title" required></td>
							</tr>
							<tr>
								<th><label for="reg-mobile">Mobile Number <span class="c_iden">*</span></label></th>
								<td>
									<input type="tel" id="reg-mobile" name="mobile" class="text wlong" placeholder="e.g. + 82 10-1234-5678" required>
									<p class="excl c_iden">* Please include your country code (e.g. +1, +44, +82).</p>
								</td>
							</tr>
							<tr>
								<th><label for="reg-email">E-mail <span class="c_iden">*</span></label></th>
								<td><input type="email" id="reg-email" name="email" class="text wlong" placeholder="e.g. email@email.com" required></td>
							</tr>
							<tr>
								<th>Attendance Mode <span class="c_iden">*</span></th>
								<td>
									<div class="flex radio_group">
										<label class="radio"><input type="radio" name="attendance_mode" class="sound_only" value="In-person" checked><i></i><span>In-person</span></label>
										<label class="radio"><input type="radio" name="attendance_mode" class="sound_only" value="Online"><i></i><span>Online</span></label>
									</div>
								</td>
							</tr>
							<tr>
								<th>Session Attendance Plan <span class="c_iden">*</span></th>
								<td>
									<div class="flex chk_group">
										<label class="chk_round"><input type="checkbox" name="session_plan[]" class="sound_only" value="Day 1 Morning Session"> <span>Day 1 Morning Session</span></label>
										<label class="chk_round"><input type="checkbox" name="session_plan[]" class="sound_only" value="Day 1 Afternoon Session"> <span>Day 1 Afternoon Session</span></label>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				
				<div class="rtit">Luncheon Notice <span class="c_iden">*</span></div>
				<div class="tbl tarms_area">
					<div class="textarea" tabindex="0">
						Lunch will be provided on a first-come, first-served basis, limited to one meal per person, for pre-registered participants who complete on-site check-in.<br/>
						Service may end early once the available meals have been fully allocated.
					</div>
					<label class="check">
						<input type="checkbox" name="luncheon_agree" id="luncheon_agree" class="sound_only" value="Y" required>
						<i></i>
						<span>I have read and understood the above notice.</span>
					</label>
				</div>
				
				<div class="rtit">Privacy Policy and Consent to Collection and Use of Personal Information <span class="c_iden">*</span></div>
				<div class="tbl tarms_area">
					<div class="textarea" tabindex="0">
						@include('publishing-original.terms.txt_privacy-policy')
					</div>
					<label class="check">
						<input type="checkbox" name="privacy_agree" id="privacy_agree" class="sound_only" value="Y" required>
						<i></i>
						<span>I agree to the collection and use of personal information.</span>
					</label>
					
					<div class="captcha_area">
						<div class="tit"><label for="reg-captcha">CAPTCHA <span class="c_iden">*</span></label></div>
						<div class="flex">
							<div class="img"><img src="/publishing-original-assets/images/captcha_sample.png" alt="CAPTCHA Image"></div>
							<button type="button" class="btn btn_sound" title="Audio CAPTCHA" aria-label="Audio CAPTCHA"></button>
							<button type="button" class="btn btn_reset" title="Refresh CAPTCHA" aria-label="Refresh CAPTCHA"></button>
							<div class="input_excl">
								<input type="text" id="reg-captcha" name="captcha" class="text" title="Enter CAPTCHA" required>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="flex_center btns_btm mt80">
				<a href="{{ route('registration.index') }}" class="btn btn_kwg">Cancel</a>
				<button type="submit" class="btn btn_wbb">Submit Registration</button>
			</div>
		</form>
	</section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
	const form = document.getElementById('form-registration');
	
	form.addEventListener('submit', (e) => {
		// 세션 선택 체크 여부 검증
		const sessionChecked = form.querySelectorAll('input[name="session_plan[]"]:checked');
		if (sessionChecked.length === 0) {
			e.preventDefault();
			alert('Please select at least one session attendance plan.');
			const firstSession = form.querySelector('input[name="session_plan[]"]');
			if (firstSession) firstSession.focus();
			return false;
		}

		// 오찬 동의 체크 여부 검증
		const luncheonAgree = document.getElementById('luncheon_agree');
		if (!luncheonAgree.checked) {
			e.preventDefault();
			alert('Please agree to the Luncheon Notice.');
			luncheonAgree.focus();
			return false;
		}

		// 개인정보 수집 동의 체크 여부 검증
		const privacyAgree = document.getElementById('privacy_agree');
		if (!privacyAgree.checked) {
			e.preventDefault();
			alert('Please agree to the Privacy Policy.');
			privacyAgree.focus();
			return false;
		}
	});
});
</script>
@endpush
