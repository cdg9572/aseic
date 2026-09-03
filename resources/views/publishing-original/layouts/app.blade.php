<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
		$menus = [
			'01' => [
				'name' => 'ABOUT',
				'route' => 'about.forum',
				'path' => 'about',
				'sub' => [
					'01' => ['name' => 'About the Forum', 'route' => 'about.forum', 'url' => '/about/forum'],
					'02' => ['name' => 'Steering Committee', 'route' => 'about.committee', 'url' => '/about/committee'],
					'03' => ['name' => 'Co-organizers', 'route' => 'about.organizers', 'url' => '/about/organizers'],
					'04' => ['name' => 'Venue', 'route' => 'about.venue', 'url' => '/about/venue'],
				]
			],
			'02' => [
				'name' => 'PROGRAMME',
				'route' => 'programme.theme',
				'path' => 'programme',
				'sub' => [
					'01' => ['name' => 'Theme', 'route' => 'programme.theme', 'url' => '/programme/theme'],
					'02' => ['name' => 'Programme', 'route' => 'programme.list', 'url' => '/programme'],
					'03' => ['name' => 'Speakers', 'route' => 'programme.speakers', 'url' => '/programme/speakers'],
					'04' => ['name' => 'Programme Book', 'route' => 'programme.book', 'url' => '/programme/book'],
				]
			],
			'03' => [
				'name' => 'ARCHIVE',
				'route' => 'archive.theme',
				'path' => 'archive',
				'sub' => [
					'01' => ['name' => '2025 Forum', 'route' => 'archive.theme', 'url' => '/archive/theme'],
					'02' => ['name' => 'Past Forums (2015~2024)', 'route' => 'archive.past', 'url' => '/archive/past'],
				],
				'aside_sub' => [
					'01' => ['name' => 'Theme', 'route' => 'archive.theme', 'url' => '/archive/theme'],
					'02' => ['name' => 'Programme', 'route' => 'archive.programme', 'url' => '/archive/programme'],
					'03' => ['name' => 'Speakers', 'route' => 'archive.speakers', 'url' => '/archive/speakers'],
				]
			],
			'04' => [
				'name' => 'MEDIA',
				'route' => 'media.gallery',
				'path' => 'media',
				'sub' => [
					'01' => ['name' => 'Photo Gallery', 'route' => 'media.gallery', 'url' => '/media/gallery'],
					'02' => ['name' => 'News Clippings', 'route' => 'media.news', 'url' => '/media/news'],
					'03' => ['name' => 'Youtube Channel', 'route' => 'media.youtube', 'url' => '/media/youtube'],
				]
			],
			'05' => [
				'name' => 'REGISTRATION',
				'route' => 'registration.index',
				'path' => 'registration',
				'sub' => []
			],
			'06' => [
				'name' => 'ANNOUNCEMENTS',
				'route' => 'announcements.index',
				'path' => 'announcements',
				'sub' => []
			],
		];
		$currentGNum = $gNum ?? '';
		$currentSNum = $sNum ?? '';
		$currentDNum = $dNum ?? '';
		$siteUrl = config('app.url');
		$siteName = 'ASEIC | Asia-Europe Young Leaders Forum';

		$groupData = $menus[$currentGNum] ?? null;
		$subData = $groupData['sub'][$currentSNum] ?? null;

		$pageName = $subData['name'] ?? $groupData['name'] ?? null;

		$jsonName = ($currentGNum === 'main' || !$pageName) ? $siteName : $siteName . ' | ' . $pageName;
		$jsonDesc = $__env->yieldContent('description') ?? '';
		$jsonKeywords = $__env->yieldContent('keywords') ?? 'ASEIC';
	@endphp

    @yield('meta_tags')
    @php
		$pageTitle = $currentGNum === 'main' ? $jsonName : trim($__env->yieldContent('title'));
		$ogTitle = $currentGNum === 'main' ? 'ASEIC' : 'ASEIC | ' . trim($__env->yieldContent('title'));
	@endphp

	<title>{{ $pageTitle }}</title>
	<meta name="title" content="{{ $pageTitle }}" />
	<meta name="subject" content="{{ $jsonName }}" />
	<meta name="description" content="@yield('description', 'ASEM SMEs Eco Innovation Center')">
	<meta name="author" content="ASEIC">
	<meta name="copyright" content="ASEIC" />
	<meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:subject" content="ASEIC" />
    <meta property="og:description" content="@yield('description', 'ASEM SMEs Eco Innovation Center')">
    <meta property="og:image" content="/publishing-original-assets/images/og_image.avif">
    <link rel="icon" href="/publishing-original-assets/images/favicon.svg" type="image/x-icon"/>
    <meta property="og:site_name" content="ASEIC">
    <link rel="canonical" href="{{ $siteUrl }}" />
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $siteUrl }}">

    <script type="application/ld+json">
	{
		"@@context": "https://schema.org",
		"@@type": "WebPage",
		"name": "{{ $jsonName }}",
		"description": "{{ $jsonDesc }}",
		"keywords": "{{ $jsonKeywords }}",
		"url": "{{ url()->current() }}",
		"inLanguage": "ko-KR",
		"publisher": {
			"@@type": "Organization",
			"name": "{{ $jsonName }}",
			"url": "{{ $siteUrl }}",
			"logo": {
				"@@type": "ImageObject",
				"url": "{{ $siteUrl }}/images/logo.png"
			}
		},
		"breadcrumb": {
			"@@type": "BreadcrumbList",
			"itemListElement": [
				{ "@@type": "ListItem", "position": 1, "name": "홈", "item": "{{ $siteUrl }}" },
				{ "@@type": "ListItem", "position": 2, "name": "{{ $gName ?? ($menus[$currentGNum]['name'] ?? '') }}", "item": "{{ $siteUrl }}/{{ request()->segment(1) }}" }
				@if($currentGNum && ($currentGNum === '01' || $currentGNum === '02' || ($page ?? '') === 'view'))
				,{ "@@type": "ListItem", "position": 3, "name": "{{ $sName ?? ($menus[$currentGNum]['sub'][$currentSNum]['name'] ?? '') }}", "item": "{{ strtok(url()->current(), '?') }}" }
				@endif
			]
		},
		"navigation": {
			"@@type": "SiteNavigationElement",
			"@@id": "{{ $siteUrl }}/#main-navi",
			"name": "ASEIC 메인 네비게이션",
			"itemListElement": [
				@php $navPos = 1; @endphp
				@foreach($menus as $gMenu)
					@foreach($gMenu['sub'] as $sMenu)
						{ "@@type": "ListItem", "position": {{ $navPos++ }}, "name": "{{ $sMenu['name'] }}", "url": "{{ $siteUrl }}{{ $sMenu['url'] }}" }@if(!$loop->parent->last || !$loop->last),@endif
					@endforeach
				@endforeach
			]
		}
		@yield('sga_plus', '')
	}
	</script>
    
    <style>
    @font-face{font-family:'Pretendard';font-style:normal;font-weight:300 900;src:url('/publishing-original-assets/css/font/PretendardVariable.woff2') format('woff2');font-display:swap;}
    body{font-family:'Pretendard','Dotum',Arial,sans-serif;}
    </style>
    
    <link rel="preload" href="/publishing-original-assets/css/font/PretendardVariable.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="icon" href="{{ asset('publishing-original-assets/images/favicon.svg') }}" type="image/x-icon"/>

    <link rel="stylesheet" href="{{ asset('publishing-original-assets/css/default.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('publishing-original-assets/css/layout.css') }}" media="all">

    @if($currentGNum === 'main')
        <link rel="stylesheet" href="{{ asset('publishing-original-assets/css/main.css') }}" media="all">
    @elseif(isset($menus[$currentGNum]))
        <link rel="stylesheet" href="{{ asset('publishing-original-assets/css/'.$menus[$currentGNum]['path'].'.css') }}" media="all">
    @elseif($currentGNum === 'total_search')
        <link rel="stylesheet" href="{{ asset('publishing-original-assets/css/board.css') }}" media="all">
    @endif

    @yield('styles')
    
    <script src="/publishing-original-assets/js/com.js"></script>
</head>
<body>
    <div class="blind_link"><a href="#mainContent">Skip to content</a></div>

    <header @class(['header', 'main' => $currentGNum === 'main'])>
		<div class="inner">
			<div class="logo">
				<a href="{{ route('home') }}" class="flex flex-center">
					<img src="{{ $currentGNum === 'main' ? asset('publishing-original-assets/images/logo_main.svg') : asset('publishing-original-assets/images/logo.svg') }}" alt="" aria-hidden="true">
					<p class="sound_only">ASEIC Asia-Europe Young Leaders Forum</p>
				</a>
			</div>
			<ul class="menus">
				@foreach($menus as $gKey => $gMenu)
					@php
						// 하위 메뉴(sub)가 존재하는지 여부 확인
						$hasSub = !empty($gMenu['sub']) && count($gMenu['sub']) > 0;
					@endphp
					<li class="{{ $currentGNum == $gKey ? 'on' : '' }} {{ !$hasSub ? 'nosnb' : '' }}">
						<a href="{{ route($gMenu['route']) }}" id="main-menu-{{ $gKey }}" aria-haspopup="{{ $hasSub ? 'true' : 'false' }}" aria-expanded="{{ $currentGNum == $gKey ? 'true' : 'false' }}" @if($currentGNum == $gKey) aria-current="page" @endif>{{ $gMenu['name'] }}</a>
						@if($hasSub)
							<ul class="snb" aria-labelledby="main-menu-{{ $gKey }}">
								@foreach($gMenu['sub'] as $sKey => $sMenu)
									<li @if($currentGNum == $gKey && $currentSNum == $sKey) class="on" aria-current="page" @endif>
										<a href="{{ route($sMenu['route']) }}">{{ $sMenu['name'] }}</a>
									</li>
								@endforeach
							</ul>
						@endif
					</li>
				@endforeach
			</ul>
		</div>
		<button type="button" class="mo_vw btn_menu">Open Menu
			<span class="t"></span>
			<span class="m"></span>
			<span class="b"></span>
		</button>
	</header>
    
    <main class="container_wrap g{{ $currentGNum }} s{{ $currentSNum }} d{{ $currentDNum }}{{ $currentGNum !== 'main' ? ' sub_wrap' : '' }} @yield('main_class')" id="mainContent">
        @if($currentGNum && $currentGNum !== 'main')
        
        @php
            $hasSubH1 = $__env->yieldContent('has_h1') ?? false;
            $hideLocation = $__env->yieldContent('hide_location') ?? false;
            
            // ARCHIVE(03) 중 Past Forums(sNum=02)에서는 aside 미노출, 그 외(sNum=01)에는 aside_sub 노출
            if ($currentGNum === '03') {
                $asideMenus = ($currentSNum === '02') ? [] : ($menus['03']['aside_sub'] ?? []);
            } else {
                $asideMenus = $menus[$currentGNum]['sub'] ?? [];
            }
                
            // tit_pagename 및 breadcrumb 2depth 영역에 표시할 현재 snb 1차 소메뉴 이름
            $displayPageTitle = $subData['name'] ?? $sName ?? $gName ?? ($menus[$currentGNum]['name'] ?? '');
        @endphp

        <div class="svisual g{{ $currentGNum }}">
            <div class="inner">
				<div class="breadcrumb">
					<div class="home"></div>
					<span>{{ $gName ?? ($menus[$currentGNum]['name'] ?? '') }}</span>
					@if(isset($subData['name']) || !empty($sName))
					<span>{{ $displayPageTitle }}</span>
					@endif
				</div>
				
				@if($hasSubH1)
                    <div class="tit_pagename">{{ $displayPageTitle }}</div>
                @else
                    <h1 class="tit_pagename">{{ $displayPageTitle }}</h1>
                @endif
				@switch($currentGNum)
					@case('01')
						@switch($currentSNum)
							@case('01')<p>Learn about the Forum, <br/>Its Overview, Background, and Objectives.</p>@break
							@case('02')<p>A Steering Committee, comprising representatives from participating countries <br/>and organizations, has been established to provide strategic guidance <br/>and support the successful organization and operation of the Forum.</p>@break
							@case('03')<p>Discover the organizations supporting the Forum<br/>and their roles in planning and delivering the event.</p>@break
							@case('04')<p>Find information about the Forum venue,<br/>including its location, facilities, and access details.</p>@break
						@endswitch
					@break
					@case('02')<p>Explore the forum programme featuring insightful sessions, <br/>global perspectives, and practical solutions for sustainable SME growth.</p>@break
					@case('03')<p>Explore the key themes, programs, speakers, <br/>and highlights of the 2025 Forum.</p>@break
					@case('04')
						@switch($currentSNum)
							@case('01')<p>Explore memorable moments and highlights <br/>from the forum through our official photo collection.</p>@break
							@case('02')<p>Discover the latest media coverage, <br/>articles, and news related to the forum.</p>@break
							@case('03')<p>Watch keynote speeches, forum sessions, interviews, <br/>and featured videos on our official YouTube channel.</p>@break
						@endswitch
					@break
					@case('05')<p>Join the Forum by completing the online registration form.<br/>Click the button below to proceed to the Google Form.</p>@break
					@case('06')<p>Find the latest updates on the Forum, including event schedules, <br/>registration, and programme changes.<br/>Please review the announcements regularly to stay informed.</p>@break
					@default
				@endswitch
            </div>
			@if(!empty($asideMenus))
			<div class="aside g{{ $currentGNum }}">
				<ul class="inner">
					<li class="g">
						<button type="button" class="btn">{{ $gName ?? ($menus[$currentGNum]['name'] ?? '') }}</button>
						<ul class="list">
							@foreach($menus as $gKey => $gMenu)
								<li class="{{ $currentGNum === $gKey ? 'on' : '' }}">
									<a href="{{ route($gMenu['route']) }}">{{ $gMenu['name'] }}</a>
								</li>
							@endforeach
						</ul>
					</li>
					
					@if(isset($menus[$currentGNum]['sub']) && count($menus[$currentGNum]['sub']) > 0)
					<li class="s">
						<button type="button" class="btn">{{ $displayPageTitle }}</button>
						<ul class="list">
							@foreach($asideMenus as $sKey => $sMenu)
								@php
									// ARCHIVE(03) 메뉴는 dNum 기준으로 'on' 표기, 그 외 일반 메뉴는 sNum 기준
									$isAsideActive = ($currentGNum === '03') ? ($currentDNum === $sKey) : ($currentSNum === $sKey);
								@endphp
								<li class="{{ $isAsideActive ? 'on' : '' }}">
									<a href="{{ route($sMenu['route']) }}">{{ $sMenu['name'] }}</a>
								</li>
							@endforeach
						</ul>
					</li>
					@endif
				</ul>
			</div>
			@endif
        </div>
        
        @if(isset($menus[$currentGNum]['sub'][$currentSNum]['tabs']) && ($page ?? '') !== 'view' && !request()->routeIs('*.view') && !request()->routeIs('*-view'))
        <div class="inner">
            @if($hasSubH1)
                <div class="ctit">{{ $sName }}</div>
            @else
                <h2 class="ctit">{{ $sName }}</h2>
            @endif

            <ul class="tabs">
                @foreach($menus[$currentGNum]['sub'][$currentSNum]['tabs'] as $dKey => $tab)
                    <li class="{{ $currentDNum === $dKey ? 'on' : '' }}">
                        <a href="{{ route($tab['route']) }}">{{ $tab['name'] }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
        @endif
    
        <div class="container">@yield('content')</div>
    </main>
    
    @if($currentGNum !== '99')
    @yield('popups')
    @stack('scripts')
    
	<div id="modal-privacy-policy" class="popup popup_terms" role="dialog" aria-modal="true" aria-labelledby="modal-title-privacy" hidden>
		<div class="dm" data-close="true"></div>
		<div class="inbox" tabindex="-1">
			<h3 id="modal-title-privacy" class="tit">Privacy policy</h3>
			<button type="button" class="btn_close" aria-label="Close modal">&times;</button>
			<div class="terms_text">
				@include('publishing-original.terms.txt_privacy-policy')
			</div>
		</div>
	</div>

	<div id="modal-email" class="popup popup_terms" role="dialog" aria-modal="true" aria-labelledby="modal-title-email" hidden>
		<div class="dm" data-close="true"></div>
		<div class="inbox" tabindex="-1">
			<h3 id="modal-title-email" class="tit">Prohibition of Unauthorized E-mail Collection</h3>
			<button type="button" class="btn_close" aria-label="Close modal">&times;</button>
			<div class="terms_text">
				@include('publishing-original.terms.txt_email')
			</div>
		</div>
	</div>

	<footer class="footer">
		<div class="quick_menu">
			<ul>
				<li class="i1"><a href="{{ route('about.forum') }}">FORUM</a></li>
				<li class="i2"><a href="{{ route('archive.theme') }}">ARCHIVE</a></li>
				<li class="i3"><a href="{{ route('registration.index') }}">REGISTER</a></li>
				<li class="i4"><a href="{{ route('media.youtube') }}">YOUTUBE</a></li>
			</ul>
			<button type="button" class="gotop">Top</button>
		</div>
		<div class="inner flex justify-between flex-wrap">
			<div class="info flex colm">
				<strong>2026 Global Eco-Innovation Forum</strong>
				<p class="copy">Copyright © ASEM SMEs Eco-Innovation Center. All Rights Reserved.</p>
			</div>
			<ul class="links flex">
				<li><button type="button" class="btn_popup btn_open" data-target="modal-privacy-policy">Privacy policy</button></li>
				<li><button type="button" class="btn_popup btn_open" data-target="modal-email">Prohibition of Unauthorized E-mail Collection</button></li>
			</ul>
		</div>
	</footer>
    @endif
</body>
</html>
