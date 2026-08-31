document.addEventListener('DOMContentLoaded', function () {
    initializePartnerList();
    initializePartnerProfileUpload();
});

function initializePartnerList() {
    const list = document.querySelector('[data-partner-list]');
    const entityName = list?.dataset.entityName || '항목';
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
        const ids = rowCheckboxes
            .filter(function (checkbox) { return checkbox.checked; })
            .map(function (checkbox) { return Number(checkbox.value); });

        if (ids.length === 0) {
            window.alert('삭제할 '+entityName+' 항목을 선택해주세요.');
            return;
        }

        if (!window.confirm('선택한 '+ids.length+'개의 '+entityName+' 항목을 삭제하시겠습니까?')) return;

        try {
            await requestPartnerJson(deleteMultipleButton.dataset.url, 'POST', { ids: ids });
            window.location.reload();
        } catch (error) {
            // requestPartnerJson에서 오류를 안내합니다.
        }
    });

    document.querySelectorAll('.btn-delete-partner').forEach(function (button) {
        button.addEventListener('click', async function () {
            if (!window.confirm('정말로 삭제하시겠습니까?')) return;

            try {
                await requestPartnerJson(button.dataset.url, 'DELETE');
                window.location.reload();
            } catch (error) {
                // requestPartnerJson에서 오류를 안내합니다.
            }
        });
    });
}

function initializePartnerProfileUpload() {
    document.querySelectorAll('[data-partner-file-upload]').forEach(function (upload) {
        const input = upload.querySelector('.board-file-input');
        const preview = upload.querySelector('[data-partner-file-preview]');
        const existingRemoveButton = upload.querySelector('[data-partner-existing-file-remove]');

        if (!input || !preview) return;

        input.addEventListener('change', function () {
            const file = input.files?.[0];
            if (file) setPartnerProfileFile(input, preview, file);
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
            const file = event.dataTransfer?.files?.[0];
            if (file) setPartnerProfileFile(input, preview, file);
        });

        existingRemoveButton?.addEventListener('click', function () {
            if (!window.confirm('기존 프로필 이미지를 삭제하시겠습니까?')) return;
            const removeInput = upload.querySelector('[data-partner-remove-input]');
            const existingFiles = existingRemoveButton.closest('.board-existing-files');
            if (removeInput) removeInput.value = '1';
            existingFiles?.remove();
        });
    });
}

function setPartnerProfileFile(input, preview, file) {
    const allowedExtensions = ['.jpg', '.jpeg', '.png'];
    const fileName = file.name.toLowerCase();
    const maxSize = Number(input.dataset.maxSize || 0);

    if (!allowedExtensions.some(function (extension) { return fileName.endsWith(extension); })) {
        window.alert('프로필은 JPG 또는 PNG 파일만 등록할 수 있습니다.');
        input.value = '';
        preview.innerHTML = '';
        return;
    }

    if (maxSize > 0 && file.size > maxSize) {
        window.alert('프로필 이미지는 5MB 이하만 등록할 수 있습니다.');
        input.value = '';
        preview.innerHTML = '';
        return;
    }

    const transfer = new DataTransfer();
    transfer.items.add(file);
    input.files = transfer.files;
    renderPartnerProfilePreview(input, preview, file);
}

function renderPartnerProfilePreview(input, preview, file) {
    preview.innerHTML = '';

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
    size.textContent = '('+(file.size / 1024 / 1024).toFixed(2)+'MB)';
    const remove = document.createElement('button');
    remove.type = 'button';
    remove.className = 'board-file-remove';
    remove.setAttribute('aria-label', '선택 파일 제거');
    remove.innerHTML = '<i class="fas fa-times"></i>';
    remove.addEventListener('click', function () {
        input.value = '';
        preview.innerHTML = '';
    });

    info.append(icon, name, size);
    item.append(info, remove);
    preview.appendChild(item);
}

async function requestPartnerJson(url, method, payload) {
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
