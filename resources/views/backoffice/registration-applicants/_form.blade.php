@php($registrationApplicant = $registrationApplicant ?? null)

<div class="bo-form-section">
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label for="registration_page_id" class="bo-form-label">Registration <span class="required">*</span></label>
            <div class="bo-form-field">
                <select class="board-form-control @error('registration_page_id') is-invalid @enderror" id="registration_page_id" name="registration_page_id" required>
                    <option value="">선택해주세요.</option>
                    @foreach ($registrationPages as $page)
                        <option value="{{ $page->id }}" @selected((string) old('registration_page_id', $registrationApplicant?->registration_page_id) === (string) $page->id)>{{ $page->page_title }}</option>
                    @endforeach
                </select>
                @error('registration_page_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        @foreach (['name' => '이름', 'email' => '이메일', 'phone' => '연락처', 'country' => '국가', 'affiliation' => '소속', 'position' => '직책'] as $field => $label)
            <div class="bo-form-row">
                <label for="{{ $field }}" class="bo-form-label">{{ $label }} @if (in_array($field, ['name', 'email']))<span class="required">*</span>@endif</label>
                <div class="bo-form-field">
                    <input type="{{ $field === 'email' ? 'email' : 'text' }}" class="board-form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $registrationApplicant?->{$field}) }}" @required(in_array($field, ['name', 'email']))>
                    @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        @endforeach

        <div class="bo-form-row">
            <label for="participation_type" class="bo-form-label">참여 형태</label>
            <div class="bo-form-field">
                <select class="board-form-control @error('participation_type') is-invalid @enderror" id="participation_type" name="participation_type">
                    <option value="">선택</option>
                    <option value="offline" @selected(old('participation_type', $registrationApplicant?->participation_type) === 'offline')>오프라인</option>
                    <option value="online" @selected(old('participation_type', $registrationApplicant?->participation_type) === 'online')>온라인</option>
                </select>
                @error('participation_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="status" class="bo-form-label">상태 <span class="required">*</span></label>
            <div class="bo-form-field">
                <select class="board-form-control @error('status') is-invalid @enderror" id="status" name="status">
                    <option value="pending" @selected(old('status', $registrationApplicant?->status ?? 'pending') === 'pending')>접수</option>
                    <option value="approved" @selected(old('status', $registrationApplicant?->status) === 'approved')>승인</option>
                    <option value="cancelled" @selected(old('status', $registrationApplicant?->status) === 'cancelled')>취소</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="agreed_privacy" class="bo-form-label">개인정보 동의</label>
            <div class="bo-form-field">
                <select class="board-form-control @error('agreed_privacy') is-invalid @enderror" id="agreed_privacy" name="agreed_privacy">
                    <option value="1" @selected((string) old('agreed_privacy', $registrationApplicant?->agreed_privacy ?? true) === '1')>동의</option>
                    <option value="0" @selected((string) old('agreed_privacy', $registrationApplicant?->agreed_privacy ?? true) === '0')>미동의</option>
                </select>
                @error('agreed_privacy')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="note" class="bo-form-label">비고</label>
            <div class="bo-form-field">
                <textarea class="board-form-control board-form-textarea @error('note') is-invalid @enderror" id="note" name="note" rows="6">{{ old('note', $registrationApplicant?->note) }}</textarea>
                @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>
