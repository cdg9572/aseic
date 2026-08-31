@php
    $isTemplate = $isTemplate ?? false;
    $fieldPrefix = 'books.'.$index;
    $inputPrefix = 'books['.$index.']';
    $bookId = $book['id'] ?? null;
    $removeFile = (bool) ($book['remove_file'] ?? false);
    $filePath = $book['file_path'] ?? null;
    $fileName = $book['file_name'] ?? null;
    $fileSize = $book['file_size'] ?? null;
@endphp

<div class="bo-repeat-item" data-programme-book-item>
    @if ($bookId)
        <input type="hidden" name="{{ $inputPrefix }}[id]" value="{{ $bookId }}">
    @endif

    <div class="bo-form-row">
        <label for="books_{{ $index }}_title" class="bo-form-label">제목</label>
        <div class="bo-form-field">
            <input type="text" class="board-form-control {{ ! $isTemplate && $errors->has($fieldPrefix.'.title') ? 'is-invalid' : '' }}" id="books_{{ $index }}_title" name="{{ $inputPrefix }}[title]" value="{{ $book['title'] ?? '' }}" maxlength="255">
            @if (! $isTemplate && $errors->has($fieldPrefix.'.title'))<div class="invalid-feedback">{{ $errors->first($fieldPrefix.'.title') }}</div>@endif
        </div>
    </div>

    <div class="bo-form-row">
        <label for="books_{{ $index }}_file" class="bo-form-label">파일</label>
        <div class="bo-form-field bo-repeat-full-field">
            <div class="board-file-upload" data-programme-book-upload>
                <div class="board-file-input-wrapper">
                    <input type="file" class="board-file-input {{ ! $isTemplate && $errors->has($fieldPrefix.'.file') ? 'is-invalid' : '' }}" id="books_{{ $index }}_file" name="{{ $inputPrefix }}[file]" accept=".pdf,.doc,.docx,.ppt,.pptx,.hwp,.zip" data-max-size="20971520">
                    <div class="board-file-input-content">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span class="board-file-input-text">파일을 선택하거나 여기로 드래그하세요</span>
                        <span class="board-file-input-subtext">1개 / 20MB 이하</span>
                    </div>
                </div>

                <input type="hidden" name="{{ $inputPrefix }}[remove_file]" value="{{ $removeFile ? 1 : 0 }}" data-programme-book-remove-file>

                @if ($filePath && ! $removeFile)
                    <div class="board-existing-files">
                        <div class="board-attachment-list">
                            <div class="board-attachment-item existing-file">
                                <i class="fas fa-file"></i>
                                <a href="{{ asset('storage/'.$filePath) }}" target="_blank" rel="noopener" class="board-attachment-name">{{ $fileName ?: basename($filePath) }}</a>
                                @if ($fileSize)<span class="board-attachment-size">({{ number_format($fileSize / 1024 / 1024, 2) }}MB)</span>@endif
                                <button type="button" class="board-attachment-remove" data-programme-book-existing-remove aria-label="기존 파일 삭제"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="board-file-preview" data-programme-book-preview></div>
            </div>
            @if (! $isTemplate && $errors->has($fieldPrefix.'.file'))<div class="invalid-feedback d-block">{{ $errors->first($fieldPrefix.'.file') }}</div>@endif
        </div>
    </div>

    <div class="bo-form-row">
        <label for="books_{{ $index }}_link" class="bo-form-label">Link</label>
        <div class="bo-form-field">
            <input type="url" class="board-form-control {{ ! $isTemplate && $errors->has($fieldPrefix.'.link') ? 'is-invalid' : '' }}" id="books_{{ $index }}_link" name="{{ $inputPrefix }}[link]" value="{{ $book['link'] ?? '' }}" maxlength="2048" placeholder="https://">
            @if (! $isTemplate && $errors->has($fieldPrefix.'.link'))<div class="invalid-feedback">{{ $errors->first($fieldPrefix.'.link') }}</div>@endif
        </div>
    </div>
</div>
