<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Menu') }} — S{{ $menu->numero_semaine }} / {{ $menu->annee }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

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
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Période') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $menu->date_debut->format('d/m/Y') }} &rarr; {{ $menu->date_fin->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Statut') }}</dt>
                        <dd><x-menu-statut-badge :statut="$menu->statut" /></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Soumis le') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $menu->date_soumission?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Validé le') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $menu->date_validation?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                </dl>
                @if ($menu->statut === 'applique')
                    <p class="text-xs text-gray-400 mt-4">
                        {{ __('Modifications déjà utilisées cette semaine :') }} {{ $menu->nb_modifications }} / 1
                    </p>
                @endif
            </div>

            @if ($menu->observations->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Observations du prestataire') }}</h3>
                    <ul class="space-y-3">
                        @foreach ($menu->observations as $observation)
                            <li class="border-b pb-3 flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm text-gray-700">{{ $observation->contenu }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $observation->date_emission->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $observation->statut === 'traitee' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $observation->statut === 'traitee' ? __('Traitée') : __('Ouverte') }}
                                    </span>
                                    @if ($observation->statut === 'ouverte')
                                        <form method="POST" action="{{ route('hotellerie.observations.traiter', $observation) }}">
                                            @csrf
                                            <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-900">
                                                {{ __('Marquer traitée') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($menu->statut === 'en_observation')
                <form method="POST" action="{{ route('hotellerie.menus.valider', $menu) }}"
                      onsubmit="return confirm('{{ __('Valider et appliquer ce menu ?') }}');">
                    @csrf
                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                            {{ __('Valider et appliquer') }}
                        </button>
                    </div>
                </form>
            @endif

            <form id="menu-composer-form" method="POST" action="{{ route('hotellerie.menus.update', $menu) }}">
                @csrf
                @method('PUT')

                @include('hotellerie.menus._grille', ['lecture' => ! $peutModifier])

                @if ($peutModifier)
                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('hotellerie.menus.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Retour') }}
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            {{ $menu->statut === 'applique' ? __('Enregistrer la modification') : __('Enregistrer') }}
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</x-app-layout>
