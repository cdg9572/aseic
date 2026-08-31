document.addEventListener('DOMContentLoaded', function () {
	// 독립형 아코디언 토글 기능
	const accordionButtons = document.querySelectorAll('.con.indetail .btn_accordion_toggle');

	accordionButtons.forEach(function (button) {
		button.addEventListener('click', function () {
			const isExpanded = this.getAttribute('aria-expanded') === 'true';
			const targetId = this.getAttribute('aria-controls');
			const targetDetail = document.getElementById(targetId);
			const parentCon = this.closest('.con.indetail');

			if (isExpanded) {
				// 닫기
				this.setAttribute('aria-expanded', 'false');
				if (targetDetail) targetDetail.setAttribute('hidden', '');
				if (parentCon) parentCon.classList.remove('active');
			} else {
				// 열기 (타 항목 상태 유지)
				this.setAttribute('aria-expanded', 'true');
				if (targetDetail) targetDetail.removeAttribute('hidden');
				if (parentCon) parentCon.classList.add('active');
			}
		});
	});
});