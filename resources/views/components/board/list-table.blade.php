@props([
    'caption' => '게시물 목록',
    'cols' => [] // 예: ['num' => 'NO.', 'state' => '상태', 'tit' => '제목', 'writer' => '작성자', 'date' => '등록일', 'hit' => '조회수']
])

<div class="board_basic">
    <table>
        <caption>{{ $caption }}</caption>

        {{-- cols 배열이 넘어온 경우 colgroup 및 thead 자동 생성 --}}
        @if(!empty($cols))
            <colgroup>
                @foreach($cols as $class => $name)
                    <col class="{{ $class }}"/>
                @endforeach
            </colgroup>
            <thead>
                <tr>
                    @foreach($cols as $class => $name)
                        <th scope="col" class="{{ $class }}">{{ $name }}</th>
                    @endforeach
                </tr>
            </thead>
        @elseif(isset($head))
            {{-- 자율적으로 커스텀할 경우 --}}
            <thead>
                <tr>
                    {{ $head }}
                </tr>
            </thead>
        @endif

        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>