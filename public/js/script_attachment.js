document.addEventListener('DOMContentLoaded', () => {
    const attachmentArea = document.querySelector('.attachment_area');
    const inputArea = attachmentArea.querySelector('.input_area');
    const fileInput = inputArea.querySelector('input[type="file"]');
    const fileArea = attachmentArea.querySelector('.file_area');
    const dataTransfer = new DataTransfer();
    fileArea.innerHTML = '';
    const processFiles = (newFiles) => {
        const MAX_FILES = 5, MAX_SIZE = 10 * 1024 * 1024;
        Array.from(newFiles).forEach(file => {
            if (dataTransfer.items.length >= MAX_FILES) return alert(`최대 ${MAX_FILES}개까지만 파일 등록이 가능합니다.`);
            if (file.size > MAX_SIZE) return alert(`파일 용량이 10MB를 초과합니다: ${file.name}`);
            if (Array.from(dataTransfer.files).some(f => f.name === file.name && f.size === file.size)) return alert(`이미 첨부된 파일입니다: ${file.name}`);
            dataTransfer.items.add(file);
        });
        fileInput.files = dataTransfer.files;
        renderFileList();
    };
    const renderFileList = () => {
        fileArea.innerHTML = '';
        const files = Array.from(dataTransfer.files);
        fileArea.classList.toggle('inset', files.length > 0);
        files.forEach((file, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.setAttribute('aria-label', `${file.name} 삭제`);
            button.innerHTML = `<strong>첨부파일</strong><p>${file.name}</p>`;
            button.addEventListener('click', () => removeFile(index));
            fileArea.appendChild(button);
        });
    };
    const removeFile = (index) => {
        dataTransfer.items.remove(index);
        fileInput.files = dataTransfer.files;
        renderFileList();
    };
    fileInput.addEventListener('change', (e) => processFiles(e.target.files));
    inputArea.addEventListener('dragover', (e) => { e.preventDefault(); inputArea.classList.add('drag_over'); });
    inputArea.addEventListener('dragleave', (e) => { e.preventDefault(); inputArea.classList.remove('drag_over'); });
    inputArea.addEventListener('drop', (e) => {
        e.preventDefault();
        inputArea.classList.remove('drag_over');
        if (e.dataTransfer.files?.length) processFiles(e.dataTransfer.files);
    });
});