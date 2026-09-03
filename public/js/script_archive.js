document.addEventListener('DOMContentLoaded', function () {
	const selectArea = document.querySelector('.years_select_tab');
	if (!selectArea) return;

	const tabsContainer = selectArea.querySelector('.tabs');
	const prevBtn = selectArea.querySelector('.arrow.prev');
	const nextBtn = selectArea.querySelector('.arrow.next');
	const tabButtons = Array.from(selectArea.querySelectorAll('.tabs button'));

	if (tabButtons.length === 0 || !tabsContainer) return;

	let activeIndex = tabButtons.findIndex((button) => button.getAttribute('aria-selected') === 'true');
	if (activeIndex < 0) activeIndex = tabButtons.length - 1;

	// 탭 활성화 및 중앙 스크롤 이동
	function setActiveTab(index) {
		if (index < 0) index = 0;
		if (index >= tabButtons.length) index = tabButtons.length - 1;

		activeIndex = index;

		tabButtons.forEach((btn, idx) => {
			const li = btn.closest('li');
			if (idx === activeIndex) {
				btn.classList.add('active');
				btn.setAttribute('aria-selected', 'true');
				if (li) li.classList.add('active');

				btn.scrollIntoView({
					behavior: 'smooth',
					block: 'nearest',
					inline: 'center'
				});
			} else {
				btn.classList.remove('active');
				btn.setAttribute('aria-selected', 'false');
				if (li) li.classList.remove('active');
			}
		});

		if (prevBtn) prevBtn.disabled = activeIndex === 0;
		if (nextBtn) nextBtn.disabled = activeIndex === tabButtons.length - 1;
	}

	// 1. 초기 탭 선택 및 화면 이동
	requestAnimationFrame(() => {
		setActiveTab(activeIndex);
	});

	// 2. 탭 클릭 이벤트
	tabButtons.forEach((btn, idx) => {
		btn.addEventListener('click', function () {
			setActiveTab(idx);
		});
	});

	// 3. 이전/다음 화살표 클릭
	if (prevBtn) {
		prevBtn.addEventListener('click', function () {
			if (activeIndex > 0) tabButtons[activeIndex - 1].click();
		});
	}

	if (nextBtn) {
		nextBtn.addEventListener('click', function () {
			if (activeIndex < tabButtons.length - 1) tabButtons[activeIndex + 1].click();
		});
	}

	// 4. 마우스 드래그 스와이프 구현
	let isDown = false;
	let startX;
	let scrollLeft;

	tabsContainer.addEventListener('mousedown', (e) => {
		isDown = true;
		tabsContainer.classList.add('dragging');
		startX = e.pageX - tabsContainer.offsetLeft;
		scrollLeft = tabsContainer.scrollLeft;
	});

	tabsContainer.addEventListener('mouseleave', () => {
		isDown = false;
		tabsContainer.classList.remove('dragging');
	});

	tabsContainer.addEventListener('mouseup', () => {
		isDown = false;
		tabsContainer.classList.remove('dragging');
	});

	tabsContainer.addEventListener('mousemove', (e) => {
		if (!isDown) return;
		e.preventDefault();
		const x = e.pageX - tabsContainer.offsetLeft;
		const walk = (x - startX) * 1.5; // 스크롤 속도 조절
		tabsContainer.scrollLeft = scrollLeft - walk;
	});
});
