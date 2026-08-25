

import Alpine from 'alpinejs';
import { Grid } from "gridjs";

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const tables = document.querySelectorAll('table.border-collapse:not(.no-grid)');
    
    tables.forEach((table) => {
        if (table.querySelector('thead') && table.querySelector('tbody')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'w-full';
            table.parentNode.insertBefore(wrapper, table);
            
            new Grid({
                from: table,
                search: true,
                sort: true,
                pagination: {
                    limit: 10
                },
                language: {
                    search: { placeholder: 'Rechercher...' },
                    pagination: {
                        previous: 'Précédent',
                        next: 'Suivant',
                        showing: 'Affichage de',
                        results: () => 'résultats',
                        of: 'sur'
                    },
                    noRecordsFound: 'Aucun enregistrement trouvé'
                }
            }).render(wrapper);
            
            // Grid.js automatically hides the original HTML table
        }
    });

    // Client-side validation for the menu composition form to avoid page reloads
    const menuForm = document.getElementById('menu-composer-form');
    if (menuForm) {
        const errorClass = 'menu-validation-error';
        console.debug('[menu] initializing client-side validation handler');

        function clearErrors() {
            menuForm.querySelectorAll('.' + errorClass).forEach(el => el.remove());
            menuForm.querySelectorAll('[aria-invalid="true"]').forEach(el => el.removeAttribute('aria-invalid'));
        }

        menuForm.addEventListener('submit', async (e) => {
            console.debug('[menu] submit event received');
            e.preventDefault();
            // Prevent double submission
            if (menuForm.dataset.submitting === '1') {
                console.debug('[menu] submit ignored: already submitting');
                return;
            }
            menuForm.dataset.submitting = '1';
            // Debug: require at least one plat selected for testing
            const platSelects = Array.from(menuForm.querySelectorAll('select[name$="[plat_id]"]'));
            clearErrors();

            let firstErrorEl = null;
            const selectedCount = platSelects.filter(s => !!s.value).length;
            console.debug('[menu] total plat selects:', platSelects.length, 'selected:', selectedCount);

            if (selectedCount === 0) {
                // No plats selected at all — block submit and show generic error
                const gen = document.createElement('div');
                gen.className = errorClass + ' bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md mb-4';
                gen.textContent = '⚠️ Veuillez sélectionner au moins un plat principal pour effectuer le test.';
                menuForm.prepend(gen);
                // focus first plat select if present
                if (platSelects.length > 0) {
                    firstErrorEl = platSelects[0];
                }
            }

            // Validate date_debut (hidden input) exists
            const dateInput = menuForm.querySelector('input[name="date_debut"]');
            if (!dateInput || !dateInput.value) {
                if (dateInput) {
                    const err = document.createElement('p');
                    err.className = errorClass + ' text-red-600 text-sm mt-1';
                    err.textContent = '⚠️ Veuillez sélectionner une date (semaine).';
                    dateInput.setAttribute('aria-invalid', 'true');
                    dateInput.parentNode && dateInput.parentNode.appendChild(err);
                    if (!firstErrorEl) firstErrorEl = dateInput;
                }
            }

            if (firstErrorEl) {
                console.debug('[menu] client validation blocked submit; focusing first error');
                firstErrorEl.focus();
                firstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            console.debug('[menu] client validation passed, preparing AJAX submit');

            // Prepare AJAX submission
            const action = menuForm.getAttribute('action') || window.location.href;
            const method = (menuForm.getAttribute('method') || 'POST').toUpperCase();
            const formData = new FormData(menuForm);
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            // Disable submit buttons while request is in flight
            const submitButtons = Array.from(menuForm.querySelectorAll('button[type="submit"], input[type="submit"]'));
            submitButtons.forEach(b => b.setAttribute('disabled', 'disabled'));

            try {
                // Debug: show form data that will be sent
                try {
                    const entries = Array.from(formData.entries());
                    console.debug('[menu] formData entries count:', entries.length, entries.slice(0, 20));
                } catch (fdErr) {
                    console.debug('[menu] could not read formData entries', fdErr);
                }
                // show small busy indicator while sending
                let busy = document.createElement('div');
                busy.className = 'menu-busy fixed inset-0 flex items-center justify-center bg-black/30 z-50';
                busy.innerHTML = '<div class="bg-white p-4 rounded shadow">Envoi en cours…</div>';
                document.body.appendChild(busy);
                console.debug('[menu] sending AJAX request to', action);
                const resp = await fetch(action, {
                    method,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                });

                console.debug('[menu] fetch response status:', resp.status, 'redirected:', resp.redirected);

                if (resp.status === 422) {
                    // Validation errors from Laravel
                    const data = await resp.json();
                    const errors = data.errors || data;
                    for (const key in errors) {
                        if (!Object.prototype.hasOwnProperty.call(errors, key)) continue;
                        const parts = key.split('.');
                        const name = parts.shift() + parts.map(p => `[${p}]`).join('');
                        const field = menuForm.querySelector(`[name="${name}"]`);
                        const msgs = errors[key];
                        const msg = Array.isArray(msgs) ? msgs[0] : msgs;
                        if (field) {
                            const err = document.createElement('p');
                            err.className = errorClass + ' text-red-600 text-sm mt-1';
                            err.textContent = msg;
                            field.setAttribute('aria-invalid', 'true');
                            field.parentNode.appendChild(err);
                        } else {
                            const gen = document.createElement('div');
                            gen.className = errorClass + ' bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md mb-4';
                            gen.textContent = msg;
                            menuForm.prepend(gen);
                        }
                    }
                    const firstField = menuForm.querySelector('.' + errorClass);
                    if (firstField) firstField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                // Success: prefer a JSON message if available, otherwise show a generic success banner.
                if (resp.ok) {
                    const contentType = resp.headers.get('Content-Type') || '';
                    const json = contentType.includes('application/json') ? await resp.json().catch(() => null) : null;
                    const msg = (json && (json.message || json.status)) ? (json.message || json.status) : 'Opération réussie.';
                    const success = document.createElement('div');
                    success.className = 'menu-success bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md mb-4';
                    success.textContent = msg;
                    menuForm.prepend(success);
                    // Optionally clear or keep form; here we keep data but mark as not submitting to allow navigation if needed.
                    return;
                }
            } catch (err) {
                console.error('[menu] AJAX error', err);
                const gen = document.createElement('div');
                gen.className = errorClass + ' bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md mb-4';
                gen.textContent = 'Une erreur réseau est survenue. Réessayez.';
                menuForm.prepend(gen);
            } finally {
                document.querySelectorAll('.menu-busy').forEach(n => n.remove());
                try {
                    menuForm.dataset.submitting = '0';
                    submitButtons.forEach(b => b.removeAttribute('disabled'));
                } catch (e) {
                    console.debug('[menu] cleanup error', e);
                }
            }
        });

        // Remove inline error when user corrects the field
        menuForm.addEventListener('change', (ev) => {
            const target = ev.target;
            if (!target) return;
            if (target.matches('select[name$="[plat_id]"], input[name="date_debut"]')) {
                const siblingError = target.parentNode.querySelector('.' + errorClass);
                if (siblingError && target.value) siblingError.remove();
                if (target.value) target.removeAttribute('aria-invalid');
            }
        });
    }
});
