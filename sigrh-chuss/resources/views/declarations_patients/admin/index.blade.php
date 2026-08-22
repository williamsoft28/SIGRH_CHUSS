<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Suivi des Déclarations Patients (Bénéficiaires Malades)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-6 shadow sm:rounded-lg mb-6">
                <form method="GET" action="{{ route('admin.declarations_patients.index') }}" class="flex items-end gap-4" id="date-filter-form">
                    <div>
                        <x-input-label for="date" :value="__('Filtrer par date')" />
                        <x-text-input id="date" name="date" type="date" class="mt-1 block w-full"
                                      value="{{ $date->toDateString() }}" onchange="document.getElementById('date-filter-form').submit()" />
                    </div>
                    <x-primary-button type="submit">
                        {{ __('Filtrer') }}
                    </x-primary-button>
                </form>
            </div>

            <div class="bg-white/80 backdrop-blur-xl overflow-hidden shadow-float rounded-2xl border border-white/60 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Récapitulatif du {{ $date->format('d/m/Y') }}</h3>

                @if($declarationsParService->isEmpty())
                    <p class="text-sm text-gray-500">Aucune déclaration de patient pour cette date.</p>
                @else
                    <div class="space-y-8">
                        @foreach($declarationsParService as $nomService => $decls)
                            <div>
                                <h4 class="text-md font-bold text-chuss-dark border-b pb-2 mb-4">{{ $nomService }}</h4>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Régime Spécial</th>
                                                <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                                <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase tracking-wider">Déclaré par</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($decls as $decl)
                                                <tr>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $decl->regimeSpecial->libelle }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-indigo-600">{{ $decl->nombre_plats }}</td>
                                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $decl->sus->user->nom ?? 'SUS' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-sm font-bold text-gray-900 text-right">Total:</th>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900">{{ $decls->sum('nombre_plats') }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-8 pt-4 border-t-2 border-gray-200">
                            <div class="flex justify-between items-center text-lg font-bold text-gray-900">
                                <span>Total Général pour le CHUSS:</span>
                                <span>{{ $declarations->sum('nombre_plats') }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
