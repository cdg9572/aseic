@props([
    'total' => 0,
    'searchOptions' => [
        'title' => '제목',
        'content' => '내용',
    ]
])

<div class="board_top flex_between">
    <div class="total">Total<strong class="c_iden">{{ number_format($total) }}</strong></div>
    <form action="{{ url()->current() }}" method="GET" class="search_wrap">
        <select name="search_type" class="text">
            @foreach($searchOptions as $value => $label)
                <option value="{{ $value }}" {{ request('search_type') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <div class="search_area">
            <input type="text" name="keyword" class="text" value="{{ request('keyword') }}" placeholder="검색어를 입력해주세요.">
            <button type="submit" class="btn">검색</button>
        </div>
    </form>
</div>