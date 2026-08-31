<div class="bo-form-row">
    <label for="main_page_id" class="bo-form-label">Main Page 연결</label>
    <div class="bo-form-field">
        <select class="board-form-control @error('main_page_id') is-invalid @enderror" id="main_page_id" name="main_page_id">
            <option value="">연결하지 않음</option>
            @foreach ($mainPages as $mainPageOption)
                <option value="{{ $mainPageOption->id }}" @selected((string) old('main_page_id', $selectedMainPageId ?? '') === (string) $mainPageOption->id)>
                    {{ $mainPageOption->folder_name }} - {{ $mainPageOption->event_name }}
                </option>
            @endforeach
        </select>
        <small class="bo-password-help">선택하면 해당 Main Page의 {{ $context['entity_name'] }} 항목에 자동으로 연결됩니다.</small>
        @error('main_page_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="bo-form-row">
    <label for="page_title" class="bo-form-label">제목 <span class="required">*</span></label>
    <div class="bo-form-field">
        <input type="text" class="board-form-control @error('page_title') is-invalid @enderror" id="page_title" name="page_title" value="{{ old('page_title', $programmePage?->page_title) }}" maxlength="255" required>
        @error('page_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
