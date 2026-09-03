@extends('layouts.app')

@section('styles')
{!! \App\Helpers\CssHelper::minTag('/css/about.css') !!}
@endsection

@section('content')
@php
	$organizers = [
		['logo' => '/images/img_organizers01.avif', 'name' => 'Asia-Europe Foundation', 'description' => 'An intergovernmental organization that strengthens mutual understanding, cooperation, and people-to-people connections between Asia and Europe.', 'url' => 'https://asef.org/'],
		['logo' => '/images/img_organizers02.avif', 'name' => 'Hanns Seidel Foundation in ASEAN', 'description' => 'A regional organization supporting democracy, peace, good governance, sustainable development, and institutional cooperation across Southeast Asia.', 'url' => 'https://southeastasia.hss.de/'],
		['logo' => '/images/img_organizers03.avif', 'name' => 'TH!NK GLOBAL Sustainability Network', 'description' => 'A global network that connects organizations and institutions to promote dialogue, knowledge exchange, and collaborative solutions in climate change, energy, environment, and sustainable development.', 'url' => 'https://www.thinkglobalnetwork.org/'],
	];

	if (! ($aboutPage ?? null) && ($mainPage?->folder_name ?? null) !== 'publishing-original') {
		$organizers = [];
	}

	if (($aboutPage ?? null) instanceof \App\Models\AboutPage) {
		$organizers = $aboutPage->coOrganizerItems->map(static function (\App\Models\AboutCoOrganizerItem $item): array {
			$logo = $item->logo_path
				? (str_starts_with($item->logo_path, '/') ? asset(ltrim($item->logo_path, '/')) : asset('storage/'.$item->logo_path))
				: null;

			return ['logo' => $logo, 'name' => $item->name, 'description' => $item->description, 'url' => $item->url];
		})->all();
	}
@endphp
@if($organizers !== [])
<div class="inner">
	<section class="scon organizers_area" aria-labelledby="organizers-heading">
		<h2 id="organizers-heading" class="stit">Co-organizers</h2>
		<ul>
			@foreach($organizers as $organizer)
			<li>
				<div class="logo" aria-hidden="true">@if($organizer['logo'])<img src="{{ $organizer['logo'] }}" alt="">@endif</div>
				<div class="txt">
					@if(filled($organizer['name']))<h3 class="name">{{ $organizer['name'] }}</h3>@endif
					@if(filled(strip_tags((string) $organizer['description'])))
						@if($organizer['description'] === strip_tags($organizer['description']))<p>{{ $organizer['description'] }}</p>@else{!! $organizer['description'] !!}@endif
					@endif
					@if(filled($organizer['url']))<a href="{{ $organizer['url'] }}" class="btn_link btn_bg_gra_i" target="_blank" rel="noopener noreferrer" aria-label="{{ $organizer['name'] }} Website (opens in a new window)"><i aria-hidden="true"></i>Visit Website</a>@endif
				</div>
			</li>
			@endforeach
		</ul>
	</section>
</div>
@endif

@endsection
