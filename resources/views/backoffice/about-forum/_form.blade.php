@php
    $aboutPage = $aboutPage ?? null;
    $detail = $aboutPage?->forumDetail;
    $emptyItem = ['title' => null, 'content' => null];
    $backgrounds = array_values((array) old('backgrounds', $detail?->backgrounds ?? []));
    $backgrounds = array_slice(array_pad($backgrounds, 4, $emptyItem), 0, 4);
    $objectives = array_values((array) old('objectives', $detail?->objectives ?? []));
    $objectives = array_slice(array_pad($objectives, 3, $emptyItem), 0, 3);
@endphp

<div class="bo-form-section">
    <div class="bo-form-list">
        @include('backoffice.about-pages._main_page_select')

        <div class="bo-form-row">
            <label for="subtitle" class="bo-form-label">About the Forum Sub Title</label>
            <div class="bo-form-field">
                <textarea class="board-form-control board-form-textarea @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" rows="10" data-backoffice-ckeditor data-source-editing="true">{{ old('subtitle', $aboutPage?->subtitle) }}</textarea>
                @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="overview" class="bo-form-label">Overview</label>
            <div class="bo-form-field">
                <textarea class="board-form-control board-form-textarea @error('overview') is-invalid @enderror" id="overview" name="overview" rows="10" data-backoffice-ckeditor data-source-editing="true">{{ old('overview', $detail?->overview) }}</textarea>
                @error('overview')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        @foreach ([
            'forums_since_2015' => 'Forums Since 2015',
            'participants' => 'Participants',
            'countries' => 'Countries',
            'organizations' => 'Organizations',
        ] as $field => $label)
            <div class="bo-form-row">
                <label for="{{ $field }}" class="bo-form-label">{{ $label }}</label>
                <div class="bo-form-field">
                    <input type="text" class="board-form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $detail?->{$field}) }}" maxlength="255" inputmode="numeric" pattern="[0-9+]*" placeholder="숫자 및 + 만 입력하세요.">
                    @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="bo-form-section">
    <h3 class="bo-section-title">Background</h3>
    <div class="bo-form-list">

        @foreach ($backgrounds as $index => $background)
            <div class="bo-form-row">
                <label for="background_{{ $index }}_title" class="bo-form-label">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }} 주제</label>
                <div class="bo-form-field">
                    <input type="text" class="board-form-control @error("backgrounds.$index.title") is-invalid @enderror" id="background_{{ $index }}_title" name="backgrounds[{{ $index }}][title]" value="{{ $background['title'] ?? '' }}" maxlength="255" placeholder="주제를 입력해 주세요">
                    @error("backgrounds.$index.title")<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="bo-form-row">
                <label for="background_{{ $index }}_content" class="bo-form-label">내용</label>
                <div class="bo-form-field">
                    <textarea class="board-form-control board-form-textarea @error("backgrounds.$index.content") is-invalid @enderror" id="background_{{ $index }}_content" name="backgrounds[{{ $index }}][content]" rows="10" aria-label="Background {{ $index + 1 }} 내용" data-backoffice-ckeditor data-source-editing="true">{{ $background['content'] ?? '' }}</textarea>
                    @error("backgrounds.$index.content")<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="bo-form-section">
    <h3 class="bo-section-title">Objectives</h3>
    <div class="bo-form-list">

        @foreach ($objectives as $index => $objective)
            <div class="bo-form-row">
                <label for="objective_{{ $index }}_title" class="bo-form-label">주제</label>
                <div class="bo-form-field">
                    <input type="text" class="board-form-control @error("objectives.$index.title") is-invalid @enderror" id="objective_{{ $index }}_title" name="objectives[{{ $index }}][title]" value="{{ $objective['title'] ?? '' }}" maxlength="255" placeholder="주제를 입력해 주세요">
                    @error("objectives.$index.title")<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="bo-form-row">
                <label for="objective_{{ $index }}_content" class="bo-form-label">내용</label>
                <div class="bo-form-field">
                    <textarea class="board-form-control board-form-textarea @error("objectives.$index.content") is-invalid @enderror" id="objective_{{ $index }}_content" name="objectives[{{ $index }}][content]" rows="10" aria-label="Objectives {{ $index + 1 }} 내용" data-backoffice-ckeditor data-source-editing="true">{{ $objective['content'] ?? '' }}</textarea>
                    @error("objectives.$index.content")<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        @endforeach
    </div>
</div>
