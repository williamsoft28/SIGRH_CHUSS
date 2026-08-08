<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Demandes de dérogation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
                <h3 class="font-medium text-gray-900 mb-4">{{ __('Nouvelle demande de saisie d’urgence') }}</h3>
                <form method="POST" action="{{ route('derogations.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="date" :value="__('Date concernée')" />
                        <x-text-input id="date" name="date" type="date" class="mt-1 block w-full"
                            value="{{ old('date', today()->toDateString()) }}" required />
                    </div>
                    <div>
                        <x-input-label for="motif" :value="__('Motif (optionnel)')" />
                        <textarea id="motif" name="motif" rows="2"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('motif') }}</textarea>
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Envoyer la demande') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Motif') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Statut') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($derogations as $derogation)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $derogation->date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $derogation->motif ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <x-derogation-statut-badge :statut="$derogation->statut" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
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
