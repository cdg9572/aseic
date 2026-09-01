@php
    $programmeItems = old('programme_items', $mainPage->programme_items ?? []);
    $programmeItems = is_array($programmeItems) ? array_values($programmeItems) : [];
    while (count($programmeItems) < 4) {
        $programmeItems[] = ['time' => '', 'subject' => '', 'content' => ''];
    }
    $programmeItems = array_slice($programmeItems, 0, 4);

    $storedSpeakerIds = $mainPage->exists && $mainPage->relationLoaded('speakers')
        ? $mainPage->speakers->pluck('id')->all()
        : [];
    $selectedSpeakerIds = array_values(array_unique(array_map('intval', (array) old('speaker_ids', $storedSpeakerIds))));
    $selectedSpeakers = $speakers->whereIn('id', $selectedSpeakerIds)->sortBy(
        fn ($speaker) => array_search($speaker->id, $selectedSpeakerIds, true)
    );

    $storedLinks = $mainPage->exists && $mainPage->relationLoaded('links')
        ? $mainPage->links->pluck('linkable_id', 'slot')->all()
        : [];
    $linkValues = (array) old('links', $storedLinks);
    $linkGroups = [
        'ABOUT 페이지 연결' => ['about_forum', 'steering_committee', 'co_organizers', 'venue'],
        'Programme 페이지 연결' => ['programme_theme', 'programme', 'programme_speakers', 'programme_book'],
        'ARCHIVE 페이지 연결' => ['archive_theme', 'archive_programme', 'archive_speakers', 'archive_legacy'],
        'REGISTRATION 페이지 연결' => ['registration'],
    ];

    $programmeBackgroundFiles = $mainPage->programme_background_path ? [[
        'path' => $mainPage->programme_background_path,
        'name' => $mainPage->programme_background_name ?: basename($mainPage->programme_background_path),
    ]] : [];
    $registerBackgroundFiles = $mainPage->register_background_path ? [[
        'path' => $mainPage->register_background_path,
        'name' => $mainPage->register_background_name ?: basename($mainPage->register_background_path),
    ]] : [];
@endphp

<div class="bo-form-section">
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label for="is_visible" class="bo-form-label">노출 여부</label>
            <div class="bo-form-field">
                <select class="board-form-control @error('is_visible') is-invalid @enderror" id="is_visible" name="is_visible">
                    <option value="1" @selected((string) old('is_visible', $mainPage->is_visible ?? false) === '1')>Y</option>
                    <option value="0" @selected((string) old('is_visible', $mainPage->is_visible ?? false) === '0')>N</option>
                </select>
                <div class="board-form-help">메인페이지 노출여부 선택 시 사용자에게 보여줍니다.</div>
                @error('is_visible')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="folder_name" class="bo-form-label">연도(폴더명) <span class="required">*</span></label>
            <div class="bo-form-field">
                @if ($mainPage->exists)
                    <input type="text" class="board-form-control" id="folder_name" value="{{ $mainPage->folder_name }}" disabled>
                @else
                    <input type="text" class="board-form-control @error('folder_name') is-invalid @enderror" id="folder_name" name="folder_name" value="{{ old('folder_name') }}" maxlength="120" placeholder="예: 2026-forum" required>
                @endif
                <div class="board-form-help">공백과 한글은 사용할 수 없으며 생성 후 수정할 수 없습니다.</div>
                @error('folder_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="bo-form-row">
            <label for="event_name" class="bo-form-label">행사명 <span class="required">*</span></label>
            <div class="bo-form-field">
                <input type="text" class="board-form-control @error('event_name') is-invalid @enderror" id="event_name" name="event_name" value="{{ old('event_name', $mainPage->event_name) }}" maxlength="255" required>
                @error('event_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label class="bo-form-label">행사일시 <span class="required">*</span></label>
            <div class="bo-form-field bo-main-page-full-field">
                <div class="bo-main-page-date-row" data-main-page-date-inputs>
                    <input type="date" class="board-form-control @error('event_start_date') is-invalid @enderror" name="event_start_date" value="{{ old('event_start_date', $mainPage->event_start_date?->format('Y-m-d')) }}">
                    <span class="bo-main-page-date-separator">~</span>
                    <input type="date" class="board-form-control @error('event_end_date') is-invalid @enderror" name="event_end_date" value="{{ old('event_end_date', $mainPage->event_end_date?->format('Y-m-d')) }}">
                    <label class="checkbox-label bo-main-page-direct-date">
                        <input type="checkbox" name="use_custom_event_date" value="1" data-main-page-custom-date-toggle @checked((bool) old('use_custom_event_date', $mainPage->use_custom_event_date))>
                        직접입력
                    </label>
                </div>
                <input type="text" class="board-form-control bo-main-page-custom-date @error('event_date_text') is-invalid @enderror" name="event_date_text" value="{{ old('event_date_text', $mainPage->event_date_text) }}" maxlength="255" placeholder="행사일시를 입력해주세요" data-main-page-custom-date-input>
                @error('event_start_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @error('event_end_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @error('event_date_text')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="banner_id" class="bo-form-label">배너 선택</label>
            <div class="bo-form-field">
                <select class="board-form-control @error('banner_id') is-invalid @enderror" id="banner_id" name="banner_id">
                    <option value="">폴더를 선택해주세요.</option>
                    @foreach ($banners as $banner)
                        <option value="{{ $banner->id }}" @selected((string) old('banner_id', $mainPage->banner_id) === (string) $banner->id)>{{ $banner->title }}</option>
                    @endforeach
                </select>
                @error('banner_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label for="popup_id" class="bo-form-label">팝업 선택</label>
            <div class="bo-form-field">
                <select class="board-form-control @error('popup_id') is-invalid @enderror" id="popup_id" name="popup_id">
                    <option value="">폴더를 선택해주세요.</option>
                    @foreach ($popups as $popup)
                        <option value="{{ $popup->id }}" @selected((string) old('popup_id', $mainPage->popup_id) === (string) $popup->id)>{{ $popup->title }}</option>
                    @endforeach
                </select>
                @error('popup_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="bo-form-row">
            <label class="bo-form-label">Speakers</label>
            <div class="bo-form-field bo-main-page-full-field">
                <button type="button" class="btn btn-secondary btn-sm" data-main-page-speaker-open>
                    <i class="fas fa-plus"></i> 추가하기
                </button>
                <span class="board-form-help bo-main-page-inline-help">메인화면에 보여줄 Speakers를 선택해주세요.</span>
                <div class="bo-main-page-selected-speakers" data-main-page-selected-speakers>
                    @foreach ($selectedSpeakers as $speaker)
                        <div class="bo-main-page-selected-item" data-selected-speaker-id="{{ $speaker->id }}">
                            <input type="hidden" name="speaker_ids[]" value="{{ $speaker->id }}">
                            <span>{{ $speaker->full_name }}@if($speaker->position) · {{ $speaker->position }}@endif</span>
                            <button type="button" class="bo-main-page-selected-remove" data-main-page-speaker-remove aria-label="Speaker 선택 해제"><i class="fas fa-times"></i></button>
                        </div>
                    @endforeach
                </div>
                @error('speaker_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @error('speaker_ids.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="bo-form-section">
    <h3 class="bo-section-title">Programme</h3>
    @include('backoffice.main-pages._image_upload', [
        'field' => 'programme_background',
        'inputName' => 'programme_background',
        'label' => 'Background 이미지',
        'uploadText' => 'Programme 배경 이미지를 선택하거나 여기로 드래그하세요',
        'multiple' => false,
        'maxFiles' => 1,
        'existingFiles' => $programmeBackgroundFiles,
        'singleRemoved' => (bool) old('remove_programme_background', false),
        'removeInputName' => 'remove_programme_background',
    ])

    <div class="bo-form-row">
        <label class="bo-form-label">Programme 항목</label>
        <div class="bo-form-field bo-main-page-full-field">
            <div class="table-responsive">
                <table class="board-table bo-main-page-programme-table">
                    <thead><tr><th class="w20">시간</th><th class="w30">주제</th><th>내용</th></tr></thead>
                    <tbody>
                        @foreach ($programmeItems as $index => $item)
                            <tr>
                                <td><input type="text" class="board-form-control" name="programme_items[{{ $index }}][time]" value="{{ $item['time'] ?? '' }}" maxlength="100" placeholder="시간을 입력해주세요."></td>
                                <td><input type="text" class="board-form-control" name="programme_items[{{ $index }}][subject]" value="{{ $item['subject'] ?? '' }}" maxlength="255" placeholder="주제를 입력해주세요."></td>
                                <td><input type="text" class="board-form-control" name="programme_items[{{ $index }}][content]" value="{{ $item['content'] ?? '' }}" maxlength="500" placeholder="내용을 입력해주세요."></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @error('programme_items')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            @error('programme_items.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="bo-form-section">
    <h3 class="bo-section-title">Register</h3>
    @include('backoffice.main-pages._image_upload', [
        'field' => 'register_background',
        'inputName' => 'register_background',
        'label' => 'Background 이미지',
        'uploadText' => 'Register 배경 이미지를 선택하거나 여기로 드래그하세요',
        'multiple' => false,
        'maxFiles' => 1,
        'existingFiles' => $registerBackgroundFiles,
        'singleRemoved' => (bool) old('remove_register_background', false),
        'removeInputName' => 'remove_register_background',
    ])
</div>

<div class="bo-form-section">
    <h3 class="bo-section-title">PAST FORUM VIDEO</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label for="past_forum_video_url" class="bo-form-label">유튜브 링크</label>
            <div class="bo-form-field">
                <input type="url" class="board-form-control @error('past_forum_video_url') is-invalid @enderror" id="past_forum_video_url" name="past_forum_video_url" value="{{ old('past_forum_video_url', $mainPage->past_forum_video_url) }}" maxlength="2048" placeholder="https://www.youtube.com/...">
                @error('past_forum_video_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

@include('backoffice.main-pages._image_upload', [
    'field' => 'host_images',
    'inputName' => 'host_images[]',
    'label' => 'Host',
    'uploadText' => 'Host 이미지를 선택하거나 여기로 드래그하세요',
    'multiple' => true,
    'maxFiles' => 5,
    'existingFiles' => $mainPage->host_image_files,
    'removedIndexes' => old('remove_host_images', []),
    'removeInputName' => 'remove_host_images',
])

@include('backoffice.main-pages._image_upload', [
    'field' => 'organizer_images',
    'inputName' => 'organizer_images[]',
    'label' => 'Organizers',
    'uploadText' => 'Organizers 이미지를 선택하거나 여기로 드래그하세요',
    'multiple' => true,
    'maxFiles' => 5,
    'existingFiles' => $mainPage->organizer_image_files,
    'removedIndexes' => old('remove_organizer_images', []),
    'removeInputName' => 'remove_organizer_images',
])

@include('backoffice.main-pages._image_upload', [
    'field' => 'co_organizer_images',
    'inputName' => 'co_organizer_images[]',
    'label' => 'Co-Organizers',
    'uploadText' => 'Co-Organizers 이미지를 선택하거나 여기로 드래그하세요',
    'multiple' => true,
    'maxFiles' => 5,
    'existingFiles' => $mainPage->co_organizer_image_files,
    'removedIndexes' => old('remove_co_organizer_images', []),
    'removeInputName' => 'remove_co_organizer_images',
])

@foreach ($linkGroups as $groupTitle => $slots)
    <div class="bo-form-section">
        <h3 class="bo-section-title">{{ $groupTitle }}</h3>
        <div class="bo-form-list bo-main-page-link-grid">
            @foreach ($slots as $slot)
                <div class="bo-form-row">
                    <label for="link_{{ $slot }}" class="bo-form-label">{{ $linkLabels[$slot] }}</label>
                    <div class="bo-form-field">
                        <select class="board-form-control @error('links.'.$slot) is-invalid @enderror" id="link_{{ $slot }}" name="links[{{ $slot }}]">
                            <option value="">페이지를 선택해주세요.</option>
                            @foreach ($linkOptions[$slot] ?? [] as $option)
                                <option value="{{ $option->id }}" @selected((string) ($linkValues[$slot] ?? '') === (string) $option->id)>
                                    {{ $option->page_title ?: '제목 미입력 #'.$option->id }}
                                </option>
                            @endforeach
                        </select>
                        @error('links.'.$slot)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endforeach

<div class="bo-form-section">
    <h3 class="bo-section-title">하단 푸터 텍스트 설정</h3>
    <div class="bo-form-list">
        <div class="bo-form-row">
            <label for="footer_text" class="bo-form-label">하단 푸터</label>
            <div class="bo-form-field bo-main-page-full-field">
                <input type="text" class="board-form-control @error('footer_text') is-invalid @enderror" id="footer_text" name="footer_text" value="{{ old('footer_text', $mainPage->footer_text) }}" maxlength="500" placeholder="텍스트를 입력해 주세요">
                @error('footer_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="bo-main-page-modal" data-main-page-speaker-modal hidden>
    <div class="bo-main-page-modal-backdrop" data-main-page-speaker-close></div>
    <div class="bo-main-page-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="mainPageSpeakerModalTitle">
        <div class="bo-main-page-modal-header">
            <h4 id="mainPageSpeakerModalTitle">Speakers 선택</h4>
            <button type="button" class="bo-main-page-modal-close" data-main-page-speaker-close aria-label="닫기"><i class="fas fa-times"></i></button>
        </div>
        <div class="bo-main-page-modal-body">
            <div class="bo-main-page-speaker-search">
                <label for="mainPageSpeakerSearch" class="filter-label">이름</label>
                <input type="text" id="mainPageSpeakerSearch" class="filter-input" placeholder="이름을 입력하세요" data-main-page-speaker-search>
            </div>
            <div class="table-responsive">
                <table class="board-table">
                    <thead><tr><th class="w8">선택</th><th>이름</th><th>소속</th><th class="w15">Role</th></tr></thead>
                    <tbody>
                        @forelse ($speakers as $speaker)
                            <tr data-main-page-speaker-row data-speaker-search-text="{{ strtolower($speaker->full_name.' '.$speaker->affiliation.' '.$speaker->position) }}">
                                <td><input type="checkbox" class="form-check-input" value="{{ $speaker->id }}" data-main-page-speaker-checkbox @checked(in_array($speaker->id, $selectedSpeakerIds, true))></td>
                                <td data-speaker-display-name>{{ $speaker->full_name }}@if($speaker->position) · {{ $speaker->position }}@endif</td>
                                <td>{{ $speaker->affiliation ?: '-' }}</td>
                                <td>{{ $speaker->role }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">등록된 Speaker가 없습니다.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bo-main-page-modal-actions">
            <button type="button" class="btn btn-primary" data-main-page-speaker-apply>선택추가</button>
            <button type="button" class="btn btn-secondary" data-main-page-speaker-close>취소</button>
        </div>
    </div>
</div>
