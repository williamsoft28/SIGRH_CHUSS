

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
});
