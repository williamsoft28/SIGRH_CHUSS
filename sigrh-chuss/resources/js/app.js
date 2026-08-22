

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
            // Debug: count selects and empty values
            const totalPlats = menuForm.querySelectorAll('select[name$="[plat_id]"]').length;
            const emptyPlats = menuForm.querySelectorAll('select[name$="[plat_id]"]').length
                - Array.from(menuForm.querySelectorAll('select[name$="[plat_id]"]')).filter(s => !!s.value).length;
            console.debug('[menu] total plat selects:', totalPlats, 'empty:', emptyPlats);
            // Validate required plat selects
            const platSelects = menuForm.querySelectorAll('select[name$="[plat_id]"]');
            clearErrors();

            let firstErrorEl = null;

            platSelects.forEach(select => {
                if (!select.value) {
                    const err = document.createElement('p');
                    err.className = errorClass + ' text-red-600 text-sm mt-1';
                    err.textContent = '⚠️ Veuillez sélectionner un plat.';
                    select.setAttribute('aria-invalid', 'true');
                    select.parentNode.appendChild(err);
                    if (!firstErrorEl) firstErrorEl = select;
                }
            });

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

                // On success, Laravel typically redirects; fetch follows redirects and sets redirected flag
                if (resp.redirected) {
                    window.location.href = resp.url;
                    return;
                }

                // Try JSON response with location
                const json = await resp.json().catch(() => null);
                if (json && json.location) {
                    window.location.href = json.location;
                    return;
                }

                // Fallback: reload page
                window.location.reload();
            } catch (err) {
                console.error('[menu] AJAX error', err);
                const gen = document.createElement('div');
                gen.className = errorClass + ' bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md mb-4';
                gen.textContent = 'Une erreur réseau est survenue. Réessayez.';
                menuForm.prepend(gen);
            } finally {
                document.querySelectorAll('.menu-busy').forEach(n => n.remove());
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
