@php($programmePage = $programmePage ?? null)

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

        @if ($context['show_event_fields'] ?? true)
        <div class="bo-form-row">
            <label for="title" class="bo-form-label">Title</label>
            <div class="bo-form-field">
                <textarea class="board-form-control board-form-textarea @error('title') is-invalid @enderror" id="title" name="title" rows="10" data-backoffice-ckeditor data-source-editing="true">{{ old('title', $programmePage?->title) }}</textarea>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="location" class="bo-form-label">Location</label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $programmePage?->location) }}" maxlength="255">
                @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="event_date" class="bo-form-label">Date</label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('event_date') is-invalid @enderror" id="event_date" name="event_date" value="{{ old('event_date', $programmePage?->event_date) }}" maxlength="255">
                @error('event_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        @endif

        <div class="bo-form-row">
            <label for="content" class="bo-form-label">{{ $context['content_label'] ?? $context['entity_name'] }}</label>
            <div class="bo-form-field">
                <textarea class="board-form-control board-form-textarea @error('content') is-invalid @enderror" id="content" name="content" rows="12" data-backoffice-ckeditor data-source-editing="true">{{ old('content', $programmePage?->content) }}</textarea>
                @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>
