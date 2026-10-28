import * as bootstrap from 'bootstrap';
import '@fortawesome/fontawesome-free/css/all.min.css';

window.bootstrap = bootstrap;

const THEME_KEY = 'tips_theme';

/* Theme ------------------------------------------------------------------ */

function applyTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
    try {
        localStorage.setItem(THEME_KEY, theme);
    } catch (e) {}
}

/* Click actions ---------------------------------------------------------- */

document.addEventListener('click', (event) => {
    const target = event.target.closest(
        '[data-theme-toggle], [data-step-go], [data-password-toggle], [data-captcha-refresh], [data-resend], [data-export-table], [data-table-clear]',
    );

    if (!target) {
        return;
    }

    if (target.matches('[data-theme-toggle]')) {
        applyTheme(document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
    }

    if (target.matches('[data-step-go]')) {
        goToStep(target);
    }

    if (target.matches('[data-password-toggle]')) {
        const input = document.getElementById(target.dataset.passwordToggle);
        const reveal = input.type === 'password';
        input.type = reveal ? 'text' : 'password';
        target.setAttribute('aria-pressed', String(reveal));
        target.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
        target.querySelector('i')?.classList.replace(reveal ? 'fa-eye' : 'fa-eye-slash', reveal ? 'fa-eye-slash' : 'fa-eye');
    }

    if (target.matches('[data-captcha-refresh]')) {
        refreshCaptcha(target);
    }

    if (target.matches('[data-resend]')) {
        toast('We sent you a new code.');
        startResendTimer(target);
    }

    if (target.matches('[data-export-table]')) {
        exportTable(target.dataset.exportTable, target.dataset.exportName);
    }

    if (target.matches('[data-table-clear]')) {
        const tableId = target.dataset.tableClear;
        document.querySelectorAll(`[data-table-search="${tableId}"], [data-table-filter="${tableId}"]`).forEach((field) => {
            field.value = '';
        });
        filterTable(tableId);
    }
});

/* Captcha (the real one is a server-rendered image) ------------------------ */

function refreshCaptcha(button) {
    const a = 2 + Math.floor(Math.random() * 8);
    const b = 1 + Math.floor(Math.random() * a);
    const add = Math.random() < 0.5;
    document.getElementById(button.dataset.captchaRefresh).textContent = `${a} ${add ? '+' : '−'} ${b} = ?`;
    const answer = document.getElementById('captcha');
    answer.value = '';
    answer.focus();
}

/* Forgot-password steps ---------------------------------------------------- */

function fieldsAreValid(container) {
    const fields = [...container.querySelectorAll('input, select, textarea')];
    const firstInvalid = fields.find((field) => !field.checkValidity());

    container.classList.toggle('was-validated', Boolean(firstInvalid));
    firstInvalid?.focus();

    return !firstInvalid;
}

function goToStep(trigger) {
    const container = trigger.closest('[data-steps]');
    const current = trigger.closest('[data-step]');
    const next = trigger.dataset.stepGo;

    if (Number(next) > Number(current.dataset.step) && !fieldsAreValid(current)) {
        return;
    }

    container.querySelectorAll('[data-step]').forEach((panel) => {
        panel.hidden = panel.dataset.step !== next;
    });
    container.querySelectorAll('[data-step-indicator]').forEach((bar) => {
        bar.dataset.active = String(Number(bar.dataset.stepIndicator) <= Number(next));
    });

    const panel = container.querySelector(`[data-step="${next}"]`);

    if (next === '2' && current.dataset.step === '1') {
        const email = document.getElementById('reset_email').value;
        panel.querySelector('[data-masked-email]').textContent = maskEmail(email);
        startResendTimer(panel.querySelector('[data-resend]'));
    }

    panel.querySelector('input')?.focus();
}

/* Steps 1 and 2 aren't forms, so make Enter press the step's main button. */
document.addEventListener('keydown', (event) => {
    if (event.key !== 'Enter' || !event.target.matches('[data-steps] input') || event.target.form) {
        return;
    }

    event.preventDefault();
    event.target.closest('[data-step]').querySelector('.btn-primary[data-step-go]')?.click();
});

function maskEmail(email) {
    const [user, domain] = email.split('@');

    return `${user[0]}${'•'.repeat(Math.max(user.length - 1, 3))}@${domain}`;
}

function startResendTimer(button) {
    let seconds = 60;
    button.disabled = true;
    clearInterval(button.timer);

    const tick = () => {
        if (seconds <= 0) {
            clearInterval(button.timer);
            button.disabled = false;
            button.textContent = 'Resend code';
            return;
        }
        button.textContent = `Resend code in 0:${String(seconds).padStart(2, '0')}`;
        seconds--;
    };

    tick();
    button.timer = setInterval(tick, 1000);
}

/* Live cover-page preview -------------------------------------------------- */

document.addEventListener('input', (event) => {
    const key = event.target.dataset?.preview;

    if (!key) {
        return;
    }

    let value = event.target.value;

    if (event.target.type === 'date' && value) {
        value = new Date(`${value}T00:00:00`).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
    }

    document.querySelectorAll(`[data-preview-target="${key}"]`).forEach((el) => {
        el.textContent = value || el.dataset.placeholder || '';
    });
});

/* <select data-autofill="inputId">: copy the chosen option's data-value into another field. */
document.addEventListener('change', (event) => {
    const targetId = event.target.dataset?.autofill;

    if (!targetId) {
        return;
    }

    const target = document.getElementById(targetId);
    target.value = event.target.selectedOptions[0]?.dataset.value ?? '';
    target.dispatchEvent(new Event('input', { bubbles: true }));
});

/* <select data-option-filter='{"dept":"std_dept"}'>: only show options whose data-dept matches #std_dept. */
document.querySelectorAll('select[data-option-filter]').forEach((select) => {
    const rules = Object.entries(JSON.parse(select.dataset.optionFilter));
    const placeholder = select.querySelector('option[value=""]');

    const update = () => {
        let matches = 0;

        for (const option of select.options) {
            if (option === placeholder) {
                continue;
            }

            const visible = rules.every(([attribute, sourceId]) => {
                const wanted = document.getElementById(sourceId).value;

                return !wanted || option.dataset[attribute] === wanted;
            });

            option.hidden = !visible;
            option.disabled = !visible;
            matches += visible ? 1 : 0;
        }

        if (select.selectedOptions[0]?.disabled) {
            select.value = '';
            select.dispatchEvent(new Event('input', { bubbles: true }));
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }

        placeholder.textContent = matches ? placeholder.dataset.label : placeholder.dataset.empty;
    };

    rules.forEach(([, sourceId]) => document.getElementById(sourceId).addEventListener('change', update));
    select.addEventListener('refresh', update);
    update();
});

document.addEventListener('reset', (event) => {
    event.target.classList.remove('was-validated');

    setTimeout(() => {
        event.target.querySelectorAll('select[data-option-filter]').forEach((select) => select.dispatchEvent(new Event('refresh')));
        event.target.querySelectorAll('[data-preview]').forEach((field) => field.dispatchEvent(new Event('input', { bubbles: true })));
    });
});

/* <input data-match="otherId">: must equal the other field. */
document.addEventListener('input', (event) => {
    const matchId = event.target.dataset?.match;
    const confirmations = matchId
        ? [event.target]
        : [...document.querySelectorAll(`[data-match="${event.target.id}"]`)];

    confirmations.forEach((field) => {
        const original = document.getElementById(field.dataset.match);
        field.setCustomValidity(field.value && field.value !== original.value ? 'mismatch' : '');
    });
});

/* Tables: search, filters, empty result -------------------------------------- */

function filterTable(tableId) {
    const table = document.getElementById(tableId);
    const query = (document.querySelector(`[data-table-search="${tableId}"]`)?.value ?? '').trim().toLowerCase();
    const filters = [...document.querySelectorAll(`[data-table-filter="${tableId}"]`)].filter((select) => select.value);
    const rows = [...table.querySelectorAll('tbody tr:not([data-empty-row]):not([data-no-results])')];

    if (!rows.length) {
        return;
    }

    let shown = 0;

    rows.forEach((row) => {
        const visible =
            (!query || row.textContent.toLowerCase().includes(query)) &&
            filters.every((select) => row.dataset[select.dataset.filterKey] === select.value);
        row.hidden = !visible;
        shown += visible ? 1 : 0;
    });

    let noResults = table.querySelector('tr[data-no-results]');

    if (!noResults) {
        noResults = document.createElement('tr');
        noResults.dataset.noResults = '';
        noResults.innerHTML = `
            <td colspan="${table.querySelectorAll('thead th').length}" class="text-center text-body-secondary py-5">
                <p class="mb-2">Nothing matches your search.</p>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-table-clear="${tableId}">Clear search</button>
            </td>`;
        table.querySelector('tbody').append(noResults);
    }

    noResults.hidden = shown > 0;
    document.querySelectorAll(`[data-table-count="${tableId}"]`).forEach((el) => {
        el.textContent = shown;
    });
}

document.addEventListener('input', (event) => {
    if (event.target.dataset?.tableSearch) {
        filterTable(event.target.dataset.tableSearch);
    }
});

document.addEventListener('change', (event) => {
    if (event.target.dataset?.tableFilter) {
        filterTable(event.target.dataset.tableFilter);
    }
});

function exportTable(tableId, name = 'export') {
    const table = document.getElementById(tableId);
    const keep = (cell) => !cell.hasAttribute('data-export-skip');
    const text = (cell) => (cell.dataset.export ?? cell.innerText).replace(/\s+/g, ' ').trim();
    const escape = (value) => (/[",\n]/.test(value) ? `"${value.replace(/"/g, '""')}"` : value);

    const header = [...table.querySelectorAll('thead th')].filter(keep).map(text);
    const rows = [...table.querySelectorAll('tbody tr:not([hidden]):not([data-empty-row]):not([data-no-results])')].map((row) =>
        [...row.cells].filter(keep).map(text),
    );

    if (!rows.length) {
        toast('There are no rows to export.', 'error');
        return;
    }

    const csv = [header, ...rows].map((row) => row.map(escape).join(',')).join('\r\n');
    const link = document.createElement('a');
    link.href = URL.createObjectURL(new Blob([`﻿${csv}`], { type: 'text/csv;charset=utf-8' }));
    link.download = `${name}-${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();
    URL.revokeObjectURL(link.href);

    toast(`Exported ${rows.length} ${rows.length === 1 ? 'row' : 'rows'}.`);
}

/* Modals: fill from the clicked row, focus the first field ----------------- */

document.addEventListener('show.bs.modal', (event) => {
    const modal = event.target;
    const form = modal.querySelector('form');
    const record = event.relatedTarget?.dataset.record;

    if (!record) {
        form?.reset();
        return;
    }

    form?.classList.remove('was-validated');

    for (const [key, value] of Object.entries(JSON.parse(record))) {
        modal.querySelectorAll(`[name="${key}"]`).forEach((field) => {
            if (field.type === 'radio') {
                field.checked = field.value === value;
            } else {
                field.value = value;
            }
        });
        modal.querySelectorAll(`[data-record-text="${key}"]`).forEach((el) => {
            el.textContent = value;
        });
    }
});

document.addEventListener('shown.bs.modal', (event) => {
    const modal = event.target;
    const field = modal.querySelector('.modal-body :is(input:not([type=hidden]):not([type=radio]):not(:disabled), select, textarea)');

    (field ?? modal.querySelector('.modal-footer [data-bs-dismiss]'))?.focus();
});

/* Forms: validation, loading state, success message ----------------------- */

function setBusy(button, busy) {
    if (!button) {
        return;
    }

    if (busy) {
        button.dataset.label = button.innerHTML;
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.innerHTML = `<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span>${button.dataset.busy ?? 'Saving…'}`;
    } else {
        button.disabled = false;
        button.removeAttribute('aria-busy');
        button.innerHTML = button.dataset.label;
    }
}

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (form.noValidate && !form.checkValidity()) {
        event.preventDefault();
        form.classList.add('was-validated');
        form.querySelector(':invalid')?.focus();
        return;
    }

    if (form.matches('[data-print-form]')) {
        event.preventDefault();
        window.print();
        return;
    }

    /* Design-only build: these forms have no backend yet. */
    if (!form.matches('[data-demo-form]')) {
        return;
    }

    event.preventDefault();

    const button = event.submitter ?? form.querySelector('[type=submit]');
    setBusy(button, true);

    setTimeout(() => {
        if (form.dataset.redirect) {
            window.location.assign(form.dataset.redirect);
            return;
        }

        setBusy(button, false);
        form.classList.remove('was-validated');

        const modal = form.closest('.modal');

        if (modal) {
            bootstrap.Modal.getInstance(modal)?.hide();
        }

        if (form.dataset.demoForm) {
            toast(form.dataset.demoForm);
        }
    }, 700);
});

/* Profile photo: check type and size before showing it. */
document.addEventListener('change', (event) => {
    const input = event.target;

    if (!input.matches('[data-photo-input]') || !input.files.length) {
        return;
    }

    const file = input.files[0];
    const error = document.getElementById(input.dataset.photoInput);
    const allowed = ['image/jpeg', 'image/png', 'image/webp'];

    error.textContent = !allowed.includes(file.type)
        ? 'Use a JPG, PNG or WebP image.'
        : file.size > 2 * 1024 * 1024
          ? 'That image is larger than 2 MB.'
          : '';
    error.hidden = !error.textContent;

    if (error.textContent) {
        input.value = '';
        return;
    }

    const image = document.createElement('img');
    image.alt = '';
    image.src = URL.createObjectURL(file);
    document.querySelector('[data-photo-preview]').replaceChildren(image);
    toast('Photo updated.');
});

/* Printing: copy the live preview into a top-level element (see app.css). */
window.addEventListener('beforeprint', () => {
    const paper = document.getElementById('contentToPrint');

    if (!paper) {
        return;
    }

    let root = document.getElementById('print-root');

    if (!root) {
        root = document.createElement('div');
        root.id = 'print-root';
        document.body.append(root);
    }

    const copy = paper.cloneNode(true);
    copy.removeAttribute('id');
    root.replaceChildren(copy);
});

/* Toasts ------------------------------------------------------------------ */

export function toast(message, type = 'success') {
    let stack = document.getElementById('toast-stack');

    if (!stack) {
        stack = document.createElement('div');
        stack.id = 'toast-stack';
        stack.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        stack.setAttribute('aria-live', 'polite');
        document.body.append(stack);
    }

    const icon = type === 'error' ? 'fa-circle-exclamation text-danger' : 'fa-circle-check text-success';
    const item = document.createElement('div');
    item.className = 'toast';
    item.setAttribute('role', type === 'error' ? 'alert' : 'status');
    item.innerHTML = `
        <div class="d-flex align-items-start">
            <div class="toast-body d-flex gap-2"><i class="fa-solid ${icon} mt-1" aria-hidden="true"></i><span></span></div>
            <button type="button" class="btn-close me-2 mt-2" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>`;
    item.querySelector('.toast-body span').textContent = message;
    stack.append(item);

    item.addEventListener('hidden.bs.toast', () => item.remove());
    bootstrap.Toast.getOrCreateInstance(item, { delay: 4000 }).show();
}

/* Flash messages rendered by the server as <template data-toast="success">. */
document.querySelectorAll('template[data-toast]').forEach((template) => {
    toast(template.content.textContent.trim(), template.dataset.toast);
});
