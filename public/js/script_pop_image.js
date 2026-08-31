document.addEventListener('DOMContentLoaded', () => {
    const galleryItems = Array.from(document.querySelectorAll('.gallery_basic li button, .gallery_basic li a, .img_area button, .registration_wrap .btn_confirm'));
    const modal = document.getElementById('modal-pop-image');
    if (!modal || galleryItems.length === 0) return;

    const modalImg = modal.querySelector('#modal-img');
    const modalTitle = modal.querySelector('#modal-title');
    const btnClose = modal.querySelector('.btn_close');
    const btnPrev = modal.querySelector('.showPrev');
    const btnNext = modal.querySelector('.showNext');
    const backdrop = modal.querySelector('.dm');

    let currentIndex = 0;

    // 모달 열기
    function openModal(index) {
        currentIndex = index;
        updateModalContent();

        modal.removeAttribute('hidden');
        modal.classList.add('is-active');

        document.body.style.overflow = 'hidden';

        const inbox = modal.querySelector('.inbox');
        if (inbox) inbox.focus();
    }

    // 모달 닫기
    function closeModal() {
        modal.classList.remove('is-active');
        modal.setAttribute('hidden', '');

        document.body.style.overflow = '';
    }

    // 모달 내 이미지/타이틀 업데이트
    function updateModalContent() {
        const currentItem = galleryItems[currentIndex];
        const imgEl = currentItem.querySelector('img');

        if (imgEl) {
            // 1순위: data-large-src 속성값 사용
            // 2순위: data-large-src가 없을 경우 기존 파일명에 _large 붙여 자동 변환 (예: img_sample.avif -> img_sample_large.avif)
            const thumbSrc = imgEl.getAttribute('src');
            const largeSrc = imgEl.dataset.largeSrc || thumbSrc.replace(/(\.[^/.]+$)/, '_large$1');
            const alt = imgEl.getAttribute('alt') || 'Image Title';

            if (modalImg) {
                modalImg.setAttribute('src', largeSrc);
                modalImg.setAttribute('alt', alt);
            }
            if (modalTitle) {
                modalTitle.textContent = alt;
            }
        }
    }

    // 이전 이미지 이동
    function showPrev() {
        currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
        updateModalContent();
    }

    // 다음 이미지 이동
    function showNext() {
        currentIndex = (currentIndex + 1) % galleryItems.length;
        updateModalContent();
    }

    // 갤러리 아이템 클릭 이벤트
    galleryItems.forEach((item, index) => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            openModal(index);
        });
    });

    // 버튼 및 딤(배경) 클릭 이벤트
    if (btnPrev) btnPrev.addEventListener('click', showPrev);
    if (btnNext) btnNext.addEventListener('click', showNext);
    const closeElements = modal.querySelectorAll('.btn_close, [data-close="true"]');
	closeElements.forEach((el) => {
		el.addEventListener('click', closeModal);
	});

    // 키보드 제어 (Left/Right: 이동, Esc: 닫기)
    document.addEventListener('keydown', (e) => {
        if (!modal.classList.contains('is-active')) return;

        if (e.key === 'Escape') closeModal();
        if (e.key === 'ArrowLeft') showPrev();
        if (e.key === 'ArrowRight') showNext();
    });
});