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
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="{{ asset('js/scanner.js') }}"></script>
</x-app-layout>
