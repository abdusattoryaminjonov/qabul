function isCardFilled(card) {
    const textInputs = card.querySelectorAll('input[type="text"], input[type="email"], input[type="date"], input[type="time"], textarea');
    for (const el of textInputs) {
        if (el.value.trim() !== '') {
            return true;
        }
    }

    const select = card.querySelector('select');
    if (select && select.value !== '') {
        return true;
    }

    const radios = card.querySelectorAll('input[type="radio"]');
    if (radios.length && [...radios].some((r) => r.checked)) {
        return true;
    }

    const checkboxes = card.querySelectorAll('input[type="checkbox"]');
    if (checkboxes.length && [...checkboxes].some((c) => c.checked)) {
        return true;
    }

    const fileInput = card.querySelector('input[type="file"]');
    if (fileInput && fileInput.files.length > 0) {
        return true;
    }

    return false;
}

function clearCardError(card) {
    card.classList.remove('public-field-card--error');
    card.querySelector('.public-field-error-msg')?.remove();
    card.querySelector('.public-q-num--error')?.classList.remove('public-q-num--error');
    card.querySelectorAll('.public-input--error').forEach((el) => el.classList.remove('public-input--error'));
    card.querySelector('.public-file-upload--error')?.classList.remove('public-file-upload--error');
}

function updateErrorBanner() {
    const banner = document.querySelector('.public-form-error');
    if (!banner) {
        return;
    }

    const hasErrors = document.querySelector('.public-field-card--error');
    banner.style.display = hasErrors ? '' : 'none';
}

function checkCard(card) {
    if (card.classList.contains('public-field-card--error') && isCardFilled(card)) {
        clearCardError(card);
        updateErrorBanner();
    }
}

export function initPublicFormValidation() {
    const form = document.querySelector('.public-shell');
    if (!form) {
        return;
    }

    form.addEventListener('input', (event) => {
        const card = event.target.closest('.public-field-card');
        if (card?.classList.contains('public-field-card--error')) {
            checkCard(card);
        }
    });

    form.addEventListener('change', (event) => {
        const card = event.target.closest('.public-field-card');
        if (card?.classList.contains('public-field-card--error')) {
            checkCard(card);
        }
    });

    form.addEventListener('click', (event) => {
        const choice = event.target.closest('.public-choice');
        if (!choice) {
            return;
        }
        const card = choice.closest('.public-field-card');
        if (card?.classList.contains('public-field-card--error')) {
            requestAnimationFrame(() => checkCard(card));
        }
    });
}
