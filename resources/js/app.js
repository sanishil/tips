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

/* Intro loader (cover page) ----------------------------------------------- */

const intro = document.querySelector('[data-intro]');
let introTimeline;

/*
| Logo pops in, the institute name types out, then the logo flies into the
| header's logo while the white overlay fades away. GSAP is only loaded here.
*/
async function playIntro() {
    intro.style.animation = 'none';

    let gsap;

    try {
        ({ gsap } = await import('gsap'));
    } catch (e) {
        intro.hidden = true;
        return;
    }

    const logo = intro.querySelector('[data-intro-logo]');
    const typed = intro.querySelector('[data-intro-typed]');
    const caret = intro.querySelector('[data-intro-caret]');
    const subtitle = intro.querySelector('[data-intro-subtitle]');
    const bar = intro.querySelector('[data-intro-bar]');
    const parts = intro.querySelectorAll('.intro-loader-name, [data-intro-subtitle], .intro-loader-bar');
    const headerLogo = document.querySelector('[data-intro-replay] .brand-mark');
    const text = typed.dataset.text;
    const typing = { length: 0 };

    introTimeline?.kill();
    gsap.set(intro, { autoAlpha: 1, backgroundColor: '#fff', pointerEvents: 'auto' });
    gsap.set(parts, { autoAlpha: 1, y: 0 });
    gsap.set(caret, { visibility: 'visible' });
    typed.textContent = '';

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        typed.textContent = text;
        gsap.set([logo, subtitle], { opacity: 1, x: 0, y: 0, scale: 1, rotation: 0 });
        gsap.set(bar, { scaleX: 1 });
        introTimeline = gsap.to(intro, { autoAlpha: 0, duration: 0.3, delay: 0.6 });
        return;
    }

    /* Distance from the loader logo's centre to the header logo's centre. */
    const toHeader = (axis) => {
        if (!headerLogo) {
            return 0;
        }

        const from = logo.getBoundingClientRect();
        const to = headerLogo.getBoundingClientRect();

        return axis === 'x' ? to.left + to.width / 2 - (from.left + from.width / 2) : to.top + to.height / 2 - (from.top + from.height / 2);
    };

    if (headerLogo) {
        gsap.set(headerLogo, { opacity: 0 });
    }

    introTimeline = gsap
        .timeline({ defaults: { ease: 'power2.out' } })
        .fromTo(logo, { opacity: 0, x: 0, y: 0, scale: 0.4, rotation: -20 }, { opacity: 1, scale: 1, rotation: 0, duration: 0.8, ease: 'back.out(1.7)' })
        .fromTo(bar, { scaleX: 0 }, { scaleX: 1, duration: 2.4, ease: 'power1.inOut' }, 0)
        .fromTo(
            typing,
            { length: 0 },
            {
                length: text.length,
                duration: text.length * 0.035,
                ease: 'none',
                onUpdate: () => {
                    typed.textContent = text.slice(0, Math.round(typing.length));
                },
            },
            0.5,
        )
        .fromTo(subtitle, { opacity: 0, y: 8 }, { opacity: 1, y: 0, duration: 0.4 })
        .set(caret, { visibility: 'hidden' }, '+=0.2')
        .to(logo, { rotation: 360, duration: 0.7, ease: 'power2.inOut' }, '+=0.1')
        .addLabel('outro', '+=0.05')
        .set(intro, { pointerEvents: 'none' }, 'outro')
        .to(parts, { autoAlpha: 0, y: -8, duration: 0.3 }, 'outro')
        .to(
            logo,
            {
                x: () => toHeader('x'),
                y: () => toHeader('y'),
                scale: () => (headerLogo ? headerLogo.offsetWidth / logo.offsetWidth : 1),
                opacity: headerLogo ? 1 : 0,
                duration: 0.7,
                ease: 'power3.inOut',
            },
            'outro',
        )
        .to(intro, { backgroundColor: 'rgba(255, 255, 255, 0)', duration: 0.6 }, 'outro+=0.1')
        .set(headerLogo ?? {}, { opacity: 1 })
        .set(intro, { autoAlpha: 0 });
}

if (intro) {
    playIntro();
}

/* Portal sidebar --------------------------------------------------------- */

const SIDEBAR_KEY = 'tips_sidebar';
const desktopWidth = window.matchMedia('(min-width: 992px)');

/* Desktop: fold the sidebar to icons and remember it. Smaller screens: open or close the slide-in panel. */
function toggleSidebar(button) {
    if (!desktopWidth.matches) {
        bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('sidebar')).toggle();
        return;
    }

    const collapsed = document.documentElement.classList.toggle('sidebar-collapsed');
    button.setAttribute('aria-expanded', String(!collapsed));

    try {
        localStorage.setItem(SIDEBAR_KEY, collapsed ? 'collapsed' : 'open');
    } catch (e) {}
}

document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
    button.setAttribute('aria-expanded', String(!document.documentElement.classList.contains('sidebar-collapsed')));
});

/* Click actions ---------------------------------------------------------- */

document.addEventListener('click', (event) => {
    const target = event.target.closest(
        '[data-intro-replay], [data-sidebar-toggle], [data-theme-toggle], [data-password-toggle], [data-recaptcha-reset], [data-export-table], [data-table-clear], [data-table-page]',
    );

    if (!target) {
        return;
    }

    /* The second click of a double click would undo a toggle or download twice; act once. */
    if (event.detail > 1) {
        return;
    }

    /* On the cover page the logo replays the intro instead of reloading the form. */
    if (target.matches('[data-intro-replay]') && intro) {
        event.preventDefault();
        window.scrollTo({ top: 0 });
        playIntro();
    }

    if (target.matches('[data-sidebar-toggle]')) {
        toggleSidebar(target);
    }

    if (target.matches('[data-theme-toggle]')) {
        applyTheme(document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
    }

    if (target.matches('[data-password-toggle]')) {
        const input = document.getElementById(target.dataset.passwordToggle);
        const reveal = input.type === 'password';
        input.type = reveal ? 'text' : 'password';
        target.setAttribute('aria-pressed', String(reveal));
        target.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
        target.querySelector('i')?.classList.replace(reveal ? 'fa-eye' : 'fa-eye-slash', reveal ? 'fa-eye-slash' : 'fa-eye');
    }

    if (target.matches('[data-recaptcha-reset]')) {
        window.grecaptcha?.reset();
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

    if (target.matches('[data-table-page]')) {
        const tableId = target.dataset.tablePage;
        filterTable(tableId, Number(target.dataset.page));
        document.getElementById(tableId).closest('.card').scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }
});

/* Email codes: "Resend code" counts down until the server allows another one. */

function startResendTimer(button, seconds) {
    const label = button.querySelector('[data-resend-label]') ?? button;
    button.disabled = true;
    clearInterval(button.timer);

    const tick = () => {
        if (seconds <= 0) {
            clearInterval(button.timer);
            button.disabled = false;
            label.textContent = 'Resend code';
            return;
        }
        label.textContent = `Resend code in 0:${String(seconds).padStart(2, '0')}`;
        seconds--;
    };

    tick();
    button.timer = setInterval(tick, 1000);
}

document.querySelectorAll('[data-resend-in]').forEach((button) => {
    const seconds = Number(button.dataset.resendIn);

    if (seconds > 0) {
        startResendTimer(button, seconds);
    }
});

/* A field the server marked wrong stops looking wrong once it's edited. */
document.addEventListener('input', (event) => {
    event.target.classList?.remove('is-invalid');
});

/* Live cover-page preview -------------------------------------------------- */

const updateCoverPreview = (event) => {
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

    /* Optional lines on the sheet only show once they have a value. */
    document.querySelectorAll(`[data-preview-row="${key}"]`).forEach((row) => {
        row.hidden = !value;
    });
};

document.addEventListener('input', updateCoverPreview);
document.addEventListener('change', updateCoverPreview);

/*
| <select data-autofill="inputId">: copy the chosen option's data-value into another field.
| On an <input list> it only fills in when the typed text matches a datalist option,
| so a name typed by hand leaves the other field for the student to fill.
*/
const autofill = (event) => {
    const source = event.target;
    const targetId = source.dataset?.autofill;

    if (!targetId) {
        return;
    }

    let value;

    if (source.list) {
        value = [...source.list.options].find((option) => !option.disabled && option.value === source.value)?.dataset.value;

        if (value === undefined) {
            return;
        }
    } else if (event.type === 'change') {
        value = source.selectedOptions[0]?.dataset.value ?? '';
    } else {
        return;
    }

    const target = document.getElementById(targetId);
    target.value = value;
    target.dispatchEvent(new Event('input', { bubbles: true }));
};

document.addEventListener('input', autofill);
document.addEventListener('change', autofill);

/*
| <select|datalist data-option-filter='{"dept":"std_dept"}'>: only offer options whose data-dept matches #std_dept.
| An empty source normally offers everything; with data-option-filter-exact it only offers options whose attribute
| is empty too (e.g. departments without a sub-course). data-option-empty-disables disables the select while nothing
| matches, so it's skipped by `required` and isn't submitted (e.g. sub-course for a course that has none).
| The placeholder reads data-label, data-empty when nothing matches, or data-waiting while every source is empty.
*/
document.querySelectorAll('select[data-option-filter], datalist[data-option-filter]').forEach((select) => {
    const rules = Object.entries(JSON.parse(select.dataset.optionFilter));
    const placeholder = select.querySelector('option[value=""]');
    const exact = select.hasAttribute('data-option-filter-exact');

    const update = () => {
        let matches = 0;

        for (const option of select.options) {
            if (option === placeholder) {
                continue;
            }

            const visible = rules.every(([attribute, sourceId]) => {
                const wanted = document.getElementById(sourceId).value;

                return (!exact && !wanted) || (option.dataset[attribute] ?? '') === wanted;
            });

            option.hidden = !visible;
            option.disabled = !visible;
            matches += visible ? 1 : 0;
        }

        if (select.selectedOptions?.[0]?.disabled) {
            select.value = '';
            select.dispatchEvent(new Event('input', { bubbles: true }));
            select.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (placeholder) {
            const waiting = placeholder.dataset.waiting && rules.every(([, sourceId]) => !document.getElementById(sourceId).value);
            placeholder.textContent = matches ? placeholder.dataset.label : waiting ? placeholder.dataset.waiting : placeholder.dataset.empty;
        }

        if (select.hasAttribute('data-option-empty-disables')) {
            select.disabled = !matches;
        }
    };

    rules.forEach(([, sourceId]) => document.getElementById(sourceId).addEventListener('change', update));
    select.addEventListener('refresh', update);
    update();
});

document.addEventListener('reset', (event) => {
    event.target.classList.remove('was-validated');

    setTimeout(() => {
        event.target.querySelectorAll('[data-option-filter]').forEach((select) => select.dispatchEvent(new Event('refresh')));
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

/* Tables: search, filters, pages, empty result ------------------------------- */

/* Rows matching the search and filters are shown `data-per-page` at a time; a new search starts again at page 1. */
function filterTable(tableId, page = 1) {
    const table = document.getElementById(tableId);
    const query = (document.querySelector(`[data-table-search="${tableId}"]`)?.value ?? '').trim().toLowerCase();
    const filters = [...document.querySelectorAll(`[data-table-filter="${tableId}"]`)].filter((select) => select.value);
    const rows = [...table.querySelectorAll('tbody tr:not([data-empty-row]):not([data-no-results])')];

    if (!rows.length) {
        return;
    }

    const matches = rows.filter((row) => {
        const match =
            (!query || row.textContent.toLowerCase().includes(query)) &&
            filters.every((select) => row.dataset[select.dataset.filterKey] === select.value);
        row.toggleAttribute('data-filtered-out', !match);
        return match;
    });

    const perPage = Number(table.dataset.perPage) || matches.length || 1;
    const pageCount = Math.max(1, Math.ceil(matches.length / perPage));
    const current = Math.min(Math.max(1, page), pageCount);
    const start = (current - 1) * perPage;
    const onPage = new Set(matches.slice(start, start + perPage));

    rows.forEach((row) => {
        row.hidden = !onPage.has(row);
        row.removeAttribute('data-striped');
    });
    [...onPage].forEach((row, index) => row.toggleAttribute('data-striped', index % 2 === 0));

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

    noResults.hidden = matches.length > 0;

    const range = matches.length ? `${start + 1}–${start + onPage.size}` : '0';
    document.querySelectorAll(`[data-table-range="${tableId}"]`).forEach((el) => {
        el.textContent = range;
    });
    document.querySelectorAll(`[data-table-count="${tableId}"]`).forEach((el) => {
        el.textContent = matches.length;
    });
    document.querySelectorAll(`[data-table-total="${tableId}"]`).forEach((el) => {
        el.textContent = ` (filtered from ${rows.length})`;
        el.hidden = matches.length === rows.length;
    });
    document.querySelectorAll(`[data-table-pager="${tableId}"]`).forEach((nav) => {
        nav.hidden = pageCount < 2;
        nav.innerHTML = nav.hidden ? '' : pagerHtml(tableId, current, pageCount);
    });
}

/* Previous, numbered pages (first, last and the two either side of the current one), next. */
function pagerHtml(tableId, current, pageCount) {
    const item = (label, page, { active = false, disabled = false, ariaLabel = null } = {}) => `
        <li class="page-item${active ? ' active' : ''}${disabled ? ' disabled' : ''}">
            <button type="button" class="page-link" data-table-page="${tableId}" data-page="${page}"
                ${disabled ? 'disabled' : ''} ${active ? 'aria-current="page"' : ''} ${ariaLabel ? `aria-label="${ariaLabel}"` : ''}>${label}</button>
        </li>`;
    const gap = '<li class="page-item disabled"><span class="page-link">…</span></li>';

    const pages = [...Array(pageCount).keys()]
        .map((index) => index + 1)
        .filter((page) => page === 1 || page === pageCount || Math.abs(page - current) <= 2);

    const numbered = pages.map((page, index) => {
        const skipped = index > 0 && page - pages[index - 1] > 1;
        return (skipped ? gap : '') + item(page, page, { active: page === current, ariaLabel: `Page ${page}` });
    });

    return `<ul class="pagination pagination-sm mb-0">
        ${item('<i class="fa-solid fa-chevron-left" aria-hidden="true"></i>', current - 1, { disabled: current === 1, ariaLabel: 'Previous page' })}
        ${numbered.join('')}
        ${item('<i class="fa-solid fa-chevron-right" aria-hidden="true"></i>', current + 1, { disabled: current === pageCount, ariaLabel: 'Next page' })}
    </ul>`;
}

document.querySelectorAll('table[data-per-page]').forEach((table) => filterTable(table.id));

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
    const rows = [...table.querySelectorAll('tbody tr:not([data-filtered-out]):not([data-empty-row]):not([data-no-results])')].map((row) =>
        [...row.cells].filter(keep).map(text),
    );

    if (!rows.length) {
        toast('There are no rows to export.', 'error');
        return;
    }

    const csv = [header, ...rows].map((row) => row.map(escape).join(',')).join('\r\n');
    const link = document.createElement('a');
    link.href = URL.createObjectURL(new Blob([`﻿${csv}`], { type: 'text/csv;charset=utf-8' }));
    /* Today's date in IST (en-CA formats as YYYY-MM-DD); toISOString would give the UTC date before 5:30 am. */
    const today = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Kolkata' }).format(new Date());
    link.download = `${name}-${today}.csv`;
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

    /* Real forms: send to this row's URL, and drop errors left from another row's failed save. */
    if (form && event.relatedTarget.dataset.action) {
        form.action = event.relatedTarget.dataset.action;
        form.querySelectorAll('.is-invalid').forEach((field) => field.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback.d-block').forEach((error) => error.classList.remove('d-block'));
    }

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

    /* Filling a field doesn't fire `change`, so re-narrow dependent lists (e.g. sub-courses) to the filled values. */
    modal.querySelectorAll('[data-option-filter]').forEach((select) => select.dispatchEvent(new Event('refresh')));
});

document.querySelectorAll('.modal[data-show-on-load]').forEach((modal) => bootstrap.Modal.getOrCreateInstance(modal).show());

/* Coming back with the browser's Back button can restore a page mid-submit; unlock its forms. */
window.addEventListener('pageshow', (event) => {
    if (!event.persisted) {
        return;
    }

    document.querySelectorAll('form[data-submitting]').forEach((form) => {
        delete form.dataset.submitting;
        form.querySelectorAll('[aria-busy="true"]').forEach((button) => setBusy(button, false));
        form.querySelectorAll('[type=submit]:disabled').forEach((button) => {
            button.disabled = false;
        });
    });
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

    /* A double click (or a second Enter) must not send the form twice: the second request would reuse a spent token or create a duplicate. */
    if (form.dataset.submitting) {
        event.preventDefault();
        return;
    }

    /* Google's "I'm not a robot" box isn't a form field, so check it by hand. */
    const recaptcha = form.querySelector('[data-recaptcha]');
    const recaptchaMissing = Boolean(recaptcha) && !window.grecaptcha?.getResponse();
    recaptcha?.querySelector('.invalid-feedback').classList.toggle('d-block', recaptchaMissing);

    if ((form.noValidate && !form.checkValidity()) || recaptchaMissing) {
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

    form.dataset.submitting = 'true';

    /* Real forms: lock the button (with its busy label, if it has one) while the server works. */
    if (!form.matches('[data-demo-form]')) {
        if (event.submitter?.dataset.busy) {
            setBusy(event.submitter, true);
        } else if (event.submitter) {
            event.submitter.disabled = true;
        }

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

        delete form.dataset.submitting;
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

/* Called by the reCAPTCHA widget (data-callback) once the box is ticked. */
window.recaptchaChecked = () => {
    document.querySelectorAll('[data-recaptcha] .invalid-feedback').forEach((error) => error.classList.remove('d-block'));
};

/*
| Profile photo: check the file, crop it to passport size in the dialog named by data-cropper, then send the
| cropped copy (a 35 × 45 mm JPEG) in place of the original. The original never leaves the browser.
*/
const PASSPORT_PHOTO = { width: 420, height: 540, maxSourceBytes: 10 * 1024 * 1024 };
let cropper = null;

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
        : file.size > PASSPORT_PHOTO.maxSourceBytes
          ? 'That image is larger than 10 MB.'
          : '';
    error.hidden = !error.textContent;

    if (error.textContent) {
        input.value = '';
        return;
    }

    openCropper(input, file, error);
});

function openCropper(input, file, error) {
    const modal = document.getElementById(input.dataset.cropper);
    const image = modal.querySelector('[data-cropper-image]');
    const shown = new Promise((resolve) => modal.addEventListener('shown.bs.modal', resolve, { once: true }));

    cropper = { input, modal, image, zoom: modal.querySelector('[data-cropper-zoom]'), saved: false };
    image.src = URL.createObjectURL(file);
    bootstrap.Modal.getOrCreateInstance(modal).show();

    /* The frame only has a size once the dialog is on screen. */
    Promise.all([shown, image.decode()])
        .then(() => fitCropper(modal.querySelector('[data-cropper-stage]')))
        .catch(() => {
            error.textContent = 'We couldn\'t read that image. Try another one.';
            error.hidden = false;
            bootstrap.Modal.getInstance(modal)?.hide();
        });
}

/* Start with the whole frame covered and the photo centred. */
function fitCropper(stage) {
    const { width, height } = stage.getBoundingClientRect();
    const { naturalWidth, naturalHeight } = cropper.image;

    cropper.frame = { width, height };
    cropper.minScale = Math.max(width / naturalWidth, height / naturalHeight);
    cropper.scale = cropper.minScale;
    cropper.x = (width - naturalWidth * cropper.scale) / 2;
    cropper.y = (height - naturalHeight * cropper.scale) / 2;
    cropper.zoom.value = 1;
    renderCropper();
}

/* Keep the photo covering the frame, so the saved picture never has empty edges. */
function renderCropper() {
    const { image, frame, scale } = cropper;
    cropper.x = Math.min(0, Math.max(frame.width - image.naturalWidth * scale, cropper.x));
    cropper.y = Math.min(0, Math.max(frame.height - image.naturalHeight * scale, cropper.y));
    image.style.transform = `translate(${cropper.x}px, ${cropper.y}px) scale(${scale})`;
}

/* Zoom around a point in the frame (its centre, or where the mouse wheel is). */
function zoomCropper(level, focusX = cropper.frame.width / 2, focusY = cropper.frame.height / 2) {
    const scale = cropper.minScale * Math.min(4, Math.max(1, level));
    const ratio = scale / cropper.scale;

    cropper.x = focusX - (focusX - cropper.x) * ratio;
    cropper.y = focusY - (focusY - cropper.y) * ratio;
    cropper.scale = scale;
    cropper.zoom.value = scale / cropper.minScale;
    renderCropper();
}

document.addEventListener('pointerdown', (event) => {
    const stage = event.target.closest('[data-cropper-stage]');

    if (!stage || !cropper?.frame) {
        return;
    }

    stage.setPointerCapture(event.pointerId);
    cropper.drag = { x: event.clientX, y: event.clientY };
});

document.addEventListener('pointermove', (event) => {
    if (!cropper?.drag) {
        return;
    }

    cropper.x += event.clientX - cropper.drag.x;
    cropper.y += event.clientY - cropper.drag.y;
    cropper.drag = { x: event.clientX, y: event.clientY };
    renderCropper();
});

['pointerup', 'pointercancel'].forEach((type) =>
    document.addEventListener(type, () => {
        if (cropper) {
            cropper.drag = null;
        }
    }),
);

document.addEventListener(
    'wheel',
    (event) => {
        const stage = event.target.closest('[data-cropper-stage]');

        if (!stage || !cropper?.frame) {
            return;
        }

        event.preventDefault();
        const box = stage.getBoundingClientRect();
        const level = cropper.scale / cropper.minScale;
        zoomCropper(event.deltaY < 0 ? level * 1.1 : level / 1.1, event.clientX - box.left, event.clientY - box.top);
    },
    { passive: false },
);

document.addEventListener('keydown', (event) => {
    const step = { ArrowLeft: [10, 0], ArrowRight: [-10, 0], ArrowUp: [0, 10], ArrowDown: [0, -10] }[event.key];

    if (!step || !event.target.matches('[data-cropper-stage]') || !cropper?.frame) {
        return;
    }

    event.preventDefault();
    cropper.x += step[0];
    cropper.y += step[1];
    renderCropper();
});

document.addEventListener('input', (event) => {
    if (event.target.matches('[data-cropper-zoom]') && cropper?.frame) {
        zoomCropper(Number(event.target.value));
    }
});

/* Draw what's inside the frame at passport size, put it in the file field, and send the form. */
document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-cropper-save]');

    if (!button || !cropper?.frame || cropper.saved) {
        return;
    }

    const { image, frame, scale, x, y, input } = cropper;
    const canvas = document.createElement('canvas');
    canvas.width = PASSPORT_PHOTO.width;
    canvas.height = PASSPORT_PHOTO.height;

    const context = canvas.getContext('2d');
    context.fillStyle = '#fff';
    context.fillRect(0, 0, canvas.width, canvas.height);
    context.imageSmoothingQuality = 'high';
    context.drawImage(image, -x / scale, -y / scale, frame.width / scale, frame.height / scale, 0, 0, canvas.width, canvas.height);

    cropper.saved = true;
    setBusy(button, true);

    canvas.toBlob(
        (blob) => {
            const files = new DataTransfer();
            files.items.add(new File([blob], 'passport-photo.jpg', { type: 'image/jpeg' }));
            input.files = files.files;
            input.form.requestSubmit();
        },
        'image/jpeg',
        0.9,
    );
});

/* Closing without "Use this photo" forgets the chosen file. */
document.addEventListener('hidden.bs.modal', (event) => {
    if (!cropper || event.target !== cropper.modal) {
        return;
    }

    if (!cropper.saved) {
        cropper.input.value = '';
    }

    URL.revokeObjectURL(cropper.image.src);
    cropper = null;
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
