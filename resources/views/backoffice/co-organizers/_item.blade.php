@php
    $existingItem = $existingItem ?? null;
    $item = $item ?? [];
@endphp
<div class="bo-repeat-item" data-co-organizer-item>
    @if($existingItem)<input type="hidden" name="items[{{ $index }}][id]" value="{{ $existingItem->id }}">@endif
    <div class="bo-form-row"><label for="item_{{ $index }}_logo" class="bo-form-label">공동 주관사 로고</label><div class="bo-form-field bo-repeat-full-field">
        <div class="board-file-upload" data-about-file-upload>
            <div class="board-file-input-wrapper"><input type="file" class="board-file-input @error('items.'.$index.'.logo') is-invalid @enderror" id="item_{{ $index }}_logo" name="items[{{ $index }}][logo]" accept=".jpg,.jpeg,.png" data-max-size="5242880"><div class="board-file-input-content"><i class="fas fa-cloud-upload-alt"></i><span class="board-file-input-text">로고 이미지를 선택하거나 여기로 드래그하세요</span><span class="board-file-input-subtext">JPG, PNG / 1개 / 5MB 이하</span></div></div>
            @if($existingItem?->logo_path && !old('items.'.$index.'.remove_logo'))
                <input type="hidden" name="items[{{ $index }}][remove_logo]" value="0" data-about-remove-logo-input>
                <div class="board-existing-files"><div class="board-attachment-list"><div class="board-attachment-item existing-file"><i class="fas fa-image"></i><a href="{{ asset('storage/'.$existingItem->logo_path) }}" target="_blank" rel="noopener" class="board-attachment-name">{{ $existingItem->logo_name ?: basename($existingItem->logo_path) }}</a><button type="button" class="board-attachment-remove" data-about-existing-logo-remove aria-label="기존 로고 삭제"><i class="fas fa-times"></i></button></div></div></div>
            @endif
            <div class="board-file-preview" data-about-file-preview></div>
        </div>@error('items.'.$index.'.logo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div></div>
    <div class="bo-form-row"><label for="item_{{ $index }}_name" class="bo-form-label">공동 주관사 이름</label><div class="bo-form-field"><input type="text" class="board-form-control @error('items.'.$index.'.name') is-invalid @enderror" id="item_{{ $index }}_name" name="items[{{ $index }}][name]" value="{{ $item['name'] ?? $existingItem?->name }}" maxlength="255">@error('items.'.$index.'.name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
    <div class="bo-form-row"><label for="item_{{ $index }}_description" class="bo-form-label">공동 주관사 설명</label><div class="bo-form-field"><textarea class="board-form-control board-form-textarea @error('items.'.$index.'.description') is-invalid @enderror" id="item_{{ $index }}_description" name="items[{{ $index }}][description]" rows="8" data-backoffice-ckeditor data-source-editing="true">{{ $item['description'] ?? $existingItem?->description }}</textarea>@error('items.'.$index.'.description')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
    <div class="bo-form-row"><label for="item_{{ $index }}_url" class="bo-form-label">링크</label><div class="bo-form-field"><input type="url" class="board-form-control @error('items.'.$index.'.url') is-invalid @enderror" id="item_{{ $index }}_url" name="items[{{ $index }}][url]" value="{{ $item['url'] ?? $existingItem?->url }}" maxlength="2048" placeholder="https://">@error('items.'.$index.'.url')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
</div>
