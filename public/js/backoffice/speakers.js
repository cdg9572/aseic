document.addEventListener('DOMContentLoaded', function () {
    initializeSpeakerFileUploads();
    initializeSpeakerExistingFileRemovals();
    initializeSpeakerExistingAttachmentRemovals();

    const selectAll = document.getElementById('select-all');
    const rowCheckboxes = Array.from(document.querySelectorAll('.bo-row-checkbox'));

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowCheckboxes.forEach(function (checkbox) {
                checkbox.checked = selectAll.checked;
            });
        });
    }

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
    if (deleteMultipleButton) {
        deleteMultipleButton.addEventListener('click', async function () {
            const ids = rowCheckboxes.filter(function (checkbox) {
                return checkbox.checked;
            }).map(function (checkbox) {
                return Number(checkbox.value);
            });

            if (ids.length === 0) {
                window.alert('삭제할 Speaker를 선택해주세요.');
                return;
            }

            if (!window.confirm('선택한 ' + ids.length + '명의 Speaker를 삭제하시겠습니까?')) {
                return;
            }

            try {
                await requestJson(deleteMultipleButton.dataset.url, 'POST', { ids: ids });
                window.location.reload();
            } catch (error) {
                // requestJson에서 사용자에게 오류를 안내합니다.
            }
        });
    }

    document.querySelectorAll('.btn-delete-speaker').forEach(function (button) {
        button.addEventListener('click', async function () {
            if (!window.confirm('정말로 삭제하시겠습니까?')) {
                return;
            }

            try {
                await requestJson(button.dataset.url, 'DELETE');
                window.location.reload();
            } catch (error) {
                // requestJson에서 사용자에게 오류를 안내합니다.
            }
        });
    });

});

function initializeSpeakerFileUploads() {
    document.querySelectorAll('[data-speaker-file-upload]').forEach(function (upload) {
        const input = upload.querySelector('.board-file-input');
        const preview = upload.querySelector('[data-speaker-file-preview]');

        if (!input || !preview) return;

        input.addEventListener('change', function () {
            const files = Array.from(input.files || []);
            if (files.length > 0) {
                setSpeakerFiles(input, preview, files);
            } else {
                preview.innerHTML = '';
            }
        });

        upload.addEventListener('dragover', function (event) {
            event.preventDefault();
            upload.classList.add('board-file-drag-over');
        });

        upload.addEventListener('dragleave', function (event) {
            event.preventDefault();
            upload.classList.remove('board-file-drag-over');
        });

        upload.addEventListener('drop', function (event) {
            event.preventDefault();
            upload.classList.remove('board-file-drag-over');

            const droppedFiles = Array.from(event.dataTransfer?.files || []);
            if (droppedFiles.length === 0) return;

            const files = input.multiple
                ? mergeSpeakerFiles(Array.from(input.files || []), droppedFiles)
                : [droppedFiles[0]];
            setSpeakerFiles(input, preview, files);
        });
    });
}

function initializeSpeakerExistingFileRemovals() {
    document.querySelectorAll('[data-speaker-existing-file-remove]').forEach(function (button) {
        button.addEventListener('click', function () {
            const upload = button.closest('[data-speaker-file-upload]');
            const removeInput = upload?.querySelector('[data-speaker-remove-input]');
            const existingFiles = button.closest('.board-existing-files');

            if (!removeInput || !existingFiles) return;

            const message = button.dataset.confirmMessage || '기존 파일을 삭제하시겠습니까?';
            if (!window.confirm(message)) return;

            removeInput.value = '1';
            existingFiles.remove();
        });
    });
}

function initializeSpeakerExistingAttachmentRemovals() {
    document.querySelectorAll('[data-speaker-existing-attachment-remove]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (!window.confirm('기존 첨부파일을 삭제하시겠습니까?')) return;

            const upload = button.closest('[data-speaker-file-upload]');
            const item = button.closest('[data-speaker-existing-attachment]');
            const inputs = upload?.querySelector('[data-speaker-removed-attachment-inputs]');

            if (!upload || !item || !inputs) return;

            const removeInput = document.createElement('input');
            removeInput.type = 'hidden';
            removeInput.name = 'remove_attachments[]';
            removeInput.value = button.dataset.index;
            inputs.appendChild(removeInput);

            const existingCount = Number(upload.dataset.existingCount || 0);
            upload.dataset.existingCount = String(Math.max(0, existingCount - 1));

            const existingFiles = item.closest('.board-existing-files');
            item.remove();
            if (existingFiles && !existingFiles.querySelector('[data-speaker-existing-attachment]')) {
                existingFiles.remove();
            }
        });
    });
}

function setSpeakerFiles(input, preview, files) {
    const maxSize = Number(input.dataset.maxSize || 0);
    const maxFiles = Number(input.dataset.maxFiles || 1);
    const upload = input.closest('[data-speaker-file-upload]');
    const existingCount = Number(upload?.dataset.existingCount || 0);

    if (existingCount + files.length > maxFiles) {
        window.alert('첨부파일은 기존 파일을 포함해 최대 ' + maxFiles + '개까지 등록할 수 있습니다.');
        input.value = '';
        preview.innerHTML = '';
        return;
    }

    if (files.some(function (file) { return !isAcceptedSpeakerFile(input, file); })) {
        window.alert('지원하지 않는 파일 형식입니다.');
        input.value = '';
        preview.innerHTML = '';
        return;
    }

    if (maxSize > 0 && files.some(function (file) { return file.size > maxSize; })) {
        window.alert('각 파일은 ' + Math.floor(maxSize / 1024 / 1024) + 'MB 이하만 등록할 수 있습니다.');
        input.value = '';
        preview.innerHTML = '';
        return;
    }

    const transfer = new DataTransfer();
    files.forEach(function (file) {
        transfer.items.add(file);
    });
    input.files = transfer.files;

    renderSpeakerFilePreviews(input, preview);
}

function mergeSpeakerFiles(currentFiles, addedFiles) {
    return [...currentFiles, ...addedFiles].filter(function (file, index, files) {
        return files.findIndex(function (candidate) {
            return candidate.name === file.name
                && candidate.size === file.size
                && candidate.lastModified === file.lastModified;
        }) === index;
    });
}

function isAcceptedSpeakerFile(input, file) {
    const extensions = (input.accept || '')
        .split(',')
        .map(function (value) {
            return value.trim().toLowerCase();
        })
        .filter(Boolean);

    if (extensions.length === 0) return true;

    const fileName = file.name.toLowerCase();
    return extensions.some(function (extension) {
        return fileName.endsWith(extension);
    });
}

function renderSpeakerFilePreviews(input, preview) {
    preview.innerHTML = '';

    Array.from(input.files || []).forEach(function (file, index) {
        const item = document.createElement('div');
        item.className = 'board-file-item';

        const info = document.createElement('div');
        info.className = 'board-file-info';

        const icon = document.createElement('i');
        icon.className = input.id === 'profile_image' ? 'fas fa-image' : 'fas fa-file';

        const name = document.createElement('span');
        name.className = 'board-file-name';
        name.textContent = file.name;

        const size = document.createElement('span');
        size.className = 'board-file-size';
        size.textContent = '(' + (file.size / 1024 / 1024).toFixed(2) + 'MB)';

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'board-file-remove';
        remove.setAttribute('aria-label', '선택 파일 제거');
        remove.innerHTML = '<i class="fas fa-times"></i>';
        remove.addEventListener('click', function () {
            const remainingFiles = Array.from(input.files || []);
            remainingFiles.splice(index, 1);
            setSpeakerFiles(input, preview, remainingFiles);
        });

        info.append(icon, name, size);
        item.append(info, remove);
        preview.appendChild(item);
    });
}

async function requestJson(url, method, payload) {
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
