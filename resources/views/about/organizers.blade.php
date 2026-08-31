@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/about.css') !!}
@endsection

@section('content')
<div class="inner">
	<section class="scon organizers_area" aria-labelledby="organizers-heading">
		<h2 id="organizers-heading" class="stit">Co-organizers</h2>
		<ul>
			<li>
				<div class="logo" aria-hidden="true"><img src="/images/img_organizers01.avif" alt=""></div>
				<div class="txt">
					<h3 class="name">Asia-Europe Foundation</h3>
					<p>An intergovernmental organization that strengthens mutual understanding, cooperation, <br class="pc_vw">and people-to-people connections between Asia and Europe.</p>
					<a href="https://asef.org/" class="btn_link btn_bg_gra_i" target="_blank" rel="noopener noreferrer" aria-label="Asia-Europe Foundation Website (opens in a new window)"><i aria-hidden="true"></i>Visit Website</a>
				</div>
			</li>
			<li>
				<div class="logo" aria-hidden="true"><img src="/images/img_organizers02.avif" alt=""></div>
				<div class="txt">
					<h3 class="name">Hanns Seidel Foundation in ASEAN</h3>
					<p>A regional organization supporting democracy, peace, good governance, sustainable development, <br class="pc_vw">and institutional cooperation across Southeast Asia.</p>
					<a href="https://southeastasia.hss.de/" class="btn_link btn_bg_gra_i" target="_blank" rel="noopener noreferrer" aria-label="Hanns Seidel Foundation in ASEAN Website (opens in a new window)"><i aria-hidden="true"></i>Visit Website</a>
				</div>
			</li>
			<li>
				<div class="logo" aria-hidden="true"><img src="/images/img_organizers03.avif" alt=""></div>
				<div class="txt">
					<h3 class="name">TH!NK GLOBAL Sustainability Network</h3>
					<p>A global network that connects organizations and institutions to promote dialogue, knowledge exchange, <br class="pc_vw">and collaborative solutions in climate change, energy, environment, and sustainable development.</p>
					<a href="https://www.thinkglobalnetwork.org/" class="btn_link btn_bg_gra_i" target="_blank" rel="noopener noreferrer" aria-label="TH!NK GLOBAL Sustainability Network Website (opens in a new window)"><i aria-hidden="true"></i>Visit Website</a>
				</div>
			</li>
		</ul>
	</section>
</div>

@endsection