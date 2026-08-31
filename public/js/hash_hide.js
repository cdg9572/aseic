document.querySelectorAll('.labels a[href^="#"]').forEach(link => {
	link.addEventListener('click', function(e) {
		e.preventDefault();
		const targetId = this.getAttribute('href');
		const targetEl = document.querySelector(targetId);
		if (!targetEl) return;
		const headerHeight = document.querySelector('header')?.offsetHeight || 0;
		const scrollContainer = this.closest('.scroll');
		if (scrollContainer) {
			const containerTop = scrollContainer.getBoundingClientRect().top;
			const elementTop = targetEl.getBoundingClientRect().top;
			scrollContainer.scrollTo({ top: scrollContainer.scrollTop + (elementTop - containerTop) - 20, behavior: 'smooth' });
		} else {
			const elementPosition = targetEl.getBoundingClientRect().top + window.pageYOffset;
			window.scrollTo({ top: elementPosition - headerHeight - 20, behavior: 'smooth' });
		}
		history.pushState("", document.title, window.location.pathname + window.location.search);
	});
});