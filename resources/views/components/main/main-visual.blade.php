@props(['banners', 'mainPage'])

@php

    $themes = [
        ['icon' => 'icon_main01.svg', 'title' => 'Global Cooperation', 'description' => 'Strengthen global networks and cooperation for sustainable development.'],
        ['icon' => 'icon_main02.svg', 'title' => 'Knowledge Sharing', 'description' => 'Share green policies, technologies, and best practices to develop practical solutions.'],
        ['icon' => 'icon_main03.svg', 'title' => 'Sustainable Innovation', 'description' => 'Promote technologies and business models that balance sustainability and growth.'],
        ['icon' => 'icon_main04.svg', 'title' => 'Inclusive Growth', 'description' => 'Support MSMEs and startups in accessing green transition opportunities.'],
        ['icon' => 'icon_main05.svg', 'title' => 'Future Partnerships', 'description' => 'Build lasting partnerships for future collaboration and joint initiatives.'],
    ];
@endphp

<section class="mvisual">
    <h2 class="sound_only">Main Banner</h2>
    @if ($banners->isNotEmpty())
    <div class="main_slide relative">
        <div class="swiper main_visual_swiper">
            <div class="swiper-wrapper">
                @foreach($banners as $banner)
                    @php
                        $desktopImage = $banner->desktop_image
                            ? (str_starts_with($banner->desktop_image, '/') ? asset(ltrim($banner->desktop_image, '/')) : asset('storage/'.$banner->desktop_image))
                            : null;
                        $mobileImage = $banner->mobile_image
                            ? (str_starts_with($banner->mobile_image, '/') ? asset(ltrim($banner->mobile_image, '/')) : asset('storage/'.$banner->mobile_image))
                            : null;
                    @endphp
                    <div class="swiper-slide">
                        <div class="box">
                            @if ($desktopImage || $mobileImage)
                                <div class="bg imgfit" aria-hidden="true">
                                    <picture>
                                        @if ($mobileImage)
                                            <source media="(max-width: 768px)" srcset="{{ $mobileImage }}">
                                        @endif
                                        <img src="{{ $desktopImage ?: $mobileImage }}" alt="">
                                    </picture>
                                </div>
                            @endif
                            <div class="flex inner">
                                <div class="txt">
                                    <div class="tit">{!! nl2br(e($banner->main_text ?: $mainPage->event_name ?: $banner->title)) !!}</div>
                                    @if ($banner->sub_text)
                                        <p>{{ $banner->sub_text }}</p>
                                    @endif
                                    @if ($mainPage->event_date_display)
                                        <ul class="info">
                                            <li>{{ $mainPage->event_date_display }}</li>
                                        </ul>
                                    @endif
                                    @if ($banner->url || $banner->video_url)
                                        <a href="{{ $banner->url ?: $banner->video_url }}" target="{{ $banner->url_target }}" class="btn_link">View More</a>
                                    @endif
                                </div>
                                <ul class="icons">
                                    @foreach($themes as $theme)
                                        <li>
                                            <i aria-hidden="true"><img src="{{ asset('images/' . $theme['icon']) }}" alt=""></i>
                                            <strong>{{ $theme['title'] }}</strong>
                                            <p>{{ $theme['description'] }}</p>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="navi flex">
            <div class="visual-pagination"></div>
            <button type="button" class="arrow visual-prev" aria-label="Previous banner">이전</button>
            <button type="button" class="arrow visual-next" aria-label="Next banner">다음</button>
            <button type="button" class="visual-toggle pause" id="visualToggle" aria-label="Pause banner autoplay">정지</button>
        </div>
    </div>
    @endif
    <div class="main_links">
        <ul class="inner">
            <li class="i1"><a href="{{ route('programme.theme') }}">Theme</a></li>
            <li class="i2"><a href="{{ route('programme.list') }}">Programme</a></li>
            <li class="i3"><a href="{{ route('media.gallery') }}">Gallery</a></li>
            <li class="i4"><a href="{{ route('registration.index') }}">Register</a></li>
        </ul>
    </div>
</section>

@push('scripts')
@if ($banners->isNotEmpty())
<script>
document.addEventListener('DOMContentLoaded', function () {
    var visualSwiper = new Swiper('.main_visual_swiper', {
        loop: {{ $banners->count() > 1 ? 'true' : 'false' }},
        autoplay: { delay: 5000, disableOnInteraction: false },
        navigation: { nextEl: '.visual-next', prevEl: '.visual-prev' },
        pagination: { el: '.visual-pagination', clickable: true }
    })
    var toggleBtn = document.getElementById('visualToggle')
    var isPlaying = true
    toggleBtn.addEventListener('click', function () {
        if (isPlaying) {
            visualSwiper.autoplay.stop()
            toggleBtn.classList.remove('pause')
            toggleBtn.classList.add('play')
            toggleBtn.textContent = '재생'
            toggleBtn.setAttribute('aria-label', 'Play banner autoplay')
        } else {
            visualSwiper.autoplay.start()
            toggleBtn.classList.remove('play')
            toggleBtn.classList.add('pause')
            toggleBtn.textContent = '정지'
            toggleBtn.setAttribute('aria-label', 'Pause banner autoplay')
        }
        isPlaying = !isPlaying
    })
})
</script>
@endif
@endpush
