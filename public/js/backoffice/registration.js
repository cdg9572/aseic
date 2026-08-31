document.addEventListener('DOMContentLoaded', function () {
    const modeInputs = document.querySelectorAll('[data-registration-mode]');
    const participating = document.querySelector('[data-registration-participating]');
    const closed = document.querySelector('[data-registration-closed]');
    const customEnd = document.querySelector('[data-registration-custom-end]');
    const endText = document.querySelector('[data-registration-end-text]');

    function refreshMode() {
        const mode = document.querySelector('[data-registration-mode]:checked')?.value;
        if (participating) participating.hidden = mode !== 'participating';
        if (closed) closed.hidden = mode !== 'not_participating';
    }

    function refreshEndText() {
        if (endText) endText.disabled = !customEnd?.checked;
    }

    modeInputs.forEach(function (input) { input.addEventListener('change', refreshMode); });
    customEnd?.addEventListener('change', refreshEndText);
    refreshMode();
    refreshEndText();
});
