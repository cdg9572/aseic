@php
    $programmePage = $programmePage ?? null;
    $existingBooks = $programmePage?->books?->keyBy('id') ?? collect();
    $submittedBooks = old('books');

    if (is_array($submittedBooks)) {
        $bookItems = collect($submittedBooks)->map(function (array $book) use ($existingBooks): array {
            $existingBook = isset($book['id']) ? $existingBooks->get((int) $book['id']) : null;

            return [
                'file_path' => $existingBook?->file_path,
                'file_name' => $existingBook?->file_name,
                'file_size' => $existingBook?->file_size,
                ...$book,
            ];
        })->values()->all();
    } else {
        $bookItems = $existingBooks->values()->map(fn ($book): array => [
            'id' => $book->id,
            'title' => $book->title,
            'file_path' => $book->file_path,
            'file_name' => $book->file_name,
            'file_size' => $book->file_size,
            'link' => $book->link,
            'remove_file' => false,
        ])->all();
    }

    if ($bookItems === []) {
        $bookItems = [[]];
    }
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

<div class="bo-repeat-toolbar">
    <span class="bo-form-label">Programme Book</span>
    <button type="button" class="btn btn-secondary btn-sm" data-programme-book-add><i class="fas fa-plus"></i> 추가하기</button>
    <button type="button" class="btn btn-danger btn-sm" data-programme-book-remove><i class="fas fa-minus"></i> 삭제</button>
</div>

@error('books')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

<div class="bo-repeat-list" data-programme-book-list data-next-index="{{ count($bookItems) }}">
    @foreach ($bookItems as $index => $book)
        @include('backoffice.programme-pages._book_item', ['index' => $index, 'book' => $book])
    @endforeach
</div>

<template data-programme-book-template>
    @include('backoffice.programme-pages._book_item', ['index' => '__INDEX__', 'book' => [], 'isTemplate' => true])
</template>
