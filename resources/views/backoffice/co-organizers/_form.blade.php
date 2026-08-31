@php
    $storedItems = $aboutPage?->relationLoaded('coOrganizerItems') ? $aboutPage->coOrganizerItems : collect();
    $oldItems = old('items');
    $items = is_array($oldItems) ? array_values($oldItems) : $storedItems->map(fn($stored) => ['id' => $stored->id, 'name' => $stored->name, 'description' => $stored->description, 'url' => $stored->url])->all();
    if ($items === []) $items = [['name' => '', 'description' => '', 'url' => '']];
@endphp
<div class="bo-form-section"><div class="bo-form-list">
    @include('backoffice.about-pages._main_page_select')
    <div class="bo-form-row"><label for="subtitle" class="bo-form-label">Sub Title</label><div class="bo-form-field"><textarea class="board-form-control board-form-textarea @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" rows="10" data-backoffice-ckeditor data-source-editing="true">{{ old('subtitle', $aboutPage?->subtitle) }}</textarea>@error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
</div></div>
<div class="bo-repeat-toolbar"><span class="bo-form-label">공동 주관사</span><button type="button" class="btn btn-secondary btn-sm" data-co-organizer-add><i class="fas fa-plus"></i> 추가하기</button><button type="button" class="btn btn-danger btn-sm" data-co-organizer-remove><i class="fas fa-minus"></i> 삭제</button></div>
@error('items')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
<div class="bo-repeat-list" data-co-organizer-items>
    @foreach($items as $index => $item)
        @include('backoffice.co-organizers._item', ['index' => $index, 'item' => $item, 'existingItem' => filled($item['id'] ?? null) ? $storedItems->firstWhere('id', (int) $item['id']) : null])
    @endforeach
</div>
<template data-co-organizer-template>
    @include('backoffice.co-organizers._item', ['index' => '__INDEX__', 'item' => [], 'existingItem' => null])
</template>
