<div class="board-form-row">
    <div class="board-form-col board-form-col-12">
        <div class="board-form-group">
            <label for="main_page_id" class="board-form-label">Main Page 연결</label>
            <select class="board-form-control @error('main_page_id') is-invalid @enderror" id="main_page_id" name="main_page_id">
                <option value="">연결하지 않음</option>
                @foreach ($mainPages as $mainPageOption)
                    <option value="{{ $mainPageOption->id }}" @selected((string) old('main_page_id', $selectedMainPageId ?? '') === (string) $mainPageOption->id)>
                        {{ $mainPageOption->folder_name }} - {{ $mainPageOption->event_name }}
                    </option>
                @endforeach
            </select>
            <small class="board-form-text">선택하면 해당 Main Page에 이 {{ $assetLabel }}만 노출됩니다.</small>
            @error('main_page_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
