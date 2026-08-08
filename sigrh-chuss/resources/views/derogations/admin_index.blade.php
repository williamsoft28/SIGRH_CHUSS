<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dérogations de saisie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-4">{{ __('Accorder directement une dérogation') }}</h3>
                <form method="POST" action="{{ route('admin.derogations.store') }}" class="flex items-end gap-4">
                    @csrf
                    <div>
                        <x-input-label for="service_id" :value="__('Service')" />
                        <select id="service_id" name="service_id" required
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="date" :value="__('Date')" />
                        <x-text-input id="date" name="date" type="date" class="mt-1"
                            value="{{ today()->toDateString() }}" required />
                    </div>
                    <x-primary-button type="submit">{{ __('Accorder') }}</x-primary-button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Demandée par') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Motif') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Statut') }}</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($derogations as $derogation)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $derogation->service->nom }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $derogation->date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $derogation->demandePar?->name ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $derogation->motif ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <x-derogation-statut-badge :statut="$derogation->statut" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                        @if ($derogation->statut !== 'autorisee')
                                            <form method="POST" action="{{ route('admin.derogations.autoriser', $derogation) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">{{ __('Autoriser') }}</button>
                                            </form>
                                        @endif
                                        @if ($derogation->statut !== 'refusee')
                                            <form method="POST" action="{{ route('admin.derogations.refuser', $derogation) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Refuser') }}</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ __('Aucune demande de dérogation.') }}
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
