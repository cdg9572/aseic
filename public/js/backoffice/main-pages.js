document.addEventListener('DOMContentLoaded', function () {
    initializeMainPageList();
    initializeMainPageDateInput();
    initializeMainPageFileUploads();
    initializeMainPageExistingFileRemovals();
    initializeMainPageSpeakerModal();
});

function initializeMainPageList() {
    const selectAll = document.getElementById('select-all');
    const rowCheckboxes = Array.from(document.querySelectorAll('.bo-row-checkbox'));

    selectAll?.addEventListener('change', function () {
        rowCheckboxes.forEach(function (checkbox) {
            checkbox.checked = selectAll.checked;
        });
    });

    rowCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            if (!selectAll) return;
            selectAll.checked = rowCheckboxes.length > 0 && rowCheckboxes.every(function (item) {
                return item.checked;
            });
        });
    });

    document.querySelectorAll('[data-auto-submit]').forEach(function (select) {
        select.addEventListener('change', function () {
            select.form?.submit();
        });
    });

    const deleteMultipleButton = document.getElementById('btnDeleteMultiple');
    deleteMultipleButton?.addEventListener('click', async function () {
        const ids = rowCheckboxes.filter(function (checkbox) {
            return checkbox.checked;
        }).map(function (checkbox) {
            return Number(checkbox.value);
        });

        if (ids.length === 0) {
            window.alert('삭제할 Main Page를 선택해주세요.');
            return;
        }

        if (!window.confirm('선택한 ' + ids.length + '개의 Main Page를 삭제하시겠습니까?')) return;

        try {
            await mainPageRequestJson(deleteMultipleButton.dataset.url, 'POST', { ids: ids });
            window.location.reload();
        } catch (error) {
            // mainPageRequestJson에서 사용자에게 오류를 안내합니다.
        }
    });

    document.querySelectorAll('.btn-delete-main-page').forEach(function (button) {
        button.addEventListener('click', async function () {
            if (!window.confirm('정말로 삭제하시겠습니까?')) return;

            try {
                await mainPageRequestJson(button.dataset.url, 'DELETE');
                window.location.reload();
            } catch (error) {
                // mainPageRequestJson에서 사용자에게 오류를 안내합니다.
            }
        });
    });
}

function initializeMainPageDateInput() {
    const toggle = document.querySelector('[data-main-page-custom-date-toggle]');
    const customInput = document.querySelector('[data-main-page-custom-date-input]');
    const dateInputs = document.querySelectorAll('[data-main-page-date-inputs] input[type="date"]');
    if (!toggle || !customInput) return;

    const synchronize = function () {
        customInput.classList.toggle('is-active', toggle.checked);
        customInput.disabled = !toggle.checked;
        dateInputs.forEach(function (input) {
            input.disabled = toggle.checked;
        });
    };

    toggle.addEventListener('change', synchronize);
    synchronize();
}

function initializeMainPageFileUploads() {
    document.querySelectorAll('[data-main-page-file-upload]').forEach(function (upload) {
        const input = upload.querySelector('.board-file-input');
        const preview = upload.querySelector('[data-main-page-file-preview]');
        if (!input || !preview) return;

        input.addEventListener('change', function () {
            setMainPageFiles(input, preview, Array.from(input.files || []));
        });

        upload.addEventListener('dragover', function (event) {
            event.preventDefault();
            upload.classList.add('board-file-drag-over');
        });

        upload.addEventListener('dragleave', function () {
            upload.classList.remove('board-file-drag-over');
        });

        upload.addEventListener('drop', function (event) {
            event.preventDefault();
            upload.classList.remove('board-file-drag-over');
            const droppedFiles = Array.from(event.dataTransfer?.files || []);
            if (droppedFiles.length === 0) return;

            const files = input.multiple
                ? mergeMainPageFiles(Array.from(input.files || []), droppedFiles)
                : [droppedFiles[0]];
            setMainPageFiles(input, preview, files);
        });
    });
}

function initializeMainPageExistingFileRemovals() {
    document.querySelectorAll('[data-main-page-existing-remove]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (!window.confirm('기존 이미지를 삭제하시겠습니까?')) return;

            const upload = button.closest('[data-main-page-file-upload]');
            const item = button.closest('[data-main-page-existing-file]');
            const inputContainer = upload?.querySelector('[data-main-page-removed-inputs]');
            if (!upload || !item || !inputContainer) return;

            if (button.dataset.removeMode === 'multiple') {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = button.dataset.removeName + '[]';
                hiddenInput.value = button.dataset.index;
                inputContainer.appendChild(hiddenInput);
            } else {
                const hiddenInput = inputContainer.querySelector('[data-main-page-single-remove-input]');
                if (hiddenInput) hiddenInput.value = '1';
            }

            upload.dataset.existingCount = String(Math.max(0, Number(upload.dataset.existingCount || 0) - 1));
            const existingFiles = item.closest('.board-existing-files');
            item.remove();
            if (existingFiles && !existingFiles.querySelector('[data-main-page-existing-file]')) {
                existingFiles.remove();
            }
        });
    });
}

function setMainPageFiles(input, preview, files) {
    const upload = input.closest('[data-main-page-file-upload]');
    const existingCount = Number(upload?.dataset.existingCount || 0);
    const maxFiles = Number(input.dataset.maxFiles || 1);
    const maxSize = Number(input.dataset.maxSize || 0);

    if (existingCount + files.length > maxFiles) {
        window.alert('기존 이미지를 포함해 최대 ' + maxFiles + '개까지 등록할 수 있습니다.');
        input.value = '';
        preview.innerHTML = '';
        return;
    }

    if (files.some(function (file) { return !isAcceptedMainPageFile(input, file); })) {
        window.alert('JPG 또는 PNG 이미지만 등록할 수 있습니다.');
        input.value = '';
        preview.innerHTML = '';
        return;
    }

    if (maxSize > 0 && files.some(function (file) { return file.size > maxSize; })) {
        window.alert('각 이미지는 ' + Math.floor(maxSize / 1024 / 1024) + 'MB 이하만 등록할 수 있습니다.');
        input.value = '';
        preview.innerHTML = '';
        return;
    }

    const transfer = new DataTransfer();
    files.forEach(function (file) {
        transfer.items.add(file);
    });
    input.files = transfer.files;
    renderMainPageFilePreviews(input, preview);
}

function mergeMainPageFiles(currentFiles, addedFiles) {
    return [...currentFiles, ...addedFiles].filter(function (file, index, files) {
        return files.findIndex(function (candidate) {
            return candidate.name === file.name && candidate.size === file.size && candidate.lastModified === file.lastModified;
        }) === index;
    });
}

function isAcceptedMainPageFile(input, file) {
    const extensions = (input.accept || '').split(',').map(function (value) {
        return value.trim().toLowerCase();
    }).filter(Boolean);
    const fileName = file.name.toLowerCase();

    return extensions.length === 0 || extensions.some(function (extension) {
        return fileName.endsWith(extension);
    });
}

function renderMainPageFilePreviews(input, preview) {
    preview.innerHTML = '';

    Array.from(input.files || []).forEach(function (file, index) {
        const item = document.createElement('div');
        item.className = 'board-file-item';

        const info = document.createElement('div');
        info.className = 'board-file-info';

        const icon = document.createElement('i');
        icon.className = 'fas fa-image';

        const name = document.createElement('span');
        name.className = 'board-file-name';
        name.textContent = file.name;

        const size = document.createElement('span');
        size.className = 'board-file-size';
        size.textContent = '(' + (file.size / 1024 / 1024).toFixed(2) + 'MB)';

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'board-file-remove';
        remove.setAttribute('aria-label', '선택 이미지 제거');
        remove.innerHTML = '<i class="fas fa-times"></i>';
        remove.addEventListener('click', function () {
            const remainingFiles = Array.from(input.files || []);
            remainingFiles.splice(index, 1);
            setMainPageFiles(input, preview, remainingFiles);
        });

        info.append(icon, name, size);
        item.append(info, remove);
        preview.appendChild(item);
    });
}

function initializeMainPageSpeakerModal() {
    const modal = document.querySelector('[data-main-page-speaker-modal]');
    const openButton = document.querySelector('[data-main-page-speaker-open]');
    const applyButton = document.querySelector('[data-main-page-speaker-apply]');
    const selectedContainer = document.querySelector('[data-main-page-selected-speakers]');
    if (!modal || !openButton || !applyButton || !selectedContainer) return;

    const checkboxes = Array.from(modal.querySelectorAll('[data-main-page-speaker-checkbox]'));
    const rows = Array.from(modal.querySelectorAll('[data-main-page-speaker-row]'));
    const searchInput = modal.querySelector('[data-main-page-speaker-search]');

    const synchronizeCheckboxes = function () {
        const selectedIds = new Set(Array.from(selectedContainer.querySelectorAll('[data-selected-speaker-id]')).map(function (item) {
            return item.dataset.selectedSpeakerId;
        }));
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = selectedIds.has(checkbox.value);
        });
    };

    const closeModal = function () {
        modal.hidden = true;
        document.body.classList.remove('bo-main-page-modal-open');
    };

    openButton.addEventListener('click', function () {
        synchronizeCheckboxes();
        modal.hidden = false;
        document.body.classList.add('bo-main-page-modal-open');
        searchInput?.focus();
    });

    modal.querySelectorAll('[data-main-page-speaker-close]').forEach(function (button) {
        button.addEventListener('click', closeModal);
    });

    searchInput?.addEventListener('input', function () {
        const keyword = searchInput.value.trim().toLowerCase();
        rows.forEach(function (row) {
            row.hidden = keyword !== '' && !(row.dataset.speakerSearchText || '').includes(keyword);
        });
    });

    applyButton.addEventListener('click', function () {
        selectedContainer.innerHTML = '';
        checkboxes.filter(function (checkbox) {
            return checkbox.checked;
        }).forEach(function (checkbox) {
            const row = checkbox.closest('[data-main-page-speaker-row]');
            const displayName = row?.querySelector('[data-speaker-display-name]')?.textContent?.trim() || checkbox.value;
            selectedContainer.appendChild(createMainPageSelectedSpeaker(checkbox.value, displayName));
        });
        closeModal();
    });

    selectedContainer.addEventListener('click', function (event) {
        const button = event.target.closest('[data-main-page-speaker-remove]');
        if (!button) return;
        button.closest('[data-selected-speaker-id]')?.remove();
    });
}

function createMainPageSelectedSpeaker(id, displayName) {
    const item = document.createElement('div');
    item.className = 'bo-main-page-selected-item';
    item.dataset.selectedSpeakerId = id;

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'speaker_ids[]';
    input.value = id;

    const name = document.createElement('span');
    name.textContent = displayName;

    const remove = document.createElement('button');
    remove.type = 'button';
    remove.className = 'bo-main-page-selected-remove';
    remove.dataset.mainPageSpeakerRemove = '';
    remove.setAttribute('aria-label', 'Speaker 선택 해제');
    remove.innerHTML = '<i class="fas fa-times"></i>';

    item.append(input, name, remove);
    return item;
}

async function mainPageRequestJson(url, method, payload) {
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                Accept: 'application/json',
            },
            body: JSON.stringify(payload || {}),
        });
        const result = await response.json();

        if (!response.ok || result.success === false) {
            throw new Error(result.message || '요청을 처리하지 못했습니다.');
        }

        return result;
    } catch (error) {
        window.alert(error.message || '요청 처리 중 오류가 발생했습니다.');
        throw error;
    }
}
