document.addEventListener('DOMContentLoaded', function() {
    var header = document.querySelector('.header') || document.querySelector('header');
    var footer = document.querySelector('.footer');
    var goTopBtn = document.querySelector('.gotop');
    var quickMenu = document.querySelector('.quick_menu');
    var langWrap = document.querySelector('.langs');
    var langBtn = langWrap ? langWrap.querySelector('.btn') : null;
    var menuBtn = document.querySelector('.btn_menu');
    var asideMenus = document.querySelectorAll('.svisual .aside .inner > li');

    var initialQuickBottom = quickMenu ? parseFloat(window.getComputedStyle(quickMenu).bottom) || 0 : 0;
    var lastFocusedElement;

    function handleScroll() {
        var scrollY = window.scrollY || window.pageYOffset;

        // 헤더 스크롤
        if (header) {
            if (scrollY > 100) {
                header.classList.add('fixed');
            } else {
                header.classList.remove('fixed');
            }
        }

        // 푸터 스크롤
        if (footer) {
            var footerTop = footer.getBoundingClientRect().top + scrollY;
            var targetPoint = footerTop + initialQuickBottom;
            var windowBottom = scrollY + window.innerHeight;

            if (windowBottom >= targetPoint) {
                footer.classList.add('unfixed');
            } else {
                footer.classList.remove('unfixed');
            }
        }
    }

    // 스크롤 모션
    window.addEventListener('scroll', handleScroll);
    handleScroll();

    if (header) {
        header.addEventListener('mouseenter', function() {
            header.classList.add('hover');
        });
        header.addEventListener('mouseleave', function() {
            header.classList.remove('hover');
        });
    }

    // 맨위로
    if (goTopBtn) {
        goTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // 햄버거 메뉴 토글 & 텍스트 변경
    if (menuBtn && header) {
        menuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            var isOn = header.classList.toggle('on');
            menuBtn.setAttribute('aria-expanded', isOn ? 'true' : 'false');
            menuBtn.childNodes[0].nodeValue = isOn ? '메뉴 닫기' : '메뉴 열림';
        });
    }

    // GNB 마우스 호버 시 Header에 .hover 클래스 토글 (PC 환경)
    if (header) {
        var gnb = header.querySelector('.gnb');
        if (gnb) {
            gnb.addEventListener('mouseenter', function() {
                header.classList.add('hover');
            });
            gnb.addEventListener('mouseleave', function() {
                header.classList.remove('hover');
            });
        }
    }

    // 서브 중앙 메뉴
    asideMenus.forEach(function (li) {
        const btn = li.querySelector('.btn');
        if (!btn) return;
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isActive = li.classList.contains('active');
            asideMenus.forEach(function (item) {
                item.classList.remove('active');
            });
            if (!isActive) {
                li.classList.add('active');
            }
        });
    });
    document.addEventListener('click', function () {
        asideMenus.forEach(function (item) {
            item.classList.remove('active');
        });
    });

    // 모바일 메뉴
    const menuLinks = document.querySelectorAll('.gnb .menu > a');
    const menus = document.querySelectorAll('.header .gnb .menu');
    menus.forEach(function (menu) {
        if (menu.querySelector('.snb') !== null) {
            menu.classList.add('inset');
        }
    });
    menuLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (window.innerWidth <= 1023) {
                const parentMenu = this.parentElement;
                const hasSnb = parentMenu.querySelector('.snb') !== null;
                if (hasSnb) {
                    e.preventDefault();
                    const isOpen = parentMenu.classList.contains('open');
                    document.querySelectorAll('.gnb .menu').forEach(function (m) {
                        m.classList.remove('open');
                        m.classList.remove('on');
                    });
                    if (!isOpen) {
                        parentMenu.classList.add('open');
                    }
                }
            }
        });
    });
    window.addEventListener('resize', function () {
        if (window.innerWidth > 1023) {
            document.querySelectorAll('.gnb .menu').forEach(function (m) {
                m.classList.remove('open');
                m.classList.remove('on');
            });
            // 1023px 초과 시 .header .menus > li 의 open, on 클래스도 원복
            document.querySelectorAll('.header .menus > li').forEach(function (li) {
                li.classList.remove('open');
                li.classList.remove('on');
                const mainLink = li.querySelector('a');
                if (mainLink) mainLink.setAttribute('aria-expanded', 'false');
            });
        }
    });

    // 모바일 1차 메뉴 아코디언 토글 (1023px 이하 / .snb 존재 시)
    const headerNavLis = document.querySelectorAll('.header .menus > li');
    headerNavLis.forEach(function (li) {
        const mainLink = li.querySelector('a');
        const hasSnb = li.querySelector('.snb') !== null;

        if (!mainLink) return;

        mainLink.addEventListener('click', function (e) {
            if (window.innerWidth <= 1023 && hasSnb) {
                e.preventDefault();
                e.stopPropagation();

                const isOpen = li.classList.contains('open') || li.classList.contains('on');

                // 다른 메뉴들의 open과 on 클래스 모두 제거
                headerNavLis.forEach(function (otherLi) {
                    if (otherLi !== li) {
                        otherLi.classList.remove('open');
                        otherLi.classList.remove('on');
                        const otherLink = otherLi.querySelector('a');
                        if (otherLink) otherLink.setAttribute('aria-expanded', 'false');
                    }
                });

                if (isOpen) {
                    li.classList.remove('open');
                    li.classList.remove('on');
                    mainLink.setAttribute('aria-expanded', 'false');
                } else {
                    li.classList.add('open');
                    mainLink.setAttribute('aria-expanded', 'true');
                }
            }
        });
    });

    // 아이폰 체크
    function checkIsIOS() {
        var userAgent = navigator.userAgent || navigator.vendor || window.opera;
        var isIOS = /iPad|iPhone|iPod/.test(userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

        if (isIOS) {
            document.body.classList.add('ios_safe');
        }
    }

    checkIsIOS();

    // 팝업 접근성 관리
    function getFocusableElements(element) {
        return element.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    }

    function showLayer(targetId) {
        if (!/^[a-zA-Z0-9_-]+$/.test(targetId)) return;

        var popup = document.getElementById(targetId);
        if (!popup) return;

        lastFocusedElement = document.activeElement;

        popup.removeAttribute('hidden');
        popup.classList.add('is-active');
        document.body.style.overflow = 'hidden'; // 배경 스크롤 차단

        var inbox = popup.querySelector('.inbox');
        if (inbox) {
            setTimeout(function() { inbox.focus(); }, 50);
        } else {
            var focusableElements = getFocusableElements(popup);
            if (focusableElements.length > 0) {
                setTimeout(function() { focusableElements[0].focus(); }, 50);
            }
        }
    }

    function hideLayer(popup) {
        if (!popup || popup.hasAttribute('hidden')) return;

        popup.classList.remove('is-active');
        popup.setAttribute('hidden', '');
        document.body.style.overflow = ''; // 배경 스크롤 복원

        if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
            setTimeout(function() { lastFocusedElement.focus(); }, 50);
        }
    }

    // 문서 클릭 이벤트 (외부 클릭 닫기 및 팝업)
    document.addEventListener('click', function(e) {
        if (langWrap && !langWrap.contains(e.target)) {
            langWrap.classList.remove('active');
        }

        // [추가] 모바일 1차 메뉴 바깥 영역 클릭 시 open, on 제거
        if (window.innerWidth <= 1023 && !e.target.closest('.header .menus')) {
            headerNavLis.forEach(function (li) {
                li.classList.remove('open');
                li.classList.remove('on');
                const mainLink = li.querySelector('a');
                if (mainLink) mainLink.setAttribute('aria-expanded', 'false');
            });
        }

        var openBtn = e.target.closest('.btn_open');
        if (openBtn) {
            var targetId = openBtn.getAttribute('data-target');
            showLayer(targetId);
            return;
        }

        if (e.target.matches('.dm') || e.target.closest('.btn_close') || e.target.closest('.btn_clo')) {
            var popup = e.target.closest('.popup');
            hideLayer(popup);
        }
    });

    // ESC 키 조작 및 Tab 포커스 트랩
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            if (langWrap) langWrap.classList.remove('active');

            if (header && header.classList.contains('on')) {
                header.classList.remove('on');
                if (menuBtn) {
                    menuBtn.setAttribute('aria-expanded', 'false');
                    menuBtn.childNodes[0].nodeValue = '메뉴 열림';
                    menuBtn.focus();
                }
            }

            var activePopup = document.querySelector('.popup.is-active');
            if (activePopup) {
                hideLayer(activePopup);
                return;
            }
        }

        var activePopup = document.querySelector('.popup.is-active');
        if (activePopup && e.key === 'Tab') {
            var focusables = Array.from(getFocusableElements(activePopup));
            if (focusables.length === 0) return;

            var firstElement = focusables[0];
            var lastElement = focusables[focusables.length - 1];

            if (e.shiftKey && document.activeElement === firstElement) {
                lastElement.focus();
                e.preventDefault();
            } else if (!e.shiftKey && document.activeElement === lastElement) {
                firstElement.focus();
                e.preventDefault();
            }
        }
    });
});