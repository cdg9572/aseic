@php
    $selectedIds = array_values(array_unique(array_map('intval', (array) old($fieldName, $selectedIds ?? []))));
    $selectedItems = $speakers->whereIn('id', $selectedIds)->sortBy(fn ($speaker) => array_search($speaker->id, $selectedIds, true));
    $errorKey = preg_replace('/\[([^\]]+)\]/', '.$1', $fieldName);
@endphp

<div class="bo-form-row" data-about-picker data-field-name="{{ $fieldName }}" @if($preventDuplicateAcrossPickers ?? false) data-prevent-cross-picker-duplicates @endif>
    <label class="bo-form-label">Speakers</label>
    <div class="bo-form-field bo-about-full-field">
        <button type="button" class="btn btn-secondary btn-sm" data-about-picker-open><i class="fas fa-plus"></i> 추가하기</button>
        <span class="board-form-help bo-about-inline-help">노출할 Speaker를 선택해주세요.</span>
        <div class="bo-about-selected-items" data-about-picker-selected>
            @foreach ($selectedItems as $speaker)
                <div class="bo-about-selected-item" data-about-selected-id="{{ $speaker->id }}">
                    <input type="hidden" name="{{ $fieldName }}[]" value="{{ $speaker->id }}">
                    <span>{{ $speaker->full_name }}</span>
                    <button type="button" class="bo-about-selected-remove" data-about-selected-remove aria-label="선택 해제"><i class="fas fa-times"></i></button>
                </div>
            @endforeach
        </div>
        @error($errorKey)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        @error($errorKey.'.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

        <div class="bo-about-modal" data-about-picker-modal hidden>
            <div class="bo-about-modal-backdrop" data-about-picker-close></div>
            <div class="bo-about-modal-dialog" role="dialog" aria-modal="true" aria-label="Speakers 선택">
                <div class="bo-about-modal-header"><h4>Speakers 선택</h4><button type="button" class="bo-about-modal-close" data-about-picker-close aria-label="닫기"><i class="fas fa-times"></i></button></div>
                <div class="bo-about-modal-body">
                    <div class="bo-about-picker-search"><label class="filter-label">이름</label><input type="text" class="filter-input" placeholder="이름을 입력하세요" data-about-picker-search></div>
                    <div class="board-alert board-alert-danger" data-about-picker-error hidden></div>
                    <div class="table-responsive"><table class="board-table"><thead><tr><th class="w8">선택</th><th>이름</th></tr></thead><tbody>
                        @forelse ($speakers as $speaker)
                            <tr data-about-picker-row data-search-text="{{ strtolower($speaker->full_name) }}"><td><input type="checkbox" class="form-check-input" value="{{ $speaker->id }}" data-about-picker-checkbox @checked(in_array($speaker->id, $selectedIds, true))></td><td data-about-picker-name>{{ $speaker->full_name }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-center">등록된 Speaker가 없습니다.</td></tr>
                        @endforelse
                    </tbody></table></div>
                </div>
                <div class="bo-about-modal-actions"><button type="button" class="btn btn-primary" data-about-picker-apply>선택추가</button><button type="button" class="btn btn-secondary" data-about-picker-close>취소</button></div>
            </div>
        </div>
    </div>
</div>
