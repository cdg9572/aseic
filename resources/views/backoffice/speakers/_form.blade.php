@php
    $speaker = $speaker ?? null;
    $speakerAttachments = $speaker?->attachment_files ?? [];
    $removedAttachmentIndexes = array_map('intval', (array) old('remove_attachments', []));
    $visibleAttachmentCount = count(array_filter(
        array_keys($speakerAttachments),
        fn ($index) => !in_array($index, $removedAttachmentIndexes, true)
    ));
@endphp

<div class="bo-form-section">
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label for="first_name" class="bo-form-label">First name <span class="required">*</span></label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $speaker?->first_name) }}" maxlength="100" required>
                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="last_name" class="bo-form-label">Last name <span class="required">*</span></label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $speaker?->last_name) }}" maxlength="100" required>
                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="position" class="bo-form-label">Position</label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $speaker?->position) }}" maxlength="255">
                @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="affiliation" class="bo-form-label">Affiliation</label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('affiliation') is-invalid @enderror" id="affiliation" name="affiliation" value="{{ old('affiliation', $speaker?->affiliation) }}" maxlength="255">
                @error('affiliation')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="presentation_subject" class="bo-form-label">Presentation Subject</label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('presentation_subject') is-invalid @enderror" id="presentation_subject" name="presentation_subject" value="{{ old('presentation_subject', $speaker?->presentation_subject) }}" maxlength="500">
                @error('presentation_subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="bo-form-row">
            <label for="profile_image" class="bo-form-label">프로필</label>
            <div class="bo-form-field">
                <div class="board-file-upload" data-speaker-file-upload>
                    <div class="board-file-input-wrapper">
                        <input
                            type="file"
                            class="board-file-input @error('profile_image') is-invalid @enderror"
                            id="profile_image"
                            name="profile_image"
                            accept=".jpg,.jpeg,.png"
                            data-max-size="5242880"
                            data-max-files="1"
                        >
                        <div class="board-file-input-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span class="board-file-input-text">프로필 이미지를 선택하거나 여기로 드래그하세요</span>
                            <span class="board-file-input-subtext">JPG, PNG / 1개 / 5MB 이하</span>
                        </div>
                    </div>
                    @if ($speaker?->profile_image)
                        <input type="hidden" name="remove_profile_image" value="{{ old('remove_profile_image', 0) ? 1 : 0 }}" data-speaker-remove-input>
                        @unless (old('remove_profile_image'))
                            <div class="board-existing-files">
                                <div class="board-attachment-list">
                                    <div class="board-attachment-item existing-file">
                                        <i class="fas fa-image"></i>
                                        <a href="{{ asset('storage/' . $speaker->profile_image) }}" target="_blank" rel="noopener" class="board-attachment-name">{{ $speaker->profile_image_name ?: basename($speaker->profile_image) }}</a>
                                        <button type="button" class="board-attachment-remove" data-speaker-existing-file-remove data-confirm-message="기존 프로필 이미지를 삭제하시겠습니까?" aria-label="기존 프로필 이미지 삭제">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endunless
                    @endif
                    <div class="board-file-preview" data-speaker-file-preview></div>
                </div>
                @error('profile_image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="role" class="bo-form-label">역할 <span class="required">*</span></label>
            <div class="bo-form-field">
                <select class="board-form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                    @foreach ($roleOptions as $value => $label)
                        <option value="{{ $value }}" @selected(old('role', $speaker?->role ?? 'speaker') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="is_active" class="bo-form-label">Speaker 노출여부</label>
            <div class="bo-form-field">
                <select class="board-form-control @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                    <option value="1" @selected((string) old('is_active', $speaker?->is_active ?? true) === '1')>보임</option>
                    <option value="0" @selected((string) old('is_active', $speaker?->is_active ?? true) === '0')>숨김</option>
                </select>
                @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="is_image_visible" class="bo-form-label">Image 노출여부</label>
            <div class="bo-form-field">
                <select class="board-form-control @error('is_image_visible') is-invalid @enderror" id="is_image_visible" name="is_image_visible">
                    <option value="1" @selected((string) old('is_image_visible', $speaker?->is_image_visible ?? false) === '1')>보임</option>
                    <option value="0" @selected((string) old('is_image_visible', $speaker?->is_image_visible ?? false) === '0')>숨김</option>
                </select>
                @error('is_image_visible')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="content" class="bo-form-label">상세 내용</label>
            <div class="bo-form-field">
                <textarea class="board-form-control board-form-textarea @error('content') is-invalid @enderror" id="content" name="content" rows="15" data-backoffice-ckeditor data-source-editing="true">{{ old('content', $speaker?->content) }}</textarea>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="attachments" class="bo-form-label">첨부파일</label>
            <div class="bo-form-field">
                <div class="board-file-upload" data-speaker-file-upload data-existing-count="{{ $visibleAttachmentCount }}">
                    <div class="board-file-input-wrapper">
                        <input
                            type="file"
                            class="board-file-input @error('attachments') is-invalid @enderror"
                            id="attachments"
                            name="attachments[]"
                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.hwp,.zip"
                            data-max-size="20971520"
                            data-max-files="5"
                            multiple
                        >
                        <div class="board-file-input-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                            <span class="board-file-input-subtext">최대 5개, 각 파일 20MB 이하</span>
                        </div>
                    </div>
                    <div data-speaker-removed-attachment-inputs>
                        @foreach ($removedAttachmentIndexes as $removedIndex)
                            <input type="hidden" name="remove_attachments[]" value="{{ $removedIndex }}">
                        @endforeach
                    </div>
                    @if ($speakerAttachments && $visibleAttachmentCount > 0)
                        <div class="board-existing-files">
                            <div class="board-attachment-list">
                                @foreach ($speakerAttachments as $index => $attachment)
                                    @continue(in_array($index, $removedAttachmentIndexes, true))
                                    <div class="board-attachment-item existing-file" data-speaker-existing-attachment>
                                        <i class="fas fa-file"></i>
                                        <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" rel="noopener" class="board-attachment-name">{{ $attachment['name'] }}</a>
                                        @if (!empty($attachment['size']))
                                            <span class="board-attachment-size">({{ number_format($attachment['size'] / 1024 / 1024, 2) }}MB)</span>
                                        @endif
                                        <button type="button" class="board-attachment-remove" data-speaker-existing-attachment-remove data-index="{{ $index }}" aria-label="기존 첨부파일 삭제">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="board-file-preview" data-speaker-file-preview></div>
                </div>
                @error('attachments')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @error('attachments.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>
