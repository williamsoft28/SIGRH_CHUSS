<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Déclarer les bénéficiaires du jour') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
                <form method="GET" action="{{ route('declarations.create') }}" class="flex items-end gap-4">
                    <div>
                        <x-input-label for="date" :value="__('Date de repas')" />
                        <x-text-input id="date" name="date" type="date" class="mt-1"
                            value="{{ $date->toDateString() }}" />
                    </div>
                    <x-secondary-button type="submit">{{ __('Charger') }}</x-secondary-button>
                </form>
            </div>

            @unless ($ouverte)
                <div class="bg-amber-100 border border-amber-300 text-amber-900 px-4 py-3 rounded-md flex items-center justify-between">
                    <span>
                        {{ __("La saisie est verrouillée pour le :date (après 09h00).", ['date' => $date->format('d/m/Y')]) }}
                    </span>
                    <a href="{{ route('derogations.index') }}" class="font-semibold underline">
                        {{ __('Demander une dérogation') }}
                    </a>
                </div>
            @endunless

            <form method="POST" action="{{ route('declarations.store') }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date->toDateString() }}">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Mange ce jour') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Bénéficiaire') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Repas concernés') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Période (réguliers)') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($beneficiaires as $beneficiaire)
                                    @php
                                        $dejaDeclare = in_array($beneficiaire->id, $dejaDeclares);
                                        $estGarde = str_contains(strtolower($beneficiaire->categorie), 'garde');
                                    @endphp
                                    <tr x-data="{ open: false }" class="align-top">
                                        <td class="px-4 py-4">
                                            @if ($dejaDeclare)
                                                <span class="text-xs text-gray-400">{{ __('Déjà déclaré(e)') }}</span>
                                            @else
                                                <input type="checkbox" x-model="open"
                                                    name="declarer[{{ $beneficiaire->id }}]" value="1"
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-900">
                                            <div class="font-medium">{{ $beneficiaire->nom }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $beneficiaire->categorie }} &middot;
                                                {{ $beneficiaire->type === 'regulier' ? 'Régulier' : 'Variable' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4" x-show="open" style="display: none;">
                                            <div class="space-y-1 text-sm text-gray-700">
                                                <label class="flex items-center gap-2">
                                                    <input type="checkbox" name="repas[{{ $beneficiaire->id }}][]" value="petit_dejeuner"
                                                        @checked($estGarde) class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                                    {{ __('Petit-déjeuner') }}
                                                </label>
                                                <label class="flex items-center gap-2">
                                                    <input type="checkbox" name="repas[{{ $beneficiaire->id }}][]" value="dejeuner"
                                                        @checked(! $estGarde) class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                                    {{ __('Déjeuner') }}
                                                </label>
                                                <label class="flex items-center gap-2">
                                                    <input type="checkbox" name="repas[{{ $beneficiaire->id }}][]" value="diner"
                                                        @checked($estGarde) class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                                    {{ __('Dîner') }}
                                                </label>
                                                @if ($estGarde)
                                                    <p class="text-xs text-gray-400">{{ __('Agent de garde : petit-déjeuner + dîner suggérés.') }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4" x-show="open" style="display: none;">
                                            @if ($beneficiaire->type === 'regulier')
                                                <div class="space-y-2">
                                                    <select name="type_periode[{{ $beneficiaire->id }}]"
                                                        class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                        <option value="hebdomadaire">{{ __('Hebdomadaire') }}</option>
                                                        <option value="mensuel">{{ __('Mensuel') }}</option>
                                                    </select>
                                                    <div class="flex gap-2">
                                                        <input type="date" name="date_debut[{{ $beneficiaire->id }}]"
                                                            value="{{ $date->toDateString() }}"
                                                            class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                        <input type="date" name="date_fin[{{ $beneficiaire->id }}]"
                                                            class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                    </div>
                                                    <p class="text-xs text-gray-400">{{ __('Laisser la date de fin vide pour une période par défaut (7 ou 30 jours).') }}</p>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">{{ __('Déclaration quotidienne, pour cette date uniquement.') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">
                                            {{ __("Aucun bénéficiaire enregistré pour votre service.") }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-6 gap-4">
                    <a href="{{ route('declarations.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        {{ __('Annuler') }}
                    </a>
                    <button type="submit" @disabled(! $ouverte)
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed">
                        {{ __('Enregistrer la déclaration') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
