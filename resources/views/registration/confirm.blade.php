@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/registration.css') !!}
@endsection

@section('content')


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

@endsection

@push('scripts')
<script src="/js/script_pop_image.js"></script>
@endpush