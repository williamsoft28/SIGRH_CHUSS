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
                <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
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
                </dl>
            </div>

            @include('hotellerie.menus._grille', ['lecture' => true])

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <h3 class="font-medium text-gray-900">{{ __('Observations') }}</h3>

                @forelse ($menu->observations as $observation)
                    <div class="border-b pb-3">
                        <p class="text-sm text-gray-700">{{ $observation->contenu }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $observation->date_emission->format('d/m/Y H:i') }}
                            &middot; {{ $observation->statut === 'traitee' ? __('Traitée') : __('Ouverte') }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">{{ __('Aucune observation pour ce menu.') }}</p>
                @endforelse

                @if ($menu->statut === 'soumis')
                    <form method="POST" action="{{ route('prestataire.menus.observations.store', $menu) }}" class="pt-2">
                        @csrf
                        <x-input-label for="contenu" :value="__('Nouvelle observation')" />
                        <textarea id="contenu" name="contenu" rows="3" required
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                        <div class="flex justify-end mt-2">
                            <x-secondary-button type="submit">{{ __('Ajouter') }}</x-secondary-button>
                        </div>
                    </form>

                    @if ($menu->observations->isNotEmpty())
                        <form method="POST" action="{{ route('prestataire.menus.envoyer', $menu) }}"
                              onsubmit="return confirm('{{ __('Envoyer ces observations au service hôtellerie ?') }}');">
                            @csrf
                            <div class="flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    {{ __('Envoyer les observations') }}
                                </button>
                            </div>
                        </form>
                    @endif
                @endif
            </div>

            <a href="{{ route('prestataire.menus.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; {{ __('Retour') }}
            </a>
        </div>
    </div>
</x-app-layout>
