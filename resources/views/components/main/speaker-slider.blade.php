@php
    $speakers = [
        ['image' => '/images/img_speaker01.avif', 'position' => 'STARTUP', 'session' => 'SESSION 2', 'name' => 'MS. Sia LEE', 'organisation' => 'GREENGRIM inc, republic of korea'],
        ['image' => '/images/img_speaker01.avif', 'position' => 'STARTUP', 'session' => 'SESSION 2', 'name' => 'MS. Nur AISYAH', 'organisation' => 'Universiti Malaya, Malaysia'],
        ['image' => '/images/img_speaker01.avif', 'position' => 'POLICY', 'session' => 'SESSION 1', 'name' => 'MR. Daniel Wong', 'organisation' => 'ASEIC, Singapore'],
        ['image' => '/images/img_speaker01.avif', 'position' => 'INNOVATION', 'session' => 'SESSION 3', 'name' => 'MS. Elena Müller', 'organisation' => 'GreenTech Europe, Germany'],
        ['image' => '/images/img_speaker01.avif', 'position' => 'STARTUP', 'session' => 'SESSION 2', 'name' => 'MS. Hana Park', 'organisation' => 'EcoLab, Republic of Korea'],
        ['image' => '/images/img_speaker01.avif', 'position' => 'POLICY', 'session' => 'SESSION 1', 'name' => 'MR. Luca Rossi', 'organisation' => 'Green Growth Network, Italy'],
    ];
@endphp

<section class="mcon main_speakers">
    <div class="inner">
        <div class="mtit">
            <h2>SPEAKERS</h2>
            <a href="{{ route('programme.speakers') }}" class="btn_link btn_bg_gra">More Info</a>
        </div>
        <div class="slide_speaker relative">
            <div class="swiper speaker_swiper">
                <div class="swiper-wrapper">
                    @foreach($speakers as $speaker)
                        <div class="swiper-slide">
                            <a href="{{ route('programme.speakers') }}" class="box">
                                <div class="photo imgfit" aria-hidden="true"><img src="{{ $speaker['image'] }}" alt=""></div>
                                <div class="info">{{ $speaker['position'] }}</div>
                                <div class="btm">
                                    <div class="session">{{ $speaker['session'] }}</div>
                                    <div class="name">{{ $speaker['name'] }}</div>
                                    <p>{{ $speaker['organisation'] }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="navi">
                <div class="speaker-pagination"></div>
                <button type="button" class="arrow speaker-prev" aria-label="Prev speaker">이전</button>
                <button type="button" class="arrow speaker-next" aria-label="Next speaker">다음</button>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.speaker_swiper', {
        loop: true,
        spaceBetween: 10,
        slidesPerView: 'auto',
        observer: true,
        observeParents: true,
        autoplay: { delay: 4500, disableOnInteraction: false },
        navigation: { nextEl: '.speaker-next', prevEl: '.speaker-prev' },
        pagination: { el: '.speaker-pagination', clickable: true },
		breakpoints: {
            768: {
                spaceBetween: 16,
            },
            1024: {
                spaceBetween: 24,
            }
        },
        on: {
            slideChangeTransitionEnd: function (swiper) {
                swiper.update()
            }
        }
    })
})
</script>
@endpush