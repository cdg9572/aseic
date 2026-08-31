@props([
    'num' => '',
    'state' => '',       // CSS 클래스용 (예: 'ing', 'end', 'wait')
    'stateText' => '',   // 노출 텍스트 (예: '진행중', '완료')
    'title' => '',
    'url' => '#this',
    'writer' => '',
    'date' => '',
    'hit' => '0'
])

<tr>
    <td class="num">{{ $num }}</td>
    <td class="state">
        @if($stateText)
            <div class="state {{ $state }}">{{ $stateText }}</div>
        @endif
    </td>
    <td class="tit">
        {{-- 제목은 접근성 및 기능성에 맞게 a 태그 적용 --}}
        <a href="{{ $url }}">{{ $title }}</a>
    </td>
    <td class="writer">{{ $writer }}</td>
    <td class="date">{{ $date }}</td>
    <td class="hit">{{ $hit }}</td>
</tr>