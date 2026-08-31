document.addEventListener('DOMContentLoaded', function () {
	const tabList = document.querySelector('.tabs');
	if (!tabList) return;

	const tabs = tabList.querySelectorAll('[role="tab"]');
	const panels = document.querySelectorAll('[role="tabpanel"]');

	tabs.forEach(function (tab) {
		tab.addEventListener('click', function () {
			tabs.forEach(function (t) {
				t.setAttribute('aria-selected', 'false');
				t.setAttribute('tabindex', '-1');
			});

			tab.setAttribute('aria-selected', 'true');
			tab.removeAttribute('tabindex');

			const targetPanelId = tab.getAttribute('aria-controls');

			panels.forEach(function (panel) {
				if (panel.id === targetPanelId) {
					panel.removeAttribute('hidden');
					// 탭 클릭 시 해당 패널의 iframe이 아직 안 읽혔을 경우 즉시 로드
					const iframe = panel.querySelector('iframe[data-src]');
					if (iframe) {
						iframe.setAttribute('src', iframe.getAttribute('data-src'));
						iframe.removeAttribute('data-src');
					}
				} else {
					panel.setAttribute('hidden', '');
				}
			});
		});

		tab.addEventListener('keydown', function (e) {
			const tabsArray = Array.from(tabs);
			const index = tabsArray.indexOf(this);

			let newIndex = null;
			if (e.key === 'ArrowRight') {
				newIndex = (index + 1) % tabsArray.length;
			} else if (e.key === 'ArrowLeft') {
				newIndex = (index - 1 + tabsArray.length) % tabsArray.length;
			}

			if (newIndex !== null) {
				tabsArray[newIndex].focus();
				tabsArray[newIndex].click();
				e.preventDefault();
			}
		});
	});
});