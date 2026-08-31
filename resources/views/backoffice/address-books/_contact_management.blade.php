@php
    $isCreate = !isset($addressBook) || !$addressBook?->exists;
    $contacts = $isCreate ? collect() : $addressBook->contacts;
    $showNewRow = $isCreate
        ? old('contacts.0.name') !== null || old('contacts.0.email') !== null
        : ($errors->has('contact_name') && old('editing_contact_id') === null);
    $editingContactId = (int) old('editing_contact_id', 0);
@endphp

<div class="bo-form-section" data-address-contact-management>
    <div class="bo-about-repeat-toolbar">
        <span class="bo-form-label">주소록 관리 리스트</span>
        <button type="button" class="btn btn-secondary btn-sm" data-address-contact-add>
            <i class="fas fa-plus"></i> 추가
        </button>
    </div>

    @unless($isCreate)
        <form id="address-contact-create-form" action="{{ route('backoffice.address-books.contacts.store', $addressBook) }}" method="POST">
            @csrf
            <input type="hidden" name="return_url" value="{{ $returnUrl }}">
        </form>
    @endunless

    <div class="table-responsive">
        <table class="board-table">
            <thead>
                <tr>
                    <th>번호</th>
                    <th>이름</th>
                    <th>이메일</th>
                    <th>등록일</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <tr data-address-contact-new @unless($showNewRow) hidden @endunless>
                    <td>신규</td>
                    <td>
                        <input
                            type="text"
                            class="board-form-control @if(!$isCreate) @error('contact_name') is-invalid @enderror @else @error('contacts.0.name') is-invalid @enderror @endif"
                            name="{{ $isCreate ? 'contacts[0][name]' : 'contact_name' }}"
                            value="{{ $isCreate ? old('contacts.0.name') : old('contact_name') }}"
                            maxlength="255"
                            @unless($isCreate) form="address-contact-create-form" @endunless
                            data-address-contact-new-name
                        >
                        @if($isCreate)
                            @error('contacts.0.name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @else
                            @error('contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </td>
                    <td>
                        <input
                            type="email"
                            class="board-form-control @if(!$isCreate) @error('contact_email') is-invalid @enderror @else @error('contacts.0.email') is-invalid @enderror @endif"
                            name="{{ $isCreate ? 'contacts[0][email]' : 'contact_email' }}"
                            value="{{ $isCreate ? old('contacts.0.email') : old('contact_email') }}"
                            maxlength="255"
                            @unless($isCreate) form="address-contact-create-form" @endunless
                        >
                        @if($isCreate)
                            @error('contacts.0.email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @else
                            @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @endif
                    </td>
                    <td>-</td>
                    <td>
                        <div class="board-btn-group">
                            @if($isCreate)
                                <input type="hidden" name="contacts[0][is_subscribed]" value="1">
                                <button type="submit" class="btn btn-primary btn-sm" name="continue_contacts" value="1">등록</button>
                            @else
                                <button type="submit" class="btn btn-primary btn-sm" form="address-contact-create-form" data-skip-button>등록</button>
                            @endif
                            <button type="button" class="btn btn-secondary btn-sm" data-address-contact-new-cancel>취소</button>
                        </div>
                    </td>
                </tr>

                @foreach($contacts as $index => $contact)
                    <tr data-address-contact-display="{{ $contact->id }}" @if($editingContactId === $contact->id) hidden @endif>
                        <td>{{ $contacts->count() - $index }}</td>
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ $contact->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="board-btn-group">
                                <button type="button" class="btn btn-primary btn-sm" data-address-contact-edit-open="{{ $contact->id }}">수정</button>
                                <form action="{{ route('backoffice.address-books.contacts.destroy', [$addressBook, $contact]) }}" method="POST" class="bo-inline-form" data-address-contact-delete-form>
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="return_url" value="{{ $returnUrl }}">
                                    <button type="submit" class="btn btn-danger btn-sm">삭제</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr data-address-contact-edit="{{ $contact->id }}" @unless($editingContactId === $contact->id) hidden @endunless>
                        <td>{{ $contacts->count() - $index }}</td>
                        <td>
                            <input type="text" class="board-form-control @if($editingContactId === $contact->id) @error('contact_name') is-invalid @enderror @endif" name="contact_name" value="{{ $editingContactId === $contact->id ? old('contact_name') : $contact->name }}" maxlength="255" form="address-contact-update-{{ $contact->id }}">
                            @if($editingContactId === $contact->id) @error('contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                        </td>
                        <td>
                            <input type="email" class="board-form-control @if($editingContactId === $contact->id) @error('contact_email') is-invalid @enderror @endif" name="contact_email" value="{{ $editingContactId === $contact->id ? old('contact_email') : $contact->email }}" maxlength="255" form="address-contact-update-{{ $contact->id }}">
                            @if($editingContactId === $contact->id) @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror @endif
                        </td>
                        <td>{{ $contact->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="board-btn-group">
                                <form id="address-contact-update-{{ $contact->id }}" action="{{ route('backoffice.address-books.contacts.update', [$addressBook, $contact]) }}" method="POST" class="bo-inline-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="editing_contact_id" value="{{ $contact->id }}">
                                    <input type="hidden" name="return_url" value="{{ $returnUrl }}">
                                    <button type="submit" class="btn btn-primary btn-sm">저장</button>
                                </form>
                                <button type="button" class="btn btn-secondary btn-sm" data-address-contact-edit-cancel="{{ $contact->id }}">취소</button>
                            </div>
                        </td>
                    </tr>
                @endforeach

                @if($contacts->isEmpty())
                    <tr data-address-contact-empty @if($showNewRow) hidden @endif>
                        <td colspan="5" class="text-center">등록된 연락처가 없습니다.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
