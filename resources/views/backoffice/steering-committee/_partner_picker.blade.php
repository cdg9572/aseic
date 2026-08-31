@php
    $selectedIds = array_values(array_unique(array_map('intval', (array) old($fieldName, $selectedIds ?? []))));
    $selectedItems = $options->whereIn('id', $selectedIds)->sortBy(fn ($item) => array_search($item->id, $selectedIds, true));
@endphp

<div class="bo-form-row" data-about-picker data-field-name="{{ $fieldName }}">
    <label class="bo-form-label">{{ $label }}</label>
    <div class="bo-form-field bo-about-full-field">
        <button type="button" class="btn btn-secondary btn-sm" data-about-picker-open><i class="fas fa-plus"></i> 추가하기</button>
        <span class="board-form-help bo-about-inline-help">{{ $helpText }}</span>
        <div class="bo-about-selected-items" data-about-picker-selected>
            @foreach ($selectedItems as $item)
                <div class="bo-about-selected-item" data-about-selected-id="{{ $item->id }}">
                    <input type="hidden" name="{{ $fieldName }}[]" value="{{ $item->id }}">
                    <span>{{ $item->full_name }}@if($item->affiliation) · {{ $item->affiliation }}@endif</span>
                    <button type="button" class="bo-about-selected-remove" data-about-selected-remove aria-label="선택 해제"><i class="fas fa-times"></i></button>
                </div>
            @endforeach
        </div>
        @error($fieldName)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

        <div class="bo-about-modal" data-about-picker-modal hidden>
            <div class="bo-about-modal-backdrop" data-about-picker-close></div>
            <div class="bo-about-modal-dialog" role="dialog" aria-modal="true" aria-label="{{ $label }} 선택">
                <div class="bo-about-modal-header"><h4>{{ $label }} 선택</h4><button type="button" class="bo-about-modal-close" data-about-picker-close aria-label="닫기"><i class="fas fa-times"></i></button></div>
                <div class="bo-about-modal-body">
                    <div class="bo-about-picker-search"><label class="filter-label">이름</label><input type="text" class="filter-input" placeholder="이름을 입력하세요" data-about-picker-search></div>
                    <div class="table-responsive"><table class="board-table"><thead><tr><th class="w8">선택</th><th>이름</th><th>소속</th></tr></thead><tbody>
                        @forelse ($options as $item)
                            <tr data-about-picker-row data-search-text="{{ strtolower($item->full_name.' '.$item->affiliation) }}"><td><input type="checkbox" class="form-check-input" value="{{ $item->id }}" data-about-picker-checkbox @checked(in_array($item->id, $selectedIds, true))></td><td data-about-picker-name>{{ $item->full_name }}</td><td>{{ $item->affiliation ?: '-' }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="text-center">등록된 항목이 없습니다.</td></tr>
                        @endforelse
                    </tbody></table></div>
                </div>
                <div class="bo-about-modal-actions"><button type="button" class="btn btn-primary" data-about-picker-apply>선택추가</button><button type="button" class="btn btn-secondary" data-about-picker-close>취소</button></div>
            </div>
        </div>
    </div>
</div>
