document.addEventListener('DOMContentLoaded', function () {
	const registrationForm = document.getElementById('form-registration');
	const privacyAgree = document.getElementById('privacy_agree');

	if (registrationForm && privacyAgree) {
		privacyAgree.addEventListener('invalid', function (event) {
			if (registrationForm.querySelector(':invalid') !== privacyAgree) return;

			event.preventDefault();
			window.alert('Please agree to the Privacy Policy.');
			privacyAgree.focus();
		});
	}

	const modal = document.querySelector('[data-registration-result-modal]');
	if (!modal || !modal.classList.contains('is-active')) return;

	document.body.style.overflow = 'hidden';
	const closeModal = function () {
		modal.classList.remove('is-active');
		modal.setAttribute('hidden', '');
		document.body.style.overflow = '';
	};

	modal.querySelectorAll('.btn_close, [data-close="true"]').forEach(function (element) {
		element.addEventListener('click', closeModal);
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape' && modal.classList.contains('is-active')) closeModal();
	});
});
