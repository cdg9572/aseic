/**
 * 새 창으로 열린 일반 팝업을 제어합니다.
 */
(function () {
    'use strict';

    const popup = document.querySelector('.popup-container[data-popup-id]');
    const todayClose = document.getElementById('todayClose');
    const closeButton = document.querySelector('.popup-close-btn');

    if (! popup || ! todayClose || ! closeButton) {
        return;
    }

    function setTodayCookie() {
        const expires = new Date();
        expires.setHours(23, 59, 59, 999);

        let cookie = 'popup_hide_' + popup.dataset.popupId + '=1; expires=' + expires.toUTCString() + '; path=/; SameSite=Lax';
        if (window.location.protocol === 'https:') {
            cookie += '; Secure';
        }

        document.cookie = cookie;
    }

    function isHiddenToday() {
        const cookieName = 'popup_hide_' + popup.dataset.popupId;

        return document.cookie
            .split('; ')
            .some(function (item) {
                return item.startsWith(cookieName + '=');
            });
    }

    function closePopup() {
        if (todayClose.checked) {
            setTodayCookie();
        }

        window.close();

        setTimeout(function () {
            if (! window.closed) {
                window.location.replace('about:blank');
                window.close();
            }
        }, 50);
    }

    if (isHiddenToday()) {
        closePopup();

        return;
    }

    todayClose.addEventListener('change', function () {
        if (todayClose.checked) {
            closePopup();
        }
    });

    closeButton.addEventListener('click', closePopup);

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closePopup();
        }
    });
})();
