@extends('publishing-original.layouts.app')

@php
$committeeGroups = [
	'group1' => [
		[
			'img' => '/publishing-original-assets/images/img_steering_committee01.avif',
			'type' => 'SPEAKER',
			'name' => 'Dr. Giulia Ajmone MARSAN',
			'position' => 'Secretary General',
			'affiliation' => 'United Afro Experts for Innovation (UAEFI)',
			'bio' => '
				<p>Dr. Giulia Ajmone Marsan is an internationally recognised expert in innovation strategy and ecosystem development.</p>
				<p>With extensive experience in economic analysis, policy advisory, research, and capacity building, she specialises in innovation, technology, entrepreneurship, and startup creation.</p>
				<p>Throughout her career, she has advised a wide range of public- and private-sector leaders, including senior policymakers, government officials, international organisations, and key stakeholders worldwide.</p>
				<p>Her work spans Africa, ASEAN, the European Union, the Indo-Pacific, MENA, North America, and Latin America, and includes collaborations with Chambers of Commerce and Industry Associations, EU Institutions, the G20, the OECD, UN agencies, and multilateral development banks.</p>
				<p>Giulia is currently the Secretary General of United Afro Experts for Innovation (UAEFI), a newly established organisation dedicated to fostering innovation and entrepreneurship across high-growth and emerging markets.</p>
			',
			'file' => '/publishing-original-assets/images/logo.svg'
		],
		[
			'img' => '/publishing-original-assets/images/img_steering_committee02.avif',
			'type' => 'MODERATOR',
			'name' => 'Dr. Asel DORANOVA',
			'position' => 'Senior research fellow at Tilburg University',
			'affiliation' => 'Lead expert of Tilburg Sustainability Center',
			'bio' => '
				<p>Dr. Asel Doranova collaborates with AEM ASEIC since 2016. Asel has more than 20 years of policy <br class="pc_vw"/>and research experience working with EU institutions and international, national, and regional agencies.</p>
				<p>She has over a decade of involvement with the EU Eco-Innovation Observatory, contributing to research and policy development on eco-innovation, sustainable energy, circular economy, and bioeconomy transitions.</p>
				<p>Her current work focuses on energy and climate justice, examining both local dimensions and North–South <br class="pc_vw"/>perspectives of sustainability transitions. With graduate training in Environmental Sciences and Policy and Development Studies, she holds a PhD in Innovation and Technical Change, bringing an interdisciplinary perspective to sustainability <br class="pc_vw"/>research and policy.</p>
			',
			'link' => 'https://linkedin.com/'
		],
		[
			'img' => '/publishing-original-assets/images/img_steering_committee03.avif',
			'type' => 'PANELIST',
			'name' => 'Mr. Juhern KIM',
			'position' => 'Directorate for Green Growth Implementation',
			'affiliation' => 'Global Green Growth Institute (GGGI)',
			'bio' => '
				<p>Juhern Kim is a climate finance and green growth leader with over 20 years of experience across multilateral, public, and private sectors. He currently leads GGGI’s Viet Nam Country Office, focusing on sustainable and green finance, green bonds, green infrastructure, climate-tech startups, and industrial decarbonization.</p>
				<p>In Viet Nam, he has helped mobilize over USD 270 million in green finance, supported six green bond issuances, and accelerated more than 30 climate-tech startups.</p>
				<p>He previously served as GGGI Country Representative in the Philippines, Colombia, and held roles with the Green Climate Fund and UNEP. He was the founding manager of Greenpreneurs, GGGI’s first global initiative supporting green startups. <br/>He also advises climate-tech enterprises and climate-focused investors on growth, market entry, and investment opportunities.</p>
			',
			'link' => 'https://linkedin.com/'
		],
		[
			'img' => '/publishing-original-assets/images/img_steering_committee04.avif',
			'type' => 'START UP',
			'name' => 'Ms. Yana ROESSL',
			'position' => 'International Expert on SMEs Upgrading',
			'affiliation' => 'United Nations Industrial Development Organization (UNIDO)',
			'bio' => '
				<p>Dr. Giulia Ajmone Marsan is an internationally recognised expert in innovation strategy and ecosystem development.</p>
				<p>With extensive experience in economic analysis, policy advisory, research, and capacity building, she specialises in innovation, technology, entrepreneurship, and startup creation.</p>
				<p>Throughout her career, she has advised a wide range of public- and private-sector leaders, including senior policymakers, government officials, international organisations, and key stakeholders worldwide.</p>
				<p>Her work spans Africa, ASEAN, the European Union, the Indo-Pacific, MENA, North America, and Latin America, and includes collaborations with Chambers of Commerce and Industry Associations, EU Institutions, the G20, the OECD, UN agencies, and multilateral development banks.</p>
				<p>Giulia is currently the Secretary General of United Afro Experts for Innovation (UAEFI), a newly established organisation dedicated to fostering innovation and entrepreneurship across high-growth and emerging markets.</p>
			',
			'link' => 'https://linkedin.com/'
		],
		[
			'img' => '/publishing-original-assets/images/img_steering_committee01.avif',
			'type' => 'SPEAKER',
			'name' => 'Dr. Giulia Ajmone MARSAN',
			'position' => 'Secretary General',
			'affiliation' => 'United Afro Experts for Innovation (UAEFI)',
			'bio' => '
				<p>Dr. Giulia Ajmone Marsan is an internationally recognised expert in innovation strategy and ecosystem development.</p>
				<p>With extensive experience in economic analysis, policy advisory, research, and capacity building, she specialises in innovation, technology, entrepreneurship, and startup creation.</p>
				<p>Throughout her career, she has advised a wide range of public- and private-sector leaders, including senior policymakers, government officials, international organisations, and key stakeholders worldwide.</p>
				<p>Her work spans Africa, ASEAN, the European Union, the Indo-Pacific, MENA, North America, and Latin America, and includes collaborations with Chambers of Commerce and Industry Associations, EU Institutions, the G20, the OECD, UN agencies, and multilateral development banks.</p>
				<p>Giulia is currently the Secretary General of United Afro Experts for Innovation (UAEFI), a newly established organisation dedicated to fostering innovation and entrepreneurship across high-growth and emerging markets.</p>
			',
			'link' => 'https://linkedin.com/'
		]
	],
	
	'group2' => [
		[
			'img' => '/publishing-original-assets/images/img_steering_committee06.avif',
			'type' => 'SPEAKER',
			'name' => 'Mr. Michael SIEGNER',
			'position' => 'Circular Economy, and Start-ups',
			'affiliation' => 'Hanns Seidel Foundation',
			'bio' => '
				<p>Mr. Michael Siegner is the Hanns Seidel Foundation’s Regional Representative for Southeast Asia for Sustainable Consumption and Production, Circular Economy and Start-ups.</p>
				<p>Previously based in Vietnam and Myanmar and currently leading HSF’s team in Indonesia, he has extensive experience in international cooperation, policy dialogue and project management.</p>
				<p>His work focuses on climate governance, sustainable business models, inclusive decision-making and conflict-sensitive approaches. He holds degrees in Peace and Conflict Studies and Political and Administrative Science.</p>
			',
			'link' => 'https://linkedin.com/'
		],
		[
			'img' => '/publishing-original-assets/images/img_steering_committee07.avif',
			'type' => 'SPEAKER',
			'name' => 'Mr. Carsten BERMIG',
			'position' => 'Director, Governance & Sustainable Development',
			'affiliation' => 'Asia-Europe Foundation',
			'bio' => '
				<p>Mr. Carsten BERMIG, a German national, served over 20 years in the European Commission in Brussels, where he was a Political Advisor to the European Commissioner for Internal Market, Industry, Entrepreneurship and SMEs, and a Member of the Cabinet of European Commissioner for Climate Action.</p>
				<p>He also worked as Policy Coordinator for economic & financial affairs in the Secretariat-General and held various positions in the Directorates-General for Climate Action, for Enterprise and Industry, as well as Competition.</p>
				<p>Prior to joining the European Commission, he held academic positions in Germany and Poland. In Singapore, he was Head of the Climate, Energy and Nature Team at the British High Commission and Director of External Relations at Climate Impact X.</p>
				<p>He graduated with an MA (Diplom-Volkswirt) in Economics from the University of Cologne.</p>
				<p>He also holds an MA in European Studies from the College of Europe and an MBA from the Singapore Management University.</p>
			',
			'link' => 'https://linkedin.com/'
		]
	],
	
	'group3' => [
		[
			'img' => '/publishing-original-assets/images/img_steering_committee06.avif',
			'type' => 'SPEAKER',
			'name' => 'Mr. Michael SIEGNER',
			'position' => 'Circular Economy, and Start-ups',
			'affiliation' => 'Hanns Seidel Foundation',
			'bio' => '
				<p>Mr. Michael Siegner is the Hanns Seidel Foundation’s Regional Representative for Southeast Asia for Sustainable Consumption and Production, Circular Economy and Start-ups.</p>
				<p>Previously based in Vietnam and Myanmar and currently leading HSF’s team in Indonesia, he has extensive experience in international cooperation, policy dialogue and project management.</p>
				<p>His work focuses on climate governance, sustainable business models, inclusive decision-making and conflict-sensitive approaches. He holds degrees in Peace and Conflict Studies and Political and Administrative Science.</p>
			',
			'link' => 'https://linkedin.com/'
		],
		[
			'img' => '/publishing-original-assets/images/img_steering_committee07.avif',
			'type' => 'START UP',
			'name' => 'Mr. Carsten BERMIG',
			'position' => 'Director, Governance & Sustainable Development',
			'affiliation' => 'Asia-Europe Foundation',
			'bio' => '
				<p>Mr. Carsten BERMIG, a German national, served over 20 years in the European Commission in Brussels, where he was a Political Advisor to the European Commissioner for Internal Market, Industry, Entrepreneurship and SMEs, and a Member of the Cabinet of European Commissioner for Climate Action.</p>
				<p>He also worked as Policy Coordinator for economic & financial affairs in the Secretariat-General and held various positions in the Directorates-General for Climate Action, for Enterprise and Industry, as well as Competition.</p>
				<p>Prior to joining the European Commission, he held academic positions in Germany and Poland. In Singapore, he was Head of the Climate, Energy and Nature Team at the British High Commission and Director of External Relations at Climate Impact X.</p>
				<p>He graduated with an MA (Diplom-Volkswirt) in Economics from the University of Cologne.</p>
				<p>He also holds an MA in European Studies from the College of Europe and an MBA from the Singapore Management University.</p>
			',
			'link' => 'https://linkedin.com/'
		]
	],
	
	'group4' => [
		[
			'img' => '/publishing-original-assets/images/img_steering_committee06.avif',
			'type' => 'SPEAKER',
			'name' => 'Mr. Michael SIEGNER',
			'position' => 'Circular Economy, and Start-ups',
			'affiliation' => 'Hanns Seidel Foundation',
			'bio' => '
				<p>Mr. Michael Siegner is the Hanns Seidel Foundation’s Regional Representative for Southeast Asia for Sustainable Consumption and Production, Circular Economy and Start-ups.</p>
				<p>Previously based in Vietnam and Myanmar and currently leading HSF’s team in Indonesia, he has extensive experience in international cooperation, policy dialogue and project management.</p>
				<p>His work focuses on climate governance, sustainable business models, inclusive decision-making and conflict-sensitive approaches. He holds degrees in Peace and Conflict Studies and Political and Administrative Science.</p>
			',
			'link' => 'https://linkedin.com/'
		],
		[
			'img' => '/publishing-original-assets/images/img_steering_committee07.avif',
			'type' => 'SPEAKER',
			'name' => 'Mr. Carsten BERMIG',
			'position' => 'Director, Governance & Sustainable Development',
			'affiliation' => 'Asia-Europe Foundation',
			'bio' => '
				<p>Mr. Carsten BERMIG, a German national, served over 20 years in the European Commission in Brussels, where he was a Political Advisor to the European Commissioner for Internal Market, Industry, Entrepreneurship and SMEs, and a Member of the Cabinet of European Commissioner for Climate Action.</p>
				<p>He also worked as Policy Coordinator for economic & financial affairs in the Secretariat-General and held various positions in the Directorates-General for Climate Action, for Enterprise and Industry, as well as Competition.</p>
				<p>Prior to joining the European Commission, he held academic positions in Germany and Poland. In Singapore, he was Head of the Climate, Energy and Nature Team at the British High Commission and Director of External Relations at Climate Impact X.</p>
				<p>He graduated with an MA (Diplom-Volkswirt) in Economics from the University of Cologne.</p>
				<p>He also holds an MA in European Studies from the College of Europe and an MBA from the Singapore Management University.</p>
			',
			'link' => 'https://linkedin.com/'
		]
	]
];
@endphp

@section('styles')
<link rel="stylesheet" href="{{ asset('publishing-original-assets/css/speakers.css') }}" media="all">
@endsection

@section('content')
<div class="inner">
	<section class="steering_committee_area tabs_area">
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
				<h2 class="btit">SESSION 1</h2>
				<ul class="flex">
					@foreach($committeeGroups['group1'] as $member)
					<li>
						<button type="button" class="btn-open-modal" aria-haspopup="dialog" aria-controls="modal-steering"
							data-img="{{ $member['img'] }}"
							@if(!empty($member['type'])) data-type="{{ $member['type'] }}" @endif
							data-name="{{ $member['name'] }}"
							data-position="{{ $member['position'] }}"
							data-affiliation="{{ $member['affiliation'] }}"
							data-bio="{{ $member['bio'] }}"
							@if(!empty($member['file'])) data-file="{{ $member['file'] }}" @endif
							data-link="{{ $member['link'] ?? '' }}">
							<span class="imgfit" aria-hidden="true"><img src="{{ $member['img'] }}" alt=""></span>
							@if(!empty($member['type']))
							<span class="type type-{{ \Illuminate\Support\Str::slug($member['type']) }}">{{ $member['type'] }}</span>
							@endif
							<span class="name">{{ $member['name'] }}</span>
							<span class="position">{{ $member['position'] }}</span>
							<span class="affiliation">{{ $member['affiliation'] }}</span>
						</button>
					</li>
					@endforeach
				</ul>

				<h2 class="btit mt">SESSION 2</h2>
				<ul class="flex group-secondary">
					@foreach($committeeGroups['group2'] as $member)
					<li>
						<button type="button" class="btn-open-modal" aria-haspopup="dialog" aria-controls="modal-steering"
							data-img="{{ $member['img'] }}"
							@if(!empty($member['type'])) data-type="{{ $member['type'] }}" @endif
							data-name="{{ $member['name'] }}"
							data-position="{{ $member['position'] }}"
							data-affiliation="{{ $member['affiliation'] }}"
							data-bio="{{ $member['bio'] }}"
							@if(!empty($member['file'])) data-file="{{ $member['file'] }}" @endif
							data-link="{{ $member['link'] ?? '' }}">
							<span class="imgfit" aria-hidden="true"><img src="{{ $member['img'] }}" alt=""></span>
							@if(!empty($member['type']))
							<span class="type type-{{ \Illuminate\Support\Str::slug($member['type']) }}">{{ $member['type'] }}</span>
							@endif
							<span class="name">{{ $member['name'] }}</span>
							<span class="position">{{ $member['position'] }}</span>
							<span class="affiliation">{{ $member['affiliation'] }}</span>
						</button>
					</li>
					@endforeach
				</ul>

				<h2 class="btit mt">SESSION 3</h2>
				<ul class="flex group-secondary">
					@foreach($committeeGroups['group3'] as $member)
					<li>
						<button type="button" class="btn-open-modal" aria-haspopup="dialog" aria-controls="modal-steering"
							data-img="{{ $member['img'] }}"
							@if(!empty($member['type'])) data-type="{{ $member['type'] }}" @endif
							data-name="{{ $member['name'] }}"
							data-position="{{ $member['position'] }}"
							data-affiliation="{{ $member['affiliation'] }}"
							data-bio="{{ $member['bio'] }}"
							@if(!empty($member['file'])) data-file="{{ $member['file'] }}" @endif
							data-link="{{ $member['link'] ?? '' }}">
							<span class="imgfit" aria-hidden="true"><img src="{{ $member['img'] }}" alt=""></span>
							@if(!empty($member['type']))
							<span class="type type-{{ \Illuminate\Support\Str::slug($member['type']) }}">{{ $member['type'] }}</span>
							@endif
							<span class="name">{{ $member['name'] }}</span>
							<span class="position">{{ $member['position'] }}</span>
							<span class="affiliation">{{ $member['affiliation'] }}</span>
						</button>
					</li>
					@endforeach
				</ul>

				<h2 class="btit mt">INTEGRATED DISCUSSION</h2>
				<ul class="flex group-secondary">
					@foreach($committeeGroups['group4'] as $member)
					<li>
						<button type="button" class="btn-open-modal" aria-haspopup="dialog" aria-controls="modal-steering"
							data-img="{{ $member['img'] }}"
							@if(!empty($member['type'])) data-type="{{ $member['type'] }}" @endif
							data-name="{{ $member['name'] }}"
							data-position="{{ $member['position'] }}"
							data-affiliation="{{ $member['affiliation'] }}"
							data-bio="{{ $member['bio'] }}"
							@if(!empty($member['file'])) data-file="{{ $member['file'] }}" @endif
							data-link="{{ $member['link'] ?? '' }}">
							<span class="imgfit" aria-hidden="true"><img src="{{ $member['img'] }}" alt=""></span>
							@if(!empty($member['type']))
							<span class="type type-{{ \Illuminate\Support\Str::slug($member['type']) }}">{{ $member['type'] }}</span>
							@endif
							<span class="name">{{ $member['name'] }}</span>
							<span class="position">{{ $member['position'] }}</span>
							<span class="affiliation">{{ $member['affiliation'] }}</span>
						</button>
					</li>
					@endforeach
				</ul>
			</div>

			<!-- DAY 2 TAB PANEL (초기 숨김) -->
			<div id="panel-day2" class="con_panel" role="tabpanel" aria-labelledby="tab-day2" hidden>
				<h2 class="btit">SESSION 1</h2>
			</div>
		</div>
	</section>
</div>

<!-- 모달 팝업 -->
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
@endsection

@push('scripts')
<script src="/publishing-original-assets/js/script_tab.js"></script>
<script src="/publishing-original-assets/js/script_speakers.js"></script>
@endpush
