document.addEventListener('DOMContentLoaded', function () {
    initializeAboutPickers();
    initializeCoOrganizerItems();
    initializeAboutFileUploads(document);
    initializeVenueAddressSearch();
});

function initializeAboutPickers(root) {
    (root || document).querySelectorAll('[data-about-picker]').forEach(function (picker) {
        if (picker.dataset.aboutPickerInitialized === 'true') return;
        picker.dataset.aboutPickerInitialized = 'true';
        const modal = picker.querySelector('[data-about-picker-modal]');
        const selected = picker.querySelector('[data-about-picker-selected]');
        const checkboxes = Array.from(picker.querySelectorAll('[data-about-picker-checkbox]'));
        const rows = Array.from(picker.querySelectorAll('[data-about-picker-row]'));
        const search = picker.querySelector('[data-about-picker-search]');
        const error = picker.querySelector('[data-about-picker-error]');
        if (!modal || !selected) return;

        const clearError = function () {
            if (!error) return;
            error.textContent = '';
            error.hidden = true;
        };

        const showDuplicateError = function () {
            if (!error) return;
            error.textContent = '동일한 Speaker는 DAY 1과 DAY 2에 중복 선택할 수 없습니다.';
            error.hidden = false;
        };

        const otherSelectedIds = function () {
            if (!picker.hasAttribute('data-prevent-cross-picker-duplicates')) return new Set();

            const ids = new Set();
            document.querySelectorAll('[data-about-picker][data-prevent-cross-picker-duplicates]').forEach(function (otherPicker) {
                if (otherPicker === picker) return;
                otherPicker.querySelectorAll('[data-about-selected-id]').forEach(function (item) {
                    ids.add(item.dataset.aboutSelectedId);
                });
            });
            return ids;
        };

        picker.querySelector('[data-about-picker-open]')?.addEventListener('click', function () {
            const ids = new Set(Array.from(selected.querySelectorAll('[data-about-selected-id]')).map(function (item) { return item.dataset.aboutSelectedId; }));
            checkboxes.forEach(function (checkbox) { checkbox.checked = ids.has(checkbox.value); });
            clearError();
            modal.hidden = false;
            document.body.classList.add('bo-about-modal-open');
            search?.focus();
        });

        picker.querySelectorAll('[data-about-picker-close]').forEach(function (button) {
            button.addEventListener('click', function () { modal.hidden = true; document.body.classList.remove('bo-about-modal-open'); });
        });

        search?.addEventListener('input', function () {
            const keyword = search.value.trim().toLowerCase();
            rows.forEach(function (row) { row.hidden = keyword !== '' && !(row.dataset.searchText || '').includes(keyword); });
        });

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                clearError();
                if (!checkbox.checked || !otherSelectedIds().has(checkbox.value)) return;
                checkbox.checked = false;
                showDuplicateError();
            });
        });

        picker.querySelector('[data-about-picker-apply]')?.addEventListener('click', function () {
            const duplicateIds = otherSelectedIds();
            if (checkboxes.some(function (checkbox) { return checkbox.checked && duplicateIds.has(checkbox.value); })) {
                showDuplicateError();
                return;
            }

            clearError();
            selected.innerHTML = '';
            checkboxes.filter(function (checkbox) { return checkbox.checked; }).forEach(function (checkbox) {
                const name = checkbox.closest('[data-about-picker-row]')?.querySelector('[data-about-picker-name]')?.textContent?.trim() || checkbox.value;
                selected.appendChild(createAboutSelectedItem(picker.dataset.fieldName, checkbox.value, name));
            });
            modal.hidden = true;
            document.body.classList.remove('bo-about-modal-open');
        });

        selected.addEventListener('click', function (event) {
            event.target.closest('[data-about-selected-remove]')?.closest('[data-about-selected-id]')?.remove();
        });
    });
}

window.initializeAboutPickers = initializeAboutPickers;

function createAboutSelectedItem(fieldName, id, displayName) {
    const item = document.createElement('div'); item.className = 'bo-about-selected-item'; item.dataset.aboutSelectedId = id;
    const input = document.createElement('input'); input.type = 'hidden'; input.name = fieldName + '[]'; input.value = id;
    const name = document.createElement('span'); name.textContent = displayName;
    const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'bo-about-selected-remove'; remove.dataset.aboutSelectedRemove = ''; remove.setAttribute('aria-label', '선택 해제'); remove.innerHTML = '<i class="fas fa-times"></i>';
    item.append(input, name, remove); return item;
}

function initializeCoOrganizerItems() {
    const container = document.querySelector('[data-co-organizer-items]');
    const template = document.querySelector('[data-co-organizer-template]');
    if (!container || !template) return;

    let nextIndex = Array.from(container.querySelectorAll('[data-co-organizer-item]')).reduce(function (maximum, item) {
        const input = item.querySelector('[name^="items["]');
        const match = input?.name.match(/^items\[(\d+)\]/);
        return Math.max(maximum, match ? Number(match[1]) + 1 : 0);
    }, 0);

    document.querySelector('[data-co-organizer-add]')?.addEventListener('click', function () {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++));
        const item = wrapper.firstElementChild;
        if (!item) return;
        container.appendChild(item);
        initializeAboutFileUploads(item);
        if (typeof window.initBackofficeCKEditors === 'function') {
            window.initBackofficeCKEditors(item);
        }
    });

    document.querySelector('[data-co-organizer-remove]')?.addEventListener('click', function () {
        const items = container.querySelectorAll('[data-co-organizer-item]');
        if (items.length <= 1) return window.alert('공동 주관사는 1개 이상 입력해주세요.');
        items[items.length - 1].remove();
    });
}

function initializeAboutFileUploads(root) {
    root.querySelectorAll('[data-about-file-upload]').forEach(function (upload) {
        if (upload.dataset.initialized === 'true') return;
        upload.dataset.initialized = 'true';
        const input = upload.querySelector('.board-file-input');
        const preview = upload.querySelector('[data-about-file-preview]');
        if (!input || !preview) return;

        const setFile = function (files) {
            const file = files[0];
            if (!file) { input.value = ''; preview.innerHTML = ''; return; }
            if (!/\.(jpe?g|png)$/i.test(file.name)) return window.alert('JPG 또는 PNG 이미지만 등록할 수 있습니다.');
            if (file.size > Number(input.dataset.maxSize || 0)) return window.alert('이미지는 5MB 이하만 등록할 수 있습니다.');
            const transfer = new DataTransfer(); transfer.items.add(file); input.files = transfer.files;
            preview.innerHTML = '';
            const item = document.createElement('div'); item.className = 'board-file-item';
            const info = document.createElement('div'); info.className = 'board-file-info';
            const icon = document.createElement('i'); icon.className = 'fas fa-image';
            const name = document.createElement('span'); name.className = 'board-file-name'; name.textContent = file.name;
            const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'board-file-remove'; remove.innerHTML = '<i class="fas fa-times"></i>'; remove.addEventListener('click', function () { input.value = ''; preview.innerHTML = ''; });
            info.append(icon, name); item.append(info, remove); preview.appendChild(item);
        };
        input.addEventListener('change', function () { setFile(Array.from(input.files || [])); });
        upload.addEventListener('dragover', function (event) { event.preventDefault(); upload.classList.add('board-file-drag-over'); });
        upload.addEventListener('dragleave', function () { upload.classList.remove('board-file-drag-over'); });
        upload.addEventListener('drop', function (event) { event.preventDefault(); upload.classList.remove('board-file-drag-over'); setFile(Array.from(event.dataTransfer?.files || [])); });
        upload.querySelector('[data-about-existing-logo-remove]')?.addEventListener('click', function (event) {
            if (!window.confirm('기존 로고를 삭제하시겠습니까?')) return;
            const hidden = upload.querySelector('[data-about-remove-logo-input]'); if (hidden) hidden.value = '1';
            event.currentTarget.closest('.board-existing-files')?.remove();
        });
    });
}

function initializeVenueAddressSearch() {
    document.querySelector('[data-venue-address-search]')?.addEventListener('click', function () {
        if (typeof daum === 'undefined') return window.alert('주소 검색을 불러오지 못했습니다.');
        new daum.Postcode({ oncomplete: function (data) {
            const postalCode = document.getElementById('postal_code'); if (postalCode) postalCode.value = data.zonecode;
            const address = document.getElementById('address'); if (address) address.value = data.address;
            document.getElementById('address_detail')?.focus();
        }}).open();
    });
}
