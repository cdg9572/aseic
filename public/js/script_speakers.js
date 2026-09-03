document.addEventListener('DOMContentLoaded', function () {
	const modal = document.getElementById('modal-steering');
	if (!modal) return;

	const modalImg = document.getElementById('modal-img');
	const modalTitle = document.getElementById('modal-title');
	const modalType = document.getElementById('type') || modal.querySelector('.type');
	const modalName = document.getElementById('name');
	const modalPosition = document.getElementById('position');
	const modalAffiliation = document.getElementById('affiliation');
	const modalLinkedIn = document.getElementById('modal-linkedin');
	const modalDownload = modal.querySelector('.btn_download');
	const modalBio = document.getElementById('modal-bio');
	const closeBtn = modal.querySelector('.btn_close');
	const prevBtn = modal.querySelector('.showPrev');
	const nextBtn = modal.querySelector('.showNext');
	const overlay = modal.querySelector('.dm');

	let buttons = [];
	let currentIndex = -1;
	let lastActiveElement = null;

	function updateButtonsList() {
		buttons = Array.from(document.querySelectorAll('.btn-open-modal'));
	}

	// slug 변환 함수 (JavaScript용)
	function slugify(text) {
		return text.toString().toLowerCase().trim()
			.replace(/\s+/g, '-')           // 공백을 - 로 변환
			.replace(/[^\w\-]+/g, '')       // 불필요한 문자는 제거
			.replace(/\-\-+/g, '-');        // 연속된 - 를 하나로 축소
	}

	function renderModalData(index) {
		const button = buttons[index];
		if (!button) return;

		const img = button.getAttribute('data-img');
		const type = button.getAttribute('data-type');
		const name = button.getAttribute('data-name');
		const position = button.getAttribute('data-position');
		const affiliation = button.getAttribute('data-affiliation');
		const bio = button.getAttribute('data-bio');
		const linkedIn = button.getAttribute('data-link');
		const file = button.getAttribute('data-file'); // 다운로드 파일 경로

		if (modalImg) {
			if (img && img.trim() !== '') {
				modalImg.setAttribute('src', img);
			} else {
				modalImg.removeAttribute('src');
			}
			modalImg.alt = name || '';
		}
		if (modalTitle) modalTitle.textContent = 'Member Detail - ' + (name || '');
		if (modalName) modalName.textContent = name || '';
		if (modalPosition) modalPosition.textContent = position || '';
		if (modalAffiliation) modalAffiliation.textContent = affiliation || '';
		if (modalBio) modalBio.innerHTML = bio || '';
		if (modalLinkedIn) {
			if (linkedIn && linkedIn.trim() !== '') {
				modalLinkedIn.setAttribute('href', linkedIn);
				modalLinkedIn.removeAttribute('hidden');
			} else {
				modalLinkedIn.removeAttribute('href');
				modalLinkedIn.setAttribute('hidden', '');
			}
		}

		// 1. Type 동적 출력 및 클래스 바인딩 (e.g. type-start-up)
		if (modalType) {
			if (type && type.trim() !== '') {
				modalType.textContent = type;
				modalType.className = 'type type-' + slugify(type);
				modalType.style.display = '';
			} else {
				modalType.textContent = '';
				modalType.className = 'type';
				modalType.style.display = 'none';
			}
		}

		// 2. 다운로드 링크 동적 바인딩 및 노출 제어
		if (modalDownload) {
			if (file && file.trim() !== '' && file !== '#' && file !== '#this') {
				modalDownload.setAttribute('href', file);
				modalDownload.setAttribute('download', ''); // 클릭 시 바로 다운로드
				modalDownload.style.display = 'inline-block'; // 명시적으로 노출 (디자인에 따라 block 등으로 변경 가능)
			} else {
				modalDownload.removeAttribute('href');
				modalDownload.removeAttribute('download');
				modalDownload.style.display = 'none'; // 파일이 없으면 숨김
			}
		}

		currentIndex = index;
	}

	function openModal(button) {
		updateButtonsList();
		lastActiveElement = button;
		const index = buttons.indexOf(button);

		renderModalData(index);

		modal.removeAttribute('hidden');
		modal.classList.add('is-active');
		document.body.style.overflow = 'hidden';
		if (closeBtn) closeBtn.focus();
	}

	function closeModal() {
		if (modal.hasAttribute('hidden')) return;

		modal.setAttribute('hidden', '');
		modal.classList.remove('is-active');
		document.body.style.overflow = '';

		if (lastActiveElement) {
			lastActiveElement.focus();
		}
	}

	function showPrev() {
		if (buttons.length === 0) return;
		let newIndex = currentIndex - 1;
		if (newIndex < 0) {
			newIndex = buttons.length - 1;
		}
		renderModalData(newIndex);
	}

	function showNext() {
		if (buttons.length === 0) return;
		let newIndex = currentIndex + 1;
		if (newIndex >= buttons.length) {
			newIndex = 0;
		}
		renderModalData(newIndex);
	}

	modal.addEventListener('keydown', function (e) {
		if (e.key === 'ArrowLeft') {
			showPrev();
		} else if (e.key === 'ArrowRight') {
			showNext();
		} else if (e.key === 'Tab') {
			const focusables = modal.querySelectorAll('button:not([disabled]), [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
			const visibleFocusables = Array.from(focusables).filter(el => el.offsetWidth > 0 || el.offsetHeight > 0);
			if (visibleFocusables.length === 0) return;

			const firstEl = visibleFocusables[0];
			const lastEl = visibleFocusables[visibleFocusables.length - 1];

			if (e.shiftKey && document.activeElement === firstEl) {
				lastEl.focus();
				e.preventDefault();
			} else if (!e.shiftKey && document.activeElement === lastEl) {
				firstEl.focus();
				e.preventDefault();
			}
		} else if (e.key === 'Escape') {
			closeModal();
		}
	});

	document.querySelectorAll('.btn-open-modal').forEach(function (btn) {
		btn.addEventListener('click', function () {
			openModal(this);
		});
	});

	if (closeBtn) closeBtn.addEventListener('click', closeModal);
	if (overlay) overlay.addEventListener('click', closeModal);
	if (prevBtn) prevBtn.addEventListener('click', showPrev);
	if (nextBtn) nextBtn.addEventListener('click', showNext);
});
