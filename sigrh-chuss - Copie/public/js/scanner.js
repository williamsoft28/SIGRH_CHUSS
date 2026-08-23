document.addEventListener('DOMContentLoaded', function () {
    var readerEl = document.getElementById('qr-reader');
    var resultEl = document.getElementById('scan-resultat');
    var erreurEl = document.getElementById('scan-erreur');

    if (!readerEl || typeof Html5Qrcode === 'undefined') {
        return;
    }

    var verifyUrl = readerEl.dataset.verifyUrl;
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    var enTraitement = false;

    var html5QrCode = new Html5Qrcode('qr-reader');

    function afficherResultat(data) {
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
    }

    function escapeHtml(texte) {
        var div = document.createElement('div');
        div.textContent = texte == null ? '' : texte;
        return div.innerHTML;
    }

    function onScanSuccess(decodedText) {
        if (enTraitement) {
            return;
        }
        enTraitement = true;
        erreurEl.classList.add('hidden');

        fetch(verifyUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
            },
            body: JSON.stringify({ code: decodedText }),
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                afficherResultat(data);
            })
            .catch(function () {
                afficherResultat({ autorise: false, motif: 'Erreur de communication avec le serveur.' });
            })
            .finally(function () {
                setTimeout(function () {
                    enTraitement = false;
                }, 2500);
            });
    }

    html5QrCode
        .start({ facingMode: 'environment' }, { fps: 10, qrbox: 250 }, onScanSuccess)
        .catch(function (err) {
            erreurEl.textContent = "Impossible d'accéder à la caméra : " + err;
            erreurEl.classList.remove('hidden');
        });
});
