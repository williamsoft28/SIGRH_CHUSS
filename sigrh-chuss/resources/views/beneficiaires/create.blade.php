<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter un bénéficiaire') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($errors->any())
                <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $erreur)
                            <li>{{ $erreur }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('beneficiaires.create') }}" class="flex items-end gap-4">
                    <div>
                        <x-input-label for="semaine" :value="__('Semaine du')" />
                        <x-text-input id="semaine" name="semaine" type="date" class="mt-1"
                            value="{{ $lundi->toDateString() }}" />
                    </div>
                    <x-secondary-button type="submit">{{ __('Charger') }}</x-secondary-button>
                </form>
            </div>

            <form method="POST" action="{{ route('beneficiaires.store') }}">
                @csrf
                <input type="hidden" name="lundi" value="{{ $lundi->toDateString() }}">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                    <div>
                        <x-input-label for="prenom" :value="__('Prénom')" />
                        <x-text-input id="prenom" name="prenom" type="text" class="mt-1 block w-full"
                            value="{{ old('prenom') }}" required autofocus />
                        <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="nom" :value="__('Nom')" />
                        <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full"
                            value="{{ old('nom') }}" required />
                        <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="numero_whatsapp" :value="__('Numéro de téléphone (WhatsApp)')" />
                        <x-text-input id="numero_whatsapp" name="numero_whatsapp" type="text" class="mt-1 block w-full"
                            value="{{ old('numero_whatsapp') }}" placeholder="+243 900 000 000" required />
                        <x-input-error :messages="$errors->get('numero_whatsapp')" class="mt-2" />
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mt-6">
                    <h3 class="font-medium text-gray-900 mb-1">
                        {{ __('Calendrier de la semaine') }}
                    </h3>
                    <p class="text-sm text-gray-500 mb-1">
                        {{ __('Du') }} {{ $lundi->format('d/m/Y') }} {{ __('au') }} {{ $lundi->copy()->addDays(6)->format('d/m/Y') }}
                        &mdash; {{ __('choisissez, jour par jour, la catégorie de la personne.') }}
                    </p>
                    <ul class="text-xs text-gray-400 list-disc list-inside mb-4">
                        <li>{{ __('Après-midi : donne droit au déjeuner ce jour.') }}</li>
                        <li>{{ __('Garde : donne droit au dîner ce jour, et au petit-déjeuner du lendemain matin.') }}</li>
                    </ul>

                    <x-input-error :messages="$errors->get('categorie_jour')" class="mb-4" />

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Jour') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Catégorie') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($jours as $jour)
                                    @php $dateStr = $jour->toDateString(); @endphp
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <span class="font-medium capitalize">{{ $jour->locale('fr')->translatedFormat('l') }}</span>
                                            <span class="text-gray-400">{{ $jour->format('d/m/Y') }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php $valeurActuelle = old('categorie_jour.'.$dateStr, 'aucune'); @endphp
                                            <select name="categorie_jour[{{ $dateStr }}]"
                                                class="block w-full sm:w-56 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                <option value="aucune" @selected($valeurActuelle === 'aucune')>{{ __('Aucune') }}</option>
                                                <option value="apres_midi" @selected($valeurActuelle === 'apres_midi')>{{ __('Après-midi') }}</option>
                                                <option value="garde" @selected($valeurActuelle === 'garde')>{{ __('Garde') }}</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6 gap-4">
                    <a href="{{ route('beneficiaires.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        {{ __('Annuler') }}
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        {{ __('Enregistrer et générer le bon') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
