/**
 * 메인 화면의 일반 팝업과 레이어 팝업을 제어합니다.
 */
(function () {
    'use strict';

    const cookiePrefix = 'popup_hide_';

    function getCookie(name) {
        const cookie = document.cookie
            .split('; ')
            .find(function (item) {
                return item.startsWith(name + '=');
            });

        return cookie ? cookie.substring(name.length + 1) : null;
    }

    function setTodayCookie(popupId) {
        const expires = new Date();
        expires.setHours(23, 59, 59, 999);

        let cookie = cookiePrefix + popupId + '=1; expires=' + expires.toUTCString() + '; path=/; SameSite=Lax';
        if (window.location.protocol === 'https:') {
            cookie += '; Secure';
        }

        document.cookie = cookie;
    }

    function isHiddenToday(popupId) {
        return getCookie(cookiePrefix + popupId) !== null;
    }

    function closeLayerPopup(popupId) {
        const popup = document.getElementById('popup-' + popupId);
        if (popup) {
            popup.classList.add('hidden');
        }
    }

    function initializeWindowPopups() {
        document.querySelectorAll('[data-popup-window]').forEach(function (trigger) {
            const popupId = trigger.dataset.popupId;
            if (isHiddenToday(popupId)) {
                return;
            }

            window.open(
                trigger.dataset.popupUrl,
                trigger.dataset.popupName,
                trigger.dataset.popupFeatures
            );
        });
    }

    function initializeLayerPopups() {
        document.querySelectorAll('.popup-layer[data-display-type="layer"]').forEach(function (popup) {
            if (isHiddenToday(popup.dataset.popupId)) {
                popup.classList.add('hidden');
            }
        });

        document.addEventListener('click', function (event) {
            const closeButton = event.target.closest('.popup-footer-close-btn');
            if (closeButton) {
                closeLayerPopup(closeButton.dataset.popupId);
            }
        });

        document.addEventListener('change', function (event) {
            if (! event.target.matches('.popup-today-close') || ! event.target.checked) {
                return;
            }

            const popupId = event.target.dataset.popupId;
            setTodayCookie(popupId);
            closeLayerPopup(popupId);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initializeWindowPopups();
        initializeLayerPopups();
    });
})();
