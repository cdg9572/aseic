@props([
    'items' => [] // DB 데이터 전달 시 사용
])

<ul class="gallery_list">
    @if(count($items) > 0)
        @foreach($items as $item)
            <li>
                <a href="{{ $item['url'] ?? '#this' }}">
                    <span class="imgfit" aria-hidden="true">
                        <img src="{{ $item['img'] ?? '/images/img_sample_gallery.avif' }}" alt="{{ $item['title'] ?? '' }}">
                    </span>
                    <span class="txt">
                        <h3 class="tt">{{ $item['title'] }}</h3>
                        <time class="date" datetime="{{ $item['datetime'] ?? '' }}">{{ $item['date'] }}</time>
                    </span>
                </a>
            </li>
        @endforeach
    @else
        {{-- 수동 작성 혹은 $slot 주입 시 사용 --}}
        {{ $slot }}
    @endif
</ul>