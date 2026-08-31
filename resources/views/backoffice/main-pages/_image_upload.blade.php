@php
    $multiple = $multiple ?? false;
    $maxFiles = $maxFiles ?? 1;
    $existingFiles = $existingFiles ?? [];
    $removedIndexes = array_map('intval', (array) ($removedIndexes ?? []));
    $singleRemoved = (bool) ($singleRemoved ?? false);
    $visibleExistingCount = $multiple
        ? count(array_filter(array_keys($existingFiles), fn ($index) => !in_array($index, $removedIndexes, true)))
        : (($existingFiles && !$singleRemoved) ? 1 : 0);
@endphp

<div class="bo-form-row">
    <label for="{{ $field }}" class="bo-form-label">{{ $label }}</label>
    <div class="bo-form-field bo-main-page-full-field">
        <div class="board-file-upload" data-main-page-file-upload data-existing-count="{{ $visibleExistingCount }}">
            <div class="board-file-input-wrapper">
                <input
                    type="file"
                    class="board-file-input @if($errors->has($field) || $errors->has($field.'.*')) is-invalid @endif"
                    id="{{ $field }}"
                    name="{{ $inputName }}"
                    accept=".jpg,.jpeg,.png"
                    data-max-size="5242880"
                    data-max-files="{{ $maxFiles }}"
                    @if($multiple) multiple @endif
                >
                <div class="board-file-input-content">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span class="board-file-input-text">{{ $uploadText }}</span>
                    <span class="board-file-input-subtext">JPG, PNG / 최대 {{ $maxFiles }}개 / 각 5MB 이하</span>
                </div>
            </div>

            <div data-main-page-removed-inputs>
                @if ($multiple)
                    @foreach ($removedIndexes as $removedIndex)
                        <input type="hidden" name="{{ $removeInputName }}[]" value="{{ $removedIndex }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $removeInputName }}" value="{{ $singleRemoved ? 1 : 0 }}" data-main-page-single-remove-input>
                @endif
            </div>

            @if ($visibleExistingCount > 0)
                <div class="board-existing-files">
                    <div class="board-attachment-list">
                        @foreach ($existingFiles as $index => $file)
                            @continue($multiple && in_array($index, $removedIndexes, true))
                            @continue(!$multiple && $singleRemoved)
                            <div class="board-attachment-item existing-file" data-main-page-existing-file>
                                <i class="fas fa-image"></i>
                                <a href="{{ asset('storage/'.$file['path']) }}" target="_blank" rel="noopener" class="board-attachment-name">{{ $file['name'] }}</a>
                                <button
                                    type="button"
                                    class="board-attachment-remove"
                                    data-main-page-existing-remove
                                    data-remove-mode="{{ $multiple ? 'multiple' : 'single' }}"
                                    data-index="{{ $index }}"
                                    data-remove-name="{{ $removeInputName }}"
                                    aria-label="기존 이미지 삭제"
                                >
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @break(!$multiple)
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="board-file-preview" data-main-page-file-preview></div>
        </div>
        @if ($errors->has($field))
            <div class="invalid-feedback d-block">{{ $errors->first($field) }}</div>
        @endif
        @if ($errors->has($field.'.*'))
            <div class="invalid-feedback d-block">{{ $errors->first($field.'.*') }}</div>
        @endif
    </div>
</div>
