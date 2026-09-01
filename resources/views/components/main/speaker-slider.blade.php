@props(['speakers'])

@if ($speakers->isNotEmpty())
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
                        @php
                            $profileImage = $speaker->is_image_visible && $speaker->profile_image
                                ? (str_starts_with($speaker->profile_image, '/') ? asset(ltrim($speaker->profile_image, '/')) : asset('storage/'.$speaker->profile_image))
                                : null;
                            $roleLabel = \App\Models\Speaker::roleOptions()[$speaker->role] ?? strtoupper((string) $speaker->role);
                        @endphp
                        <div class="swiper-slide">
                            <a href="{{ route('programme.speakers') }}" class="box">
                                <div class="photo imgfit">
                                    @if ($profileImage)
                                        <img src="{{ $profileImage }}" alt="{{ $speaker->full_name }}">
                                    @endif
                                </div>
                                <div class="info">{{ $roleLabel }}</div>
                                <div class="btm">
                                    @if ($speaker->presentation_subject)
                                        <div class="session">{{ $speaker->presentation_subject }}</div>
                                    @endif
                                    <div class="name">{{ $speaker->full_name }}</div>
                                    @if ($speaker->position || $speaker->affiliation)
                                        <p>{{ collect([$speaker->position, $speaker->affiliation])->filter()->implode(' · ') }}</p>
                                    @endif
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
        loop: {{ $speakers->count() > 1 ? 'true' : 'false' }},
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
@endif
