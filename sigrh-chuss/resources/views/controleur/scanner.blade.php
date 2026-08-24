<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contrôle des bons au réfectoire') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div id="scan-erreur" class="hidden bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md"></div>

            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <div id="qr-reader" data-verify-url="{{ route('controleur.verifier') }}" class="w-full"></div>
            </div>

            <div id="scan-resultat" class="p-8 rounded-lg text-center text-white bg-gray-400">
                <div class="text-xl">{{ __('En attente d’un scan...') }}</div>
            </div>

            <p class="text-xs text-gray-400 text-center">
                {{ __('Le type de repas (petit-déjeuner, déjeuner, dîner) est déterminé automatiquement selon l’heure du scan.') }}
            </p>
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ __('Alternative : Code Manuel') }}</h3>
                <form id="manual-code-form" class="flex gap-2">
                    <input type="text" id="manual_code" name="manual_code" placeholder="Ex: MI12AG" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-chuss-green focus:ring-chuss-green sm:text-sm uppercase" autocomplete="off">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-chuss-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-chuss-green focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Vérifier') }}
                    </button>
                </form>
            </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('manual-code-form');
            var input = document.getElementById('manual_code');
            var resultEl = document.getElementById('scan-resultat');
            var verifyUrl = document.getElementById('qr-reader').dataset.verifyUrl;
            var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var code = input.value.trim();
                if (!code) return;

                // Disable input during request
                input.disabled = true;

                fetch(verifyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ code: code }),
                })
                .then(response => response.json())
                .then(data => {
                    var couleur = data.autorise ? 'bg-green-600' : 'bg-red-600';
                    resultEl.className = 'p-8 rounded-lg text-center text-white ' + couleur;

                    if (data.autorise) {
                        resultEl.innerHTML =
                            '<div class="text-4xl font-bold">✓ AUTORISÉ</div>' +
                            '<div class="mt-3 text-2xl">' + escapeHtml(data.beneficiaire) + '</div>' +
                            '<div class="text-lg capitalize">' + escapeHtml(data.type_repas) + '</div>';
                    } else {
                        resultEl.innerHTML =
                            '<div class="text-4xl font-bold">✗ REFUSÉ</div>' +
                            '<div class="mt-3 text-xl">' + escapeHtml(data.motif || 'Erreur inconnue.') + '</div>';
                    }
                })
                .catch(err => {
                    resultEl.className = 'p-8 rounded-lg text-center text-white bg-red-600';
                    resultEl.innerHTML = '<div class="text-4xl font-bold">✗ ERREUR</div><div class="mt-3 text-xl">Erreur réseau.</div>';
                })
                .finally(() => {
                    input.disabled = false;
                    input.value = '';
                    input.focus();
                });
            });

            function escapeHtml(texte) {
                var div = document.createElement('div');
                div.textContent = texte == null ? '' : texte;
                return div.innerHTML;
            }
        });
    </script>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="{{ asset('js/scanner.js') }}"></script>
</x-app-layout>
