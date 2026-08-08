<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Déclarations de mon service') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex justify-between items-center">
                <a href="{{ route('derogations.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                    {{ __('Mes demandes de dérogation') }}
                </a>
                <a href="{{ route('declarations.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Déclarer pour une date') }}
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Bénéficiaire') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Période') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Repas') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Statut') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Bon de repas') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($declarations as $declaration)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $declaration->beneficiaire->nom }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $declaration->date_debut->format('d/m/Y') }}
                                        @if (! $declaration->date_debut->equalTo($declaration->date_fin))
                                            &rarr; {{ $declaration->date_fin->format('d/m/Y') }}
                                        @endif
                                        <span class="text-xs text-gray-400">({{ $declaration->type_periode }})</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ collect($declaration->repas)->map(fn ($r) => str_replace('_', ' ', $r))->implode(', ') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <x-declaration-statut-badge :statut="$declaration->statut" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if ($declaration->bonRepas)
                                            {{ __('Généré') }}
                                        @else
                                            &mdash;
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ __('Aucune déclaration enregistrée.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $declarations->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
