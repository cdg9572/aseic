@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/table.css') !!}
@endsection

@section('content')

<!-- 포럼 핵심 정보 (Hero Section) -->
<section class="theme_info_area" aria-labelledby="archive-theme-overview-heading">
	<div class="inner">
		<h2 id="archive-theme-overview-heading" class="btit">History of Previous Forums</h2>

		<div class="tbl_radius box_tbl">
			<div class="head">
				<div class="date">Date & Venue</div>
				<div class="program">Forum Theme</div>
			</div>
			<div class="body">
				<div class="con">
					<div class="date">November 14–15, 2024 <br/>Ulsan, Republic of Korea</div>
					<div class="program">Global Supply Chains and Climate Change Responses for SMEs in the ASEM Region
						<p>*Held in conjunction with the Korea Technology and Innovation Expo</p>
					</div>
				</div>

				<div class="con">
					<div class="date">November 1, 2023 <br/>Jakarta, Indonesia</div>
					<div class="program">Accelerating the ESG and Carbon-Neutral Transition for SMEs
						<p>*Held in conjunction with the 4th Korea–ASEAN SDGs Business Model Competition</p>
					</div>
				</div>

				<div class="con">
					<div class="date">October 24, 2022 <br/>Incheon, Republic of Korea</div>
					<div class="program">Circular Industry: Innovation and Digitalization of SMEs
						<p>*Held in conjunction with a spin-off program of the ENV Forum</p>
					</div>
				</div>

				<div class="con">
					<div class="date">March 12, 2021 <br/>Seoul, Republic of Korea</div>
					<div class="program">Green and Digital Transformation for the Inclusive Growth of SMEs
						<p>*Organized by ASEIC and hosted by the Green Manufacturing Technology Institute of Korea University and the Institute for Inclusive Society</p>
					</div>
				</div>

				<div class="con">
					<div class="date">December 22, 2020 <br/>Online</div>
					<div class="program">Green and Digital Transformation for SMEs
					</div>
				</div>

				<div class="con">
					<div class="date">October 15–16, 2019 <br/>Seoul, Republic of Korea</div>
					<div class="program">SME Eco-Innovation Capacity-Building Seminar
						<p>*Co-organized with the Korea Small Business Institute Held in conjunction with the ASEAN Startup Policy and Cooperation Workshop</p>
					</div>
				</div>

				<div class="con">
					<div class="date">September 4–5, 2018 <br/>Jakarta, Indonesia</div>
					<div class="program">Strengthening Eco-Innovation among SMEs for Inclusive and Sustainable Growth
						<p>*Co-hosted with Indonesia’s Ministry of Cooperatives and SMEs</p>
					</div>
				</div>

				<div class="con">
					<div class="date">September 20, 2017 <br/>Seoul, Republic of Korea</div>
					<div class="program">Sustainable Consumption and Production for SMEs: Addressing Climate Change through Sustainability
					</div>
				</div>

				<div class="con">
					<div class="date">June 1, 2016 <br/>Seoul, Republic of Korea</div>
					<div class="program">The New Climate Regime and the Utilization of Eco-Innovation by SMEs
					</div>
				</div>

				<div class="con">
					<div class="date">November 10, 2015 <br/>Seoul, Republic of Korea</div>
					<div class="program">Eco-Innovation Practices and Future Challenges for SMEs
						<p>*Co-organized with the Korea Institute of Industrial Technology</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection