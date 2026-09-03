@php
    $mediaContent = $mediaContent ?? null;
@endphp

<div class="bo-form-section"><div class="bo-form-list">
    @if (isset($context['category_group_code']))
        <div class="bo-form-row"><label for="category_id" class="bo-form-label">{{ $context['category_label'] ?? '분류' }} <span class="required">*</span></label><div class="bo-form-field"><select class="board-form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required><option value="">선택해주세요</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', request()->query('category_id', $mediaContent?->category_id)) === (string) $category->id)>{{ $category->name }}</option>@endforeach</select>@if($categories->isEmpty())<small class="bo-password-help">탭 관리의 {{ $context['category_group_name'] }} 그룹에 1차 메뉴를 먼저 등록해주세요.</small>@endif @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
    @endif

    @if ($context['form'] === 'folder')
        <div class="bo-form-row"><label for="page_title" class="bo-form-label">제목 <span class="required">*</span></label><div class="bo-form-field"><input type="text" class="board-form-control @error('page_title') is-invalid @enderror" id="page_title" name="page_title" value="{{ old('page_title', $mediaContent?->page_title) }}" maxlength="255" required>@error('page_title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
    @endif

    @if (in_array($context['form'], ['folder', 'youtube'], true))
        <div class="bo-form-row"><label for="subtitle" class="bo-form-label">Sub Title</label><div class="bo-form-field"><textarea class="board-form-control board-form-textarea @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" rows="10" data-backoffice-ckeditor data-source-editing="true">{{ old('subtitle', $mediaContent?->subtitle) }}</textarea>@error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
    @endif

    @if (in_array($context['form'], ['photo', 'news', 'youtube'], true))
        <div class="bo-form-row"><label for="title" class="bo-form-label">{{ $context['form'] === 'news' ? '뉴스 제목' : '제목' }} <span class="required">*</span></label><div class="bo-form-field"><input type="text" class="board-form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $mediaContent?->title) }}" maxlength="255" required>@error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
    @endif

    @if ($context['form'] === 'news')
        <div class="bo-form-row"><label for="published_date" class="bo-form-label">날짜</label><div class="bo-form-field"><input type="date" class="board-form-control @error('published_date') is-invalid @enderror" id="published_date" name="published_date" value="{{ old('published_date', $mediaContent?->published_date?->format('Y-m-d')) }}">@error('published_date')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
        <div class="bo-form-row"><label for="view_count" class="bo-form-label">조회수</label><div class="bo-form-field"><input type="number" class="board-form-control @error('view_count') is-invalid @enderror" id="view_count" name="view_count" value="{{ old('view_count', $mediaContent?->view_count ?? 0) }}" min="0">@error('view_count')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
        <div class="bo-form-row"><label for="content" class="bo-form-label">내용</label><div class="bo-form-field"><textarea class="board-form-control board-form-textarea @error('content') is-invalid @enderror" id="content" name="content" rows="12" data-backoffice-ckeditor data-source-editing="true">{{ old('content', $mediaContent?->content) }}</textarea>@error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
    @endif

    @if (in_array($context['form'], ['photo', 'news'], true) || $context['type'] === \App\Models\MediaContent::TYPE_PHOTO_FOLDER)
        <div class="bo-form-row"><label for="image" class="bo-form-label">{{ $context['form'] === 'news' ? '사진 첨부' : 'Photo' }} @if($context['form'] === 'photo')<span class="required">*</span>@endif</label><div class="bo-form-field bo-about-full-field">
            <div class="board-file-upload" data-speaker-file-upload><div class="board-file-input-wrapper"><input type="file" class="board-file-input @error('image') is-invalid @enderror" id="image" name="image" accept=".jpg,.jpeg,.png" data-max-size="5242880" data-max-files="1"><div class="board-file-input-content"><i class="fas fa-cloud-upload-alt"></i><span class="board-file-input-text">{{ $context['form'] === 'news' ? '사진을 선택하거나 여기로 드래그하세요' : 'Photo 이미지를 선택하거나 여기로 드래그하세요' }}</span><span class="board-file-input-subtext">JPG, PNG / 1개 / 5MB 이하</span></div></div>
                <input type="hidden" name="remove_image" value="{{ old('remove_image', 0) ? 1 : 0 }}" data-speaker-remove-input>
                @if ($mediaContent?->image_path && !old('remove_image'))<div class="board-existing-files"><div class="board-attachment-list"><div class="board-attachment-item existing-file"><i class="fas fa-image"></i><a href="{{ asset('storage/'.$mediaContent->image_path) }}" target="_blank" rel="noopener" class="board-attachment-name">{{ $mediaContent->image_name ?: basename($mediaContent->image_path) }}</a><button type="button" class="board-attachment-remove" data-speaker-existing-file-remove data-confirm-message="기존 이미지를 삭제하시겠습니까?" aria-label="기존 이미지 삭제"><i class="fas fa-times"></i></button></div></div></div>@endif
                <div class="board-file-preview" data-speaker-file-preview></div>
            </div>@error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div></div>
    @endif

    @if ($context['form'] === 'youtube')
        <div class="bo-form-row"><label for="link" class="bo-form-label">Link <span class="required">*</span></label><div class="bo-form-field"><input type="url" class="board-form-control @error('link') is-invalid @enderror" id="link" name="link" value="{{ old('link', $mediaContent?->link) }}" maxlength="2048" placeholder="https://" required>@error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
    @endif

    <div class="bo-form-row"><label for="is_visible" class="bo-form-label">노출 여부</label><div class="bo-form-field"><select class="board-form-control @error('is_visible') is-invalid @enderror" id="is_visible" name="is_visible"><option value="1" @selected((string) old('is_visible', $mediaContent?->is_visible ?? true) === '1')>보임</option><option value="0" @selected((string) old('is_visible', $mediaContent?->is_visible ?? true) === '0')>숨김</option></select>@error('is_visible')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
</div></div>
