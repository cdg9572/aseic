@extends('publishing-original.layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('publishing-original-assets/css/programme.css') }}" media="all">
<link rel="stylesheet" href="{{ asset('publishing-original-assets/css/table.css') }}" media="all">
@endsection

@section('content')
<div class="inner">
	<section class="program_list tabs_area">
		<ul class="tabs" role="tablist" aria-label="Programme Schedule">
			<li role="presentation">
				<button type="button" role="tab" id="tab-day1" aria-selected="true" aria-controls="panel-day1">DAY 1</button>
			</li>
			<li role="presentation">
				<button type="button" role="tab" id="tab-day2" aria-selected="false" aria-controls="panel-day2" tabindex="-1">DAY 2</button>
			</li>
		</ul>
		
		<div class="cont">
			<!-- DAY 1 TAB PANEL -->
			<div id="panel-day1" class="con_panel" role="tabpanel" aria-labelledby="tab-day1">
				<h2 class="btit">WEDNESDAY, 4 MARCH</h2>
				<p class="gmt">All times shown in GMT</p>
				
				<div class="tbl_radius">
					<div class="head">
						<div class="time">Time</div>
						<div class="program">Program</div>
					</div>
					<div class="body">
						<!-- 등록 (상세 없음) -->
						<div class="con">
							<time class="time" datetime="09:00/09:30">09:00AM - 09:30AM (GMT)</time>
							<div class="program">Registration</div>
						</div>

						<!-- 개회식 (상세 있음: indetail / 기본 열림) -->
						<div class="con indetail active">
							<time class="time" datetime="09:30/09:50">09:30AM - 09:50AM (GMT)</time>
							<div class="program_wrap">
								<div class="program_head">
									<div class="program">Opening Ceremony</div>
									<button type="button" class="btn_accordion_toggle" aria-expanded="true" aria-controls="detail-01">
										<span class="sound_only">Opening Ceremony details </span>
										<span class="toggle_icon" aria-hidden="true"></span>
									</button>
								</div>
								<ul id="detail-01" class="detail">
									<li>
										<div class="tt">Opening Remarks</div>
										<p>Mr. Kwang-cheon Chung, Chairman, ASEM SMES Eco-Innovation Center (ASEIC)</p>
									</li>
									<li>
										<div class="tt">Welcome Remarks</div>
										<p>Mr. Soonbai Lee, Director General, Global Growth Policy, Ministry of SMEs and Startups, Republic of Korea</p>
									</li>
									<li>
										<div class="tt">Congratulatory Remarks</div>
										<p>Mr. Yeon-chul Yoo, Executive Director, UN Global Compact Network Korea</p>
										<p>Mrs. Ae-Suk Kang, Director General, Climate and Environment Division, Jeju Special Self-governing Province</p>
									</li>
								</ul>
							</div>
						</div>

						<!-- 기조 연설 (indetail / 기본 열림) -->
						<div class="con indetail active">
							<time class="time" datetime="09:50/10:30">09:50AM - 10:30AM (GMT)</time>
							<div class="program_wrap">
								<div class="program_head">
									<div class="program">Keynote Speeches</div>
									<button type="button" class="btn_accordion_toggle" aria-expanded="true" aria-controls="detail-02">
										<span class="sound_only">Keynote Speeches details </span>
										<span class="toggle_icon" aria-hidden="true"></span>
									</button>
								</div>
								<ul id="detail-02" class="detail">
									<li>
										<div class="tt">Global Policy Directions for Green &amp; Inclusive SME Growth</div>
										<p>Mr. Vuyani Jarana, Chairperson, G20 Startup20 Engagement Group</p>
									</li>
									<li>
										<div class="tt">Climate-smart Food Innovation for Local Resilience</div>
										<p>Professor. Paul Teng, RSIS, Nanyang Technological University, Singapore</p>
									</li>
								</ul>
							</div>
						</div>

						<!-- 휴식 (상세 없음) -->
						<div class="con">
							<time class="time" datetime="10:30/10:50">10:30AM - 10:50AM (GMT)</time>
							<div class="program">Networking Break</div>
						</div>

						<!-- 세션 1 (indetail / 기본 열림) -->
						<div class="con indetail active">
							<time class="time" datetime="10:50/11:20">10:50AM - 11:20AM (GMT)</time>
							<div class="program_wrap">
								<div class="program_head">
									<div class="program">Session 1. Policy Foundation for Climate-smart SME Innovation</div>
									<button type="button" class="btn_accordion_toggle" aria-expanded="true" aria-controls="detail-03">
										<span class="sound_only">Session 1 details </span>
										<span class="toggle_icon" aria-hidden="true"></span>
									</button>
								</div>
								<ul id="detail-03" class="detail">
									<li>
										<div class="tt">Policy and Institutional Readiness for Green Transition</div>
										<p>Mrs. Alis Daniela Torres, Head of Green Digital Transformation, ICLEI Europe</p>
									</li>
									<li>
										<div class="tt">Local governance for Climate Action</div>
										<p>Mr. Ki Won Lee, Co-Chairman, World Food Tech Council</p>
									</li>
								</ul>
							</div>
						</div>

						<!-- 패널 토론 1 (indetail / 기본 열림) -->
						<div class="con indetail active">
							<time class="time" datetime="11:20/12:00">11:20AM - 12:00PM (GMT)</time>
							<div class="program_wrap">
								<div class="program_head">
									<div class="program">Panel Discussion and Q&amp;A</div>
									<button type="button" class="btn_accordion_toggle" aria-expanded="true" aria-controls="detail-04">
										<span class="sound_only">Panel Discussion 1 details </span>
										<span class="toggle_icon" aria-hidden="true"></span>
									</button>
								</div>
								<ul id="detail-04" class="detail">
									<li>
										<div class="tt">Moderator</div>
										<p>Dr. Giulia Ajmone Marsan, Head of Startups and Digital Inclusion, ERIA</p>
									</li>
									<li>
										<div class="tt">Panelists <span>Session 1 Speakers</span></div>
										<p>Dr. Michiel Stapper, Assistant Professor, University of Amsterdam</p>
										<p>Mr. Chorn Vanthou, Deputy Director of SMEs Department, Ministry of Industry, Science, Technology and Innovation, Kingdom of Cambodia</p>
										<p>Mr. Azman Abdullah, Deputy Undersecretary, Ministry of Entrepreneur and Cooperatives Development, Malaysia</p>
									</li>
								</ul>
							</div>
						</div>

						<!-- 점심 (상세 없음) -->
						<div class="con">
							<time class="time" datetime="12:00/13:30">12:00PM - 13:30PM (GMT)</time>
							<div class="program">Lunch Break (3F Delizia)</div>
						</div>

						<!-- 세션 2 (indetail / 기본 열림) -->
						<div class="con indetail active">
							<time class="time" datetime="13:30/13:50">13:30PM - 13:50PM (GMT)</time>
							<div class="program_wrap">
								<div class="program_head">
									<div class="program">Session 2. Startups and Innovation for Thriving Local Economies</div>
									<button type="button" class="btn_accordion_toggle" aria-expanded="true" aria-controls="detail-05">
										<span class="sound_only">Session 2 details </span>
										<span class="toggle_icon" aria-hidden="true"></span>
									</button>
								</div>
								<ul id="detail-05" class="detail">
									<li>
										<div class="tt">Tech Based Agri/Food Innovation Case Studies</div>
										<p>Dr. Long Phi HO, CTO, Enfarm AgriTech, Viet Nam</p>
										<p>Ms. Sia Lee, CEO, GREENGRIM Inc., Republic of Korea</p>
										<p>Mr. Adi Reza Nugroho, CEO, MYCL, Indonesia</p>
										<p>Ms. Elén Faxö, CEO, OlsAro Crop Biotech, Sweden</p>
									</li>
								</ul>
							</div>
						</div>

						<!-- 패널 토론 2 (indetail / 기본 열림) -->
						<div class="con indetail active">
							<time class="time" datetime="13:50/14:40">13:50PM - 14:40PM (GMT)</time>
							<div class="program_wrap">
								<div class="program_head">
									<div class="program">Panel Discussion and Q&amp;A</div>
									<button type="button" class="btn_accordion_toggle" aria-expanded="true" aria-controls="detail-06">
										<span class="sound_only">Panel Discussion 2 details </span>
										<span class="toggle_icon" aria-hidden="true"></span>
									</button>
								</div>
								<ul id="detail-06" class="detail">
									<li>
										<div class="tt">Moderator</div>
										<p>Ms. Asel Doranova, Senior Researcher, Tilburg Sustainability Center</p>
									</li>
									<li>
										<div class="tt">Panelists <span>Session 2 Speakers</span></div>
										<p>Ms. Angela Tay, Senior Investment Associate, AgFunder</p>
										<p>Ms. Seong Hyun Oh, Principal, D3Jubilee Partners</p>
									</li>
								</ul>
							</div>
						</div>

						<!-- 휴식 (상세 없음) -->
						<div class="con">
							<time class="time" datetime="14:40/15:00">14:40PM - 15:00PM (GMT)</time>
							<div class="program">Networking Break</div>
						</div>

						<!-- 세션 3 (indetail / 기본 열림) -->
						<div class="con indetail active">
							<time class="time" datetime="15:00/15:30">15:00PM - 15:30PM (GMT)</time>
							<div class="program_wrap">
								<div class="program_head">
									<div class="program">Session 3. Scaling Local SMEs through Innovation and Partnerships</div>
									<button type="button" class="btn_accordion_toggle" aria-expanded="true" aria-controls="detail-07">
										<span class="sound_only">Session 3 details </span>
										<span class="toggle_icon" aria-hidden="true"></span>
									</button>
								</div>
								<ul id="detail-07" class="detail">
									<li>
										<div class="tt">Advancing Digital Transformation &amp; AI for Sustainable &amp; Inclusive Local Economic Revitalization</div>
										<p>Mr. Farrukh Alimdjanov, Industrial Development Officer, UNIDO</p>
									</li>
									<li>
										<div class="tt">Leveraging Digital Technologies for ESG Management: Case Studies and Corporate Growth Strategies</div>
										<p>Mr. Yong Guk Choi, Manager, SK Inc.</p>
									</li>
								</ul>
							</div>
						</div>

						<!-- 패널 토론 3 (indetail / 기본 열림) -->
						<div class="con indetail active">
							<time class="time" datetime="15:30/16:10">15:30PM - 16:10PM (GMT)</time>
							<div class="program_wrap">
								<div class="program_head">
									<div class="program">Panel Discussion and Q&amp;A</div>
									<button type="button" class="btn_accordion_toggle" aria-expanded="true" aria-controls="detail-08">
										<span class="sound_only">Panel Discussion 3 details </span>
										<span class="toggle_icon" aria-hidden="true"></span>
									</button>
								</div>
								<ul id="detail-08" class="detail">
									<li>
										<div class="tt">Moderator</div>
										<p>Ms. Yana Roessl, International Expert of SMEs Upgrading, UNIDO</p>
									</li>
									<li>
										<div class="tt">Panelists <span>Session 3 Speakers</span></div>
										<p>Mr. Yong-Hyun Kim, CEO, GINT</p>
										<p>Dr. Malaykham Philaphone, Deputy Director General, Department for SME Promotion, Ministry of Industry of Commerce, Lao PDR</p>
										<p>Mr. Karl Lyndon B. Pacolor, Officer-in-Charge Assistant Director, Bureau of Policy Research and Innovation, Department of Trade and Industry</p>
									</li>
								</ul>
							</div>
						</div>

						<!-- 휴식 (상세 없음) -->
						<div class="con">
							<time class="time" datetime="16:10/16:20">16:10PM - 16:20PM (GMT)</time>
							<div class="program">Networking Break</div>
						</div>

						<!-- 종합 패널 토론 (indetail / 기본 열림) -->
						<div class="con indetail active">
							<time class="time" datetime="16:20/16:55">16:20PM - 16:55PM (GMT)</time>
							<div class="program_wrap">
								<div class="program_head">
									<div class="program">Integrated Panel Discussion<br/>Bringing It All Together: Toward Climate-smart Innovation</div>
									<button type="button" class="btn_accordion_toggle" aria-expanded="true" aria-controls="detail-09">
										<span class="sound_only">Integrated Panel Discussion details </span>
										<span class="toggle_icon" aria-hidden="true"></span>
									</button>
								</div>
								<ul id="detail-09" class="detail">
									<li>
										<div class="tt">Moderator</div>
										<p>Mr. Juhern Kim, Country Representative to Viet Nam, GGGI</p>
									</li>
									<li>
										<div class="tt">Panelists</div>
										<p>Keynote Speakers and Speakers from Session 1~3</p>
									</li>
								</ul>
							</div>
						</div>

						<!-- 폐회식 (상세 없음) -->
						<div class="con">
							<time class="time" datetime="16:55/17:00">16:55PM - 17:00PM (GMT)</time>
							<div class="program">
								Closing Ceremony
								<p class="speaker_sub">Mr. Seok-tae Lee, Secretary-General, ASEM SMEs Eco-Innovation Center (ASEIC)</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- DAY 2 TAB PANEL (초기 숨김) -->
			<div id="panel-day2" class="con_panel" role="tabpanel" aria-labelledby="tab-day2" hidden>
				<h2 class="btit">THURSDAY, 5 MARCH</h2>
				<p class="gmt">Program Schedule to be announced.</p>
			</div>
		</div>
	</section>
</div>
@endsection

@push('scripts')
<script src="/publishing-original-assets/js/script_tab.js"></script>
<script src="/publishing-original-assets/js/script_accordion.js"></script>
@endpush
