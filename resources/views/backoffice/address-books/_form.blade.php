@php($addressBook = $addressBook ?? null)

<div class="bo-form-section">
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label for="name" class="bo-form-label">주소록명 <span class="required">*</span></label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $addressBook?->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="import_file" class="bo-form-label">엑셀 등록</label>
            <div class="bo-form-field bo-about-full-field">
                <div class="board-file-upload" data-speaker-file-upload>
                    <div class="board-file-input-wrapper">
                        <input type="file" class="board-file-input" id="import_file" name="import_file" accept=".csv,.xlsx" data-max-size="10485760" data-max-files="1">
                        <div class="board-file-input-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span class="board-file-input-text">CSV 또는 XLSX 파일을 선택하거나 여기로 드래그하세요</span>
                            <span class="board-file-input-subtext">1개 / 10MB 이하</span>
                        </div>
                    </div>
                    <div class="board-file-preview" data-speaker-file-preview></div>
                </div>
                @error('import_file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <span class="bo-form-label">엑셀 샘플</span>
            <div class="bo-form-field">
                <a href="{{ route('backoffice.address-books.sample') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-download"></i> 샘플 다운로드
                </a>
            </div>
        </div>
    </div>
</div>
