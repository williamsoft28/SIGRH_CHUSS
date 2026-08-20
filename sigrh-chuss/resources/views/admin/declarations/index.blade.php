<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Validation des déclarations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('admin.declarations.index') }}" class="flex items-end gap-4">
                    <div>
                        <x-input-label for="date" :value="__('Date de repas')" />
                        <x-text-input id="date" name="date" type="date" class="mt-1"
                            value="{{ $date->toDateString() }}" />
                    </div>
                    <x-secondary-button type="submit">{{ __('Charger') }}</x-secondary-button>
                </form>
            </div>

            <div class="bg-white/80 backdrop-blur-xl overflow-hidden shadow-float rounded-2xl border border-white/60">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gradient-to-r from-chuss-green/5 to-chuss-amber/5 border-b border-gray-100">
                            <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Bénéficiaire') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Repas') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Période') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Statut') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Valider') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100/50">
                            @forelse ($declarations as $declaration)
                                <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $declaration->beneficiaire->service->nom }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $declaration->beneficiaire->nom }}
                                        @if ($declaration->deroge)
                                            <span class="ml-1 text-xs text-amber-600">({{ __('dérogation') }})</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ collect($declaration->repas)->map(fn ($r) => str_replace('_', ' ', $r))->implode(', ') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $declaration->date_debut->format('d/m/Y') }}
                                        @if (! $declaration->date_debut->equalTo($declaration->date_fin))
                                            &rarr; {{ $declaration->date_fin->format('d/m/Y') }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <x-declaration-statut-badge :statut="$declaration->statut" />
                                    </td>
                                    <td class="px-4 py-4">
                                        <form method="POST" action="{{ route('admin.declarations.valider', $declaration) }}" class="flex items-center gap-2">
                                            @csrf
                                            <select name="canal_envoi" required
                                                class="text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                <option value="whatsapp">WhatsApp</option>
                                                <option value="email">{{ __('Email') }}</option>
                                                <option value="tiers">{{ __('Tiers') }}</option>
                                            </select>
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                                {{ __('Valider') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                    <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">
                                        {{ __('Aucune déclaration en attente pour cette date.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
