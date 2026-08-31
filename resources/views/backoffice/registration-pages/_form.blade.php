@php
    $registrationPage = $registrationPage ?? null;
    $mode = old('participation_mode', $registrationPage?->participation_mode ?? \App\Models\RegistrationPage::MODE_PARTICIPATING);
@endphp

<div class="bo-form-section">
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label for="main_page_id" class="bo-form-label">Main Page 연결</label>
            <div class="bo-form-field">
                <select class="board-form-control @error('main_page_id') is-invalid @enderror" id="main_page_id" name="main_page_id">
                    <option value="">연결하지 않음</option>
                    @foreach ($mainPages as $mainPage)
                        <option value="{{ $mainPage->id }}" @selected((string) old('main_page_id', $selectedMainPageId) === (string) $mainPage->id)>{{ $mainPage->folder_name }} - {{ $mainPage->event_name }}</option>
                    @endforeach
                </select>
                @error('main_page_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="page_title" class="bo-form-label">제목 <span class="required">*</span></label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('page_title') is-invalid @enderror" id="page_title" name="page_title" value="{{ old('page_title', $registrationPage?->page_title) }}" required>
                @error('page_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="subtitle" class="bo-form-label">Sub Title</label>
            <div class="bo-form-field">
                <textarea class="board-form-control board-form-textarea @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" rows="10" data-backoffice-ckeditor data-source-editing="true">{{ old('subtitle', $registrationPage?->subtitle) }}</textarea>
                @error('subtitle')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <span class="bo-form-label">참여 여부 <span class="required">*</span></span>
            <div class="bo-form-field">
                <div class="board-radio-group">
                    <label class="board-radio-item"><input type="radio" name="participation_mode" value="participating" data-registration-mode @checked($mode === 'participating')> 참여</label>
                    <label class="board-radio-item"><input type="radio" name="participation_mode" value="not_participating" data-registration-mode @checked($mode === 'not_participating')> 미참여</label>
                </div>
                @error('participation_mode')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div data-registration-participating @if ($mode !== 'participating') hidden @endif>
            <div class="bo-form-row">
                <label for="period_text" class="bo-form-label">Period <span class="required">*</span></label>
                <div class="bo-form-field">
                    <input type="text" class="board-form-control @error('period_text') is-invalid @enderror" id="period_text" name="period_text" value="{{ old('period_text', $registrationPage?->period_text) }}" maxlength="255">
                    @error('period_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            @foreach ([1, 2, 3] as $step)
                <div class="bo-form-row">
                    <label for="guide_step_{{ $step }}" class="bo-form-label">STEP0{{ $step }}</label>
                    <div class="bo-form-field">
                        <input type="text" class="board-form-control @error('guide_step_'.$step) is-invalid @enderror" id="guide_step_{{ $step }}" name="guide_step_{{ $step }}" value="{{ old('guide_step_'.$step, $registrationPage?->{'guide_step_'.$step}) }}" maxlength="255">
                        @error('guide_step_'.$step)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            @endforeach

            <div class="bo-form-row">
                <span class="bo-form-label">Registration 종료</span>
                <div class="bo-form-field bo-inline-fields">
                    <input type="date" class="board-form-control @error('registration_start_date') is-invalid @enderror" name="registration_start_date" value="{{ old('registration_start_date', $registrationPage?->registration_start_date?->format('Y-m-d')) }}">
                    <span>~</span>
                    <input type="date" class="board-form-control @error('registration_end_date') is-invalid @enderror" name="registration_end_date" value="{{ old('registration_end_date', $registrationPage?->registration_end_date?->format('Y-m-d')) }}">
                    <label class="checkbox-label"><input type="checkbox" name="use_custom_end_text" value="1" data-registration-custom-end @checked((bool) old('use_custom_end_text', $registrationPage?->use_custom_end_text))> 직접입력</label>
                    <input type="text" class="board-form-control @error('registration_end_text') is-invalid @enderror" name="registration_end_text" value="{{ old('registration_end_text', $registrationPage?->registration_end_text) }}" data-registration-end-text placeholder="종료 문구">
                    @error('registration_start_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @error('registration_end_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    @error('registration_end_text')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div data-registration-closed @if ($mode !== 'not_participating') hidden @endif>
            <div class="bo-form-row">
                <label for="closed_notice" class="bo-form-label">미참여 공지</label>
                <div class="bo-form-field">
                    <textarea class="board-form-control board-form-textarea @error('closed_notice') is-invalid @enderror" id="closed_notice" name="closed_notice" rows="10" data-backoffice-ckeditor data-source-editing="true">{{ old('closed_notice', $registrationPage?->closed_notice) }}</textarea>
                    @error('closed_notice')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>
