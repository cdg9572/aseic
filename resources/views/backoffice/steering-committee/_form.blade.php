@php
    $aboutPage = $aboutPage ?? null;
    $organizedIds = $aboutPage?->relationLoaded('steeringOrganizedPartners') ? $aboutPage->steeringOrganizedPartners->pluck('id')->all() : [];
    $partnershipIds = $aboutPage?->relationLoaded('steeringPartnershipPartners') ? $aboutPage->steeringPartnershipPartners->pluck('id')->all() : [];
@endphp

<div class="bo-form-section"><div class="bo-form-list">
    @include('backoffice.about-pages._main_page_select')
    <div class="bo-form-row">
        <label for="subtitle" class="bo-form-label">Sub Title</label>
        <div class="bo-form-field"><textarea class="board-form-control board-form-textarea @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" rows="10" data-backoffice-ckeditor data-source-editing="true">{{ old('subtitle', $aboutPage?->subtitle) }}</textarea>@error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    </div>
    @include('backoffice.steering-committee._partner_picker', ['fieldName' => 'organized_ids', 'label' => 'Organized By', 'helpText' => 'Organized By를 선택해주세요.', 'options' => $organizedPartners, 'selectedIds' => $organizedIds])
    @include('backoffice.steering-committee._partner_picker', ['fieldName' => 'partnership_ids', 'label' => 'Partnership with', 'helpText' => 'Partnership with를 선택해주세요.', 'options' => $partnershipPartners, 'selectedIds' => $partnershipIds])
</div></div>
