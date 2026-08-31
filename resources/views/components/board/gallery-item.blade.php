@props([
    'url' => '#this',
    'img' => '/images/img_sample_gallery.avif',
    'title' => '',
    'date' => '',
    'datetime' => '',
    'pdf' => '',
    'pdfName' => ''
])

<li>
    <a href="{{ $url }}">
        <span class="imgfit" aria-hidden="true"><img src="{{ $img }}" alt="{{ $title }}"></span>
        <span class="txt">
            <h3 class="tt">{{ $title }}</h3>

            @if($pdf)
                {{-- button 태그 기반 다운로드 --}}
                <button type="button" class="btn btn_pdf_download flex_center" onclick="event.preventDefault(); event.stopPropagation(); const a = document.createElement('a'); a.href = '{{ $pdf }}'; a.download = '{{ $pdfName ?: basename($pdf) }}'; a.click();">
                    <span>PDF 다운로드</span>
                </button>
            @elseif($date)
                {{-- 기존 날짜 표시 --}}
                <time class="date" datetime="{{ $datetime ?: str_replace('.', '-', $date) }}">{{ $date }}</time>
            @endif
        </span>
    </a>
</li>