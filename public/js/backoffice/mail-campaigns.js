document.addEventListener('DOMContentLoaded', function () {
    const targets = document.querySelectorAll('[data-mail-target]');
    const addressBooks = document.querySelector('[data-mail-address-books]');
    const direct = document.querySelector('[data-mail-direct]');

    function refreshTarget() {
        const target = document.querySelector('[data-mail-target]:checked')?.value;
        if (addressBooks) addressBooks.hidden = target !== 'address_book';
        if (direct) direct.hidden = target !== 'direct';
    }

    targets.forEach(function (target) { target.addEventListener('change', refreshTarget); });
    refreshTarget();

    document.querySelectorAll('[data-mail-send-form]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm('이 메일을 수신자에게 발송하시겠습니까?')) event.preventDefault();
        });
    });
});
