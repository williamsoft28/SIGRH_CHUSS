<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bénéficiaires du jour') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('admin.beneficiaires.jour') }}" class="flex items-end gap-4">
                    <div>
                        <x-input-label for="date" :value="__('Date')" />
                        <x-text-input id="date" name="date" type="date" class="mt-1"
                            value="{{ $date->toDateString() }}" />
                    </div>
                    <x-secondary-button type="submit">{{ __('Charger') }}</x-secondary-button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Bénéficiaire') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Repas') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Statut') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($declarations as $declaration)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $declaration->beneficiaire->service?->nom ?? '—' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $declaration->beneficiaire->nom }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ collect($declaration->repas)->map(fn ($r) => str_replace('_', ' ', $r))->implode(', ') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <x-declaration-statut-badge :statut="$declaration->statut" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">
                                        {{ __('Aucun bénéficiaire déclaré pour cette date.') }}
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
