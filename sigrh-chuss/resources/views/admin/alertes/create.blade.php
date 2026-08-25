<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Envoyer une Alerte') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md mb-4">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $erreur)
                            <li>{{ $erreur }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <!-- Formulaire de sélection du service (en GET pour recharger la page et afficher les bénéficiaires) -->
                <form method="GET" action="{{ route('hotellerie.alertes.create') }}" class="mb-6 pb-6 border-b border-gray-200">
                    <div>
                        <x-input-label for="service_id_filter" :value="__('1. Sélectionner le service en infraction')" />
                        <div class="flex items-center gap-4 mt-1">
                            <select id="service_id_filter" name="service_id" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" onchange="this.form.submit()">
                                <option value="">Choisir un service...</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" @selected(request('service_id') == $service->id)>{{ $service->nom }}</option>
                                @endforeach
                            </select>
                            <noscript>
                                <x-secondary-button type="submit">{{ __('Charger') }}</x-secondary-button>
                            </noscript>
                        </div>
                    </div>
                </form>

                @if(request()->has('service_id') && request('service_id') != '')
                <!-- Formulaire principal d'envoi de l'alerte -->
                <form method="POST" action="{{ route('hotellerie.alertes.store') }}">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ request('service_id') }}">

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-input-label for="beneficiaire_id" :value="__('2. Sélectionner le bénéficiaire concerné (Optionnel)')" />
                            <select id="beneficiaire_id" name="beneficiaire_id" class="block w-full mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">Alerte générale pour le service</option>
                                @foreach($beneficiaires as $beneficiaire)
                                    <option value="{{ $beneficiaire->id }}">{{ $beneficiaire->nom }} ({{ $beneficiaire->type }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="titre" :value="__('3. Objet de l\'alerte')" />
                            <x-text-input id="titre" name="titre" type="text" class="mt-1 block w-full" value="Note d'avertissement pour infraction aux règles d'attribution" required />
                        </div>

                        <div>
                            <x-input-label for="message" :value="__('4. Corps du message (Note d\'avertissement)')" />
                            <textarea id="message" name="message" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>Il a été constaté que votre service a enfreint les règles d'attribution des bons de restauration. Veuillez veiller au respect strict des consignes concernant l'attribution des repas au personnel de garde.</textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                            {{ __('Envoyer l\'alerte au SUS') }}
                        </button>
                    </div>
                </form>
                @else
                    <p class="text-sm text-gray-500 italic">Veuillez d'abord sélectionner un service pour rédiger l'alerte.</p>
                @endif
            </div>
            
        </div>
    </div>
</x-app-layout>
