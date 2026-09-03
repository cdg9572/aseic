@extends('layouts.app')

@php
$committeeGroups = [
	'group1' => [
		[
			'img' => '/images/img_steering_committee01.avif',
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
		],
		[
			'img' => '/images/img_steering_committee02.avif',
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
			'img' => '/images/img_steering_committee03.avif',
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
			'img' => '/images/img_steering_committee04.avif',
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
			'img' => '/images/img_steering_committee01.avif',
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
			'img' => '/images/img_steering_committee06.avif',
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
			'img' => '/images/img_steering_committee07.avif',
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

if (! ($aboutPage ?? null) && ($mainPage?->folder_name ?? null) !== 'publishing-original') {
	$committeeGroups = ['group1' => [], 'group2' => []];
}

if (($aboutPage ?? null) instanceof \App\Models\AboutPage) {
	$toMember = static function (\App\Models\HomepagePartner $partner): array {
		$image = null;
		if ($partner->is_image_visible && $partner->profile_image) {
			$image = str_starts_with($partner->profile_image, '/')
				? asset(ltrim($partner->profile_image, '/'))
				: asset('storage/'.$partner->profile_image);
		}

		return [
			'img' => $image,
			'name' => $partner->full_name,
			'position' => $partner->position,
			'affiliation' => $partner->affiliation,
			'bio' => $partner->content,
			'link' => $partner->linkedin_url,
		];
	};

	$committeeGroups = [
		'group1' => $aboutPage->steeringOrganizedPartners->where('is_active', true)->map($toMember)->values()->all(),
		'group2' => $aboutPage->steeringPartnershipPartners->where('is_active', true)->map($toMember)->values()->all(),
	];
}
@endphp

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/about.css') !!}
{!! \App\Helpers\CssHelper::minTag('/css/speakers.css') !!}
@endsection

@section('content')
<div class="inner">
	<section class="scon steering_committee_area" aria-labelledby="steering-committee-heading">
		<h2 id="steering-committee-heading" class="sound_only">Steering Committee List</h2>

		@if($committeeGroups['group1'] !== [])
		<ul class="flex">
			@foreach($committeeGroups['group1'] as $member)
			<li>
				<button type="button" class="btn-open-modal" aria-haspopup="dialog" aria-controls="modal-steering"
					data-img="{{ $member['img'] }}"
					data-name="{{ $member['name'] }}"
					data-position="{{ $member['position'] }}"
					data-affiliation="{{ $member['affiliation'] }}"
					data-bio="{{ $member['bio'] }}"
					data-link="{{ $member['link'] ?? '' }}">
					<span class="imgfit" aria-hidden="true">@if($member['img'])<img src="{{ $member['img'] }}" alt="">@endif</span>
					<span class="name">{{ $member['name'] }}</span>
					<span class="position">{{ $member['position'] }}</span>
					<span class="affiliation">{{ $member['affiliation'] }}</span>
				</button>
			</li>
			@endforeach
		</ul>
		@endif

		@if($committeeGroups['group2'] !== [])
		<ul class="flex group-secondary">
			@foreach($committeeGroups['group2'] as $member)
			<li>
				<button type="button" class="btn-open-modal" aria-haspopup="dialog" aria-controls="modal-steering"
					data-img="{{ $member['img'] }}"
					data-name="{{ $member['name'] }}"
					data-position="{{ $member['position'] }}"
					data-affiliation="{{ $member['affiliation'] }}"
					data-bio="{{ $member['bio'] }}"
					data-link="{{ $member['link'] ?? '' }}">
					<span class="imgfit" aria-hidden="true">@if($member['img'])<img src="{{ $member['img'] }}" alt="">@endif</span>
					<span class="name">{{ $member['name'] }}</span>
					<span class="position">{{ $member['position'] }}</span>
					<span class="affiliation">{{ $member['affiliation'] }}</span>
				</button>
			</li>
			@endforeach
		</ul>
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
					<p id="name" class="name"></p>
					<div class="info">
						<p id="position" class="position"></p>
						<p id="affiliation" class="affiliation"></p>
						<a href="#this" id="modal-linkedin" target="_blank" rel="noopener noreferrer" class="link" aria-label="LinkedIn profile (opens in a new window)">LinkedIn</a>
					</div>
				</div>
			</div>
			<div id="modal-bio" class="modal-bio"></div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="/js/script_speakers.js"></script>
@endpush
