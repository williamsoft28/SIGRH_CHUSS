<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Déclarations des Patients / Bénéficiaires Malades') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('beneficiaires.declarations-patients.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Déclarer pour une date') }}
                </a>
            </div>

            <div class="bg-white/80 backdrop-blur-xl overflow-hidden shadow-float rounded-2xl border border-white/60">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gradient-to-r from-chuss-green/5 to-chuss-amber/5 border-b border-gray-100">
                            <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Date') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Régime Spécial') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Nombre de Malades') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Nombre de Plats') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100/50">
                            @forelse ($declarations as $declaration)
                                <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ \Carbon\Carbon::parse($declaration->date_repas)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $declaration->regimeSpecial->libelle ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-bold">
                                        {{ $declaration->nombre_malades }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-bold">
                                        {{ $declaration->nombre_plats }}
                                    </td>
                                </tr>
                            @empty
                                <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ __('Aucune déclaration enregistrée pour les patients.') }}
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
