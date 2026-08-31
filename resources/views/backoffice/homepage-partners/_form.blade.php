@php
    $partner = $partner ?? null;
@endphp

<div class="bo-form-section">
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label for="first_name" class="bo-form-label">First name <span class="required">*</span></label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $partner?->first_name) }}" maxlength="100" required>
                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="bo-form-row">
            <label for="last_name" class="bo-form-label">Last name <span class="required">*</span></label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $partner?->last_name) }}" maxlength="100" required>
                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="bo-form-row">
            <label for="position" class="bo-form-label">Position</label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $partner?->position) }}" maxlength="255">
                @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="bo-form-row">
            <label for="affiliation" class="bo-form-label">Affiliation</label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('affiliation') is-invalid @enderror" id="affiliation" name="affiliation" value="{{ old('affiliation', $partner?->affiliation) }}" maxlength="255">
                @error('affiliation')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="bo-form-row">
            <label for="linkedin_url" class="bo-form-label">LinkedIn link</label>
            <div class="bo-form-field">
                <input type="url" class="board-form-control @error('linkedin_url') is-invalid @enderror" id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $partner?->linkedin_url) }}" maxlength="2048" placeholder="https://www.linkedin.com/in/...">
                @error('linkedin_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="bo-form-row">
            <label for="profile_image" class="bo-form-label">프로필</label>
            <div class="bo-form-field">
                <div class="board-file-upload" data-partner-file-upload>
                    <div class="board-file-input-wrapper">
                        <input type="file" class="board-file-input @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image" accept=".jpg,.jpeg,.png" data-max-size="5242880">
                        <div class="board-file-input-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span class="board-file-input-text">프로필 이미지를 선택하거나 여기로 드래그하세요</span>
                            <span class="board-file-input-subtext">JPG, PNG / 1개 / 5MB 이하</span>
                        </div>
                    </div>
                    @if ($partner?->profile_image)
                        <input type="hidden" name="remove_profile_image" value="{{ old('remove_profile_image', 0) ? 1 : 0 }}" data-partner-remove-input>
                        @unless (old('remove_profile_image'))
                            <div class="board-existing-files">
                                <div class="board-attachment-list">
                                    <div class="board-attachment-item existing-file">
                                        <i class="fas fa-image"></i>
                                        <a href="{{ asset('storage/' . $partner->profile_image) }}" target="_blank" rel="noopener" class="board-attachment-name">{{ $partner->profile_image_name ?: basename($partner->profile_image) }}</a>
                                        <button type="button" class="board-attachment-remove" data-partner-existing-file-remove aria-label="기존 프로필 이미지 삭제"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                        @endunless
                    @endif
                    <div class="board-file-preview" data-partner-file-preview></div>
                </div>
                @error('profile_image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="bo-form-row">
            <label for="is_active" class="bo-form-label">{{ $context['entity_name'] }} 노출여부</label>
            <div class="bo-form-field">
                <select class="board-form-control @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                    <option value="1" @selected((string) old('is_active', $partner?->is_active ?? true) === '1')>보임</option>
                    <option value="0" @selected((string) old('is_active', $partner?->is_active ?? true) === '0')>숨김</option>
                </select>
                @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="bo-form-row">
            <label for="is_image_visible" class="bo-form-label">Image 노출여부</label>
            <div class="bo-form-field">
                <select class="board-form-control @error('is_image_visible') is-invalid @enderror" id="is_image_visible" name="is_image_visible">
                    <option value="1" @selected((string) old('is_image_visible', $partner?->is_image_visible ?? false) === '1')>보임</option>
                    <option value="0" @selected((string) old('is_image_visible', $partner?->is_image_visible ?? false) === '0')>숨김</option>
                </select>
                @error('is_image_visible')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="bo-form-row">
            <label for="content" class="bo-form-label">상세 내용</label>
            <div class="bo-form-field">
                <textarea class="board-form-control board-form-textarea @error('content') is-invalid @enderror" id="content" name="content" rows="15" data-backoffice-ckeditor data-source-editing="true">{{ old('content', $partner?->content) }}</textarea>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>
