@php
	$programmeSessions = $programmePage->sessions
		->where('is_active', true)
		->map(function (\App\Models\ProgrammePageSession $session): array {
			$members = $session->speakers
				->where('is_active', true)
				->map(static function (\App\Models\Speaker $speaker): array {
					$image = null;
					if ($speaker->is_image_visible && $speaker->profile_image) {
						$image = str_starts_with($speaker->profile_image, '/')
							? asset(ltrim($speaker->profile_image, '/'))
							: asset('storage/'.$speaker->profile_image);
					}

					$attachment = $speaker->attachment_files[0]['path'] ?? null;
					$file = $attachment
						? (str_starts_with($attachment, '/') ? asset(ltrim($attachment, '/')) : asset('storage/'.$attachment))
						: null;

					return [
						'img' => $image,
						'type' => \App\Models\Speaker::roleOptions()[$speaker->role] ?? $speaker->role,
						'name' => $speaker->full_name,
						'position' => $speaker->position,
						'affiliation' => $speaker->affiliation,
						'bio' => $speaker->content,
						'file' => $file,
					];
				})
				->values();

			return [
				'day_number' => $session->day_number,
				'name' => $session->session_name,
				'members' => $members,
			];
		})
		->values();
@endphp

<div class="inner">
	<section class="steering_committee_area tabs_area">
		@if($programmeSessions->isNotEmpty())
		<ul class="tabs" role="tablist" aria-label="Programme Speakers">
			@foreach($programmeSessions as $session)
			<li role="presentation">
				<button type="button" role="tab" id="tab-day{{ $session['day_number'] }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" aria-controls="panel-day{{ $session['day_number'] }}" @if(!$loop->first) tabindex="-1" @endif>DAY {{ $session['day_number'] }}</button>
			</li>
			@endforeach
		</ul>

		<div class="cont">
			@foreach($programmeSessions as $session)
			<div id="panel-day{{ $session['day_number'] }}" class="con_panel" role="tabpanel" aria-labelledby="tab-day{{ $session['day_number'] }}" @if(!$loop->first) hidden @endif>
				<h2 class="btit">{{ $session['name'] ?: 'DAY '.$session['day_number'].' SESSION' }}</h2>
				@if($session['members']->isNotEmpty())
				<ul class="flex">
					@foreach($session['members'] as $member)
					<li>
						<button type="button" class="btn-open-modal" aria-haspopup="dialog" aria-controls="modal-steering"
							data-img="{{ $member['img'] }}"
							@if(filled($member['type'])) data-type="{{ $member['type'] }}" @endif
							data-name="{{ $member['name'] }}"
							data-position="{{ $member['position'] }}"
							data-affiliation="{{ $member['affiliation'] }}"
							data-bio="{{ $member['bio'] }}"
							@if($member['file']) data-file="{{ $member['file'] }}" @endif>
							<span class="imgfit" aria-hidden="true">@if($member['img'])<img src="{{ $member['img'] }}" alt="">@endif</span>
							@if(filled($member['type']))<span class="type type-{{ \Illuminate\Support\Str::slug($member['type']) }}">{{ $member['type'] }}</span>@endif
							<span class="name">{{ $member['name'] }}</span>
							<span class="position">{{ $member['position'] }}</span>
							<span class="affiliation">{{ $member['affiliation'] }}</span>
						</button>
					</li>
					@endforeach
				</ul>
				@endif
			</div>
			@endforeach
		</div>
		@endif
	</section>
</div>

<div id="modal-steering" class="popup popup_steering" role="dialog" aria-modal="true" aria-labelledby="modal-title" hidden>
	<div class="dm" data-close="true"></div>
	<div class="inbox" tabindex="-1">
		<h3 id="modal-title" class="sound_only">Member Detail</h3>
		<button type="button" class="btn_close" aria-label="Close modal">&times;</button>
		<button type="button" class="arrow showPrev" aria-label="Previous member">이전</button>
		<button type="button" class="arrow showNext" aria-label="Next member">다음</button>
		<div class="scroll">
			<div class="profile">
				<div class="imgfit"><img id="modal-img" src="" alt=""></div>
				<div class="info_area">
					<p id="type" class="type"></p>
					<p id="name" class="name"></p>
					<div class="info">
						<p id="position" class="position"></p>
						<p id="affiliation" class="affiliation"></p>
						<a class="btn_download">Download</a>
					</div>
				</div>
			</div>
			<div id="modal-bio" class="modal-bio"></div>
		</div>
	</div>
</div>

@push('scripts')
<script src="/js/script_speakers.js"></script>
@endpush
