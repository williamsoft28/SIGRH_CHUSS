<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Déclarer les repas des patients') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white p-6 shadow sm:rounded-lg">
                <form method="GET" action="{{ route('beneficiaires.declarations-patients.create') }}" class="mb-6 border-b pb-6" id="date-form">
                    <div>
                        <x-input-label for="date" :value="__('Date de la déclaration')" />
                        <x-text-input id="date" name="date" type="date" class="mt-1 block w-full max-w-sm"
                                      value="{{ $date->toDateString() }}" required onchange="document.getElementById('date-form').submit()" />
                    </div>
                </form>

                @if (! $ouverte)
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Attention!</strong>
                        <span class="block sm:inline">La saisie est verrouillée pour cette date (après 09h00). Faites une demande de dérogation si nécessaire.</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('beneficiaires.declarations-patients.store') }}">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date->toDateString() }}">

                    <div class="space-y-4 mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Nombres de plats par Régime Spécial</h3>
                        <p class="text-sm text-gray-500">Saisissez le nombre de repas nécessaires pour chaque régime. Laissez à 0 si aucun patient n'est concerné.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($regimes as $regime)
                                @php
                                    $maladesExistants = isset($declarationsExistantes[$regime->id]) ? $declarationsExistantes[$regime->id]->nombre_malades : 0;
                                @endphp
                                <div class="flex flex-col border border-gray-200 p-3 rounded-md space-y-2">
                                    <div class="text-sm font-medium text-gray-700">{{ $regime->libelle }}</div>
                                    <div class="flex items-center justify-between">
                                        <label for="malades_{{ $regime->id }}" class="text-xs text-gray-500">Nombre</label>
                                        <input type="number" id="malades_{{ $regime->id }}" name="malades[{{ $regime->id }}]" 
                                               class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-center text-sm"
                                               value="{{ old('malades.'.$regime->id, $maladesExistants) }}" 
                                               min="0"
                                               @if(!$ouverte) disabled @endif>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <a href="{{ route('beneficiaires.declarations-patients.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">
                            {{ __('Annuler') }}
                        </a>
                        <x-primary-button :disabled="!$ouverte">
                            {{ __('Enregistrer') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
