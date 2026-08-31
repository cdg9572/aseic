document.addEventListener('DOMContentLoaded', function () {
    const list = document.querySelector('[data-programme-book-list]');
    const template = document.querySelector('[data-programme-book-template]');
    const addButton = document.querySelector('[data-programme-book-add]');
    const removeButton = document.querySelector('[data-programme-book-remove]');

    if (!list || !template || !addButton || !removeButton) return;

    list.querySelectorAll('[data-programme-book-item]').forEach(initializeProgrammeBookItem);

    addButton.addEventListener('click', function () {
        const index = Number(list.dataset.nextIndex || list.children.length);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(index)).trim();
        const item = wrapper.firstElementChild;

        if (!item) return;

        list.appendChild(item);
        list.dataset.nextIndex = String(index + 1);
        initializeProgrammeBookItem(item);
        item.querySelector('input[type="text"]')?.focus();
    });

    removeButton.addEventListener('click', function () {
        const items = list.querySelectorAll('[data-programme-book-item]');
        if (items.length <= 1) {
            window.alert('Programme Book 항목은 한 개 이상 필요합니다.');
            return;
        }

        const item = items[items.length - 1];
        const persistedItem = item.querySelector('input[name$="[id]"]');
        if (persistedItem && !window.confirm('마지막 Programme Book 항목을 삭제하시겠습니까?')) return;

        item.remove();
    });
});

function initializeProgrammeBookItem(item) {
    if (item.dataset.programmeBookInitialized === 'true') return;
    item.dataset.programmeBookInitialized = 'true';

    const upload = item.querySelector('[data-programme-book-upload]');
    const input = upload?.querySelector('.board-file-input');
    const preview = upload?.querySelector('[data-programme-book-preview]');
    const existingRemove = upload?.querySelector('[data-programme-book-existing-remove]');
    const removeFileInput = upload?.querySelector('[data-programme-book-remove-file]');

    if (!upload || !input || !preview) return;

    input.addEventListener('change', function () {
        const file = input.files?.[0];
        file ? setProgrammeBookFile(input, preview, file) : clearProgrammeBookPreview(preview);
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
        const file = event.dataTransfer?.files?.[0];
        if (file) setProgrammeBookFile(input, preview, file);
    });

    existingRemove?.addEventListener('click', function () {
        if (!removeFileInput || !window.confirm('기존 파일을 삭제하시겠습니까?')) return;

        removeFileInput.value = '1';
        existingRemove.closest('.board-existing-files')?.remove();
    });
}

function setProgrammeBookFile(input, preview, file) {
    if (!isAcceptedProgrammeBookFile(input, file)) {
        window.alert('지원하지 않는 파일 형식입니다.');
        input.value = '';
        clearProgrammeBookPreview(preview);
        return;
    }

    const maxSize = Number(input.dataset.maxSize || 0);
    if (maxSize > 0 && file.size > maxSize) {
        window.alert('파일은 ' + Math.floor(maxSize / 1024 / 1024) + 'MB 이하만 등록할 수 있습니다.');
        input.value = '';
        clearProgrammeBookPreview(preview);
        return;
    }

    const transfer = new DataTransfer();
    transfer.items.add(file);
    input.files = transfer.files;
    renderProgrammeBookPreview(input, preview, file);
}

function isAcceptedProgrammeBookFile(input, file) {
    const extensions = (input.accept || '')
        .split(',')
        .map(function (extension) { return extension.trim().toLowerCase(); })
        .filter(Boolean);
    const fileName = file.name.toLowerCase();

    return extensions.length === 0 || extensions.some(function (extension) {
        return fileName.endsWith(extension);
    });
}

function renderProgrammeBookPreview(input, preview, file) {
    clearProgrammeBookPreview(preview);

    const item = document.createElement('div');
    item.className = 'board-file-item';

    const info = document.createElement('div');
    info.className = 'board-file-info';

    const icon = document.createElement('i');
    icon.className = 'fas fa-file';

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
        input.value = '';
        clearProgrammeBookPreview(preview);
    });

    info.append(icon, name, size);
    item.append(info, remove);
    preview.appendChild(item);
}

function clearProgrammeBookPreview(preview) {
    preview.replaceChildren();
}
