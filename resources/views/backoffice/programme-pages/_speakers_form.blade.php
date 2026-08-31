@php
    $programmePage = $programmePage ?? null;
    $storedSessions = $programmePage?->relationLoaded('sessions') ? $programmePage->sessions->keyBy('day_number') : collect();
@endphp

<div class="bo-form-section">
    <div class="bo-form-list">
        @include('backoffice.programme-pages._main_page_select')

        <div class="bo-form-row">
            <label for="subtitle" class="bo-form-label">Sub Title</label>
            <div class="bo-form-field">
                <textarea class="board-form-control board-form-textarea @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" rows="10" data-backoffice-ckeditor data-source-editing="true">{{ old('subtitle', $programmePage?->subtitle) }}</textarea>
                @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

@foreach ([1, 2] as $dayNumber)
    @php
        $session = $storedSessions->get($dayNumber);
        $sessionIndex = $dayNumber - 1;
        $storedSpeakerIds = $session?->relationLoaded('speakers') ? $session->speakers->pluck('id')->all() : [];
    @endphp
    <div class="bo-form-section">
        <h3 class="bo-section-title">DAY {{ $dayNumber }} SESSION</h3>
        <div class="bo-form-list">
            @if ($dayNumber === 2)
                <div class="bo-form-row">
                    <label for="day_2_active" class="bo-form-label">활성화</label>
                    <div class="bo-form-field">
                        <label class="checkbox-label">
                            <input type="checkbox" id="day_2_active" name="sessions[1][is_active]" value="1" @checked((bool) old('sessions.1.is_active', $session?->is_active ?? false))>
                            활성화 클릭 시 메인페이지에 표출됩니다.
                        </label>
                    </div>
                </div>
            @else
                <input type="hidden" name="sessions[0][is_active]" value="1">
            @endif

            <div class="bo-form-row">
                <label for="session_{{ $sessionIndex }}_name" class="bo-form-label">Session</label>
                <div class="bo-form-field">
                    <input type="text" class="board-form-control @error('sessions.'.$sessionIndex.'.session_name') is-invalid @enderror" id="session_{{ $sessionIndex }}_name" name="sessions[{{ $sessionIndex }}][session_name]" value="{{ old('sessions.'.$sessionIndex.'.session_name', $session?->session_name) }}" maxlength="255" placeholder="SESSION 이름을 작성해주세요">
                    @error('sessions.'.$sessionIndex.'.session_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            @include('backoffice.programme-pages._speaker_picker', [
                'fieldName' => 'sessions['.$sessionIndex.'][speaker_ids]',
                'selectedIds' => $storedSpeakerIds,
                'preventDuplicateAcrossPickers' => true,
            ])
        </div>
    </div>
@endforeach
