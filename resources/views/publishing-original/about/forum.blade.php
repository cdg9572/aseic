@extends('publishing-original.layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('publishing-original-assets/css/about.css') }}" media="all">
@endsection

@section('content')
<div class="inner">
	<section class="scon overview_area" aria-labelledby="overview-heading">
		<h2 id="overview-heading" class="stit">Overview</h2>
		<div class="inbox flex">
			<div class="txt">Since 2015, the Global Eco-Innovation Forum has served as the flagship annual platform <br class="pc_vw">
			of ASEM SMEs Eco-Innovation Center (ASEIC), <br class="pc_vw">
			with the continued support of the Ministry of SMEs and Startups (MSS) of the Republic of Korea (ROK).<br/>
			It stands as one of the very few dedicated Asia-Europe forums <br class="pc_vw">
			advancing environmentally sustainable and innovation-driven growth for MSMEs and start-ups.<br/>
			<br/>
			This year, the Forum will be co-organized with the Hanns Seidel Foundation (HSF) and the Asia-Europe <br class="pc_vw">
			Foundation (ASEF), further strengthening its institutional partnership framework across Asia and Europe. The Forum will be held in Jakarta, Indonesia, where ASEIC operates <br class="pc_vw">
			its Green Innovation Cooperation Center (GICC), reinforcing Indonesia's role as a strategic regional gateway connecting the two regions</div>
			<ul class="flex" aria-label="Key Statistics">
				<li class="i1"><strong>10</strong><p>Forums Since 2015</p></li>
				<li class="i2"><strong>1500+</strong><p>Participants</p></li>
				<li class="i3"><strong>40</strong><p>Countries</p></li>
				<li class="i4"><strong>300+</strong><p>Organizations</p></li>
			</ul>
		</div>
	</section>
	
	<section class="scon background_area" aria-labelledby="background-heading">
		<h2 id="background-heading" class="stit">Background</h2>
		<ol class="flex">
			<li class="i1"><span class="num" aria-hidden="true">01</span><h3 class="tit">Global Challenges</h3><p>Geopolitical tensions, climate change, and supply chain disruptions, along with tightening sustainability regulations and rapid advances in AI and digital transformation, are creating unprecedented uncertainty.</p></li>
			<li class="i2"><span class="num" aria-hidden="true">02</span><h3 class="tit">Impact on MSMEs <br class="pc_vw">and Start-ups</h3><p>These shifts place <br class="pc_vw">disproportionate pressure <br class="pc_vw">on smaller enterprises, <br class="pc_vw">whose financial and operational resilience is often limited.</p></li>
			<li class="i3"><span class="num" aria-hidden="true">03</span><h3 class="tit">New Opportunities</h3><p>Adapting to these shifts <br class="pc_vw">can be challenging <br class="pc_vw">but also opens doors for <br class="pc_vw">eco-innovation, competitiveness, and integration into <br class="pc_vw">global value chains.</p></li>
			<li class="i4"><span class="num" aria-hidden="true">04</span><h3 class="tit">Our Response</h3><p>The Forum provides <br class="pc_vw">a platform to exchange experiences, showcase solutions, and build partnerships <br class="pc_vw">for sustainable growth.</p></li>
		</ol>
	</section>
	
	<section class="scon objectives_area" aria-labelledby="objectives-heading">
		<h2 id="objectives-heading" class="stit">Objectives</h2>
		<ul class="flex">
			<li class="i1"><h3 class="tit">Knowledge Exchange</h3><p>To explore how MSMEs and start-ups across Asia <br class="pc_vw">and Europe can navigate evolving sustainability <br class="pc_vw">expectations and unlock opportunities <br class="pc_vw">in emerging green value chains through practical <br class="pc_vw">knowledge exchange and cross-regional dialogue.</p></li>
			<li class="i2"><h3 class="tit">Case Studies &amp; Solutions</h3><p>To share practical case studies and <br class="pc_vw">experiences of how MSMEs and <br class="pc_vw">start-ups in Asia and Europe <br class="pc_vw">are leveraging eco-innovation and AI solutions <br class="pc_vw">to access global markets and achieve growth.</p></li>
			<li class="i3"><h3 class="tit">Partnerships for Action</h3><p>To advance early-stage Working Group <br class="pc_vw">recommendations into structured cooperation <br class="pc_vw">proposals with potential relevance for future <br class="pc_vw">consideration by the ASEM Senior Officials' <br class="pc_vw">Meeting to foster long-term policy alignment <br class="pc_vw">and regional impact.</p></li>
		</ul>
	</section>
</div>

@endsection
