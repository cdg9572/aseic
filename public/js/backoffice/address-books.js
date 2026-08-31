document.addEventListener('DOMContentLoaded', function () {
    const management = document.querySelector('[data-address-contact-management]');
    if (!management) return;

    const newRow = management.querySelector('[data-address-contact-new]');
    const emptyRow = management.querySelector('[data-address-contact-empty]');

    management.querySelector('[data-address-contact-add]')?.addEventListener('click', function () {
        if (!newRow) return;
        newRow.hidden = false;
        if (emptyRow) emptyRow.hidden = true;
        newRow.querySelector('[data-address-contact-new-name]')?.focus();
    });

    management.querySelector('[data-address-contact-new-cancel]')?.addEventListener('click', function () {
        if (!newRow) return;
        newRow.querySelectorAll('input:not([type="hidden"])').forEach(function (input) {
            input.value = '';
        });
        newRow.hidden = true;
        if (emptyRow) emptyRow.hidden = false;
    });

    management.querySelectorAll('[data-address-contact-edit-open]').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.dataset.addressContactEditOpen;
            const displayRow = management.querySelector('[data-address-contact-display="' + id + '"]');
            const editRow = management.querySelector('[data-address-contact-edit="' + id + '"]');
            if (!displayRow || !editRow) return;
            displayRow.hidden = true;
            editRow.hidden = false;
            editRow.querySelector('input[name="contact_name"]')?.focus();
        });
    });

    management.querySelectorAll('[data-address-contact-edit-cancel]').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.dataset.addressContactEditCancel;
            const displayRow = management.querySelector('[data-address-contact-display="' + id + '"]');
            const editRow = management.querySelector('[data-address-contact-edit="' + id + '"]');
            if (!displayRow || !editRow) return;
            editRow.hidden = true;
            displayRow.hidden = false;
        });
    });

    management.querySelectorAll('[data-address-contact-delete-form]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm('이 연락처를 삭제하시겠습니까?')) event.preventDefault();
        });
    });
});
