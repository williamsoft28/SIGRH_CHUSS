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

            <div class="flex justify-between items-center bg-white shadow-sm sm:rounded-lg p-6">
                <div>
                    <h3 class="font-medium text-gray-900 mb-1">{{ __('Actions globales (Aujourd\'hui)') }}</h3>
                    <p class="text-sm text-gray-500">Débloquez la saisie pour tous les services simultanément pour la journée en cours.</p>
                </div>
                <div class="flex gap-4">
                    <form method="POST" action="{{ route('super_admin.derogations.tout-debloquer') }}">
                        @csrf
                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                            {{ __('Tout débloquer') }}
                        </x-primary-button>
                    </form>
                    <form method="POST" action="{{ route('super_admin.derogations.tout-rebloquer') }}">
                        @csrf
                        <x-primary-button class="bg-red-600 hover:bg-red-700 focus:ring-red-500">
                            {{ __('Tout rebloquer') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-4">{{ __('Accorder directement une dérogation') }}</h3>
                <form method="POST" action="{{ route('super_admin.derogations.store') }}" class="flex flex-wrap items-end gap-4">
                    @csrf
                    <div>
                        <x-input-label for="service_id" :value="__('Service')" />
                        <select id="service_id" name="service_id" required
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="all">{{ __('-- Tous les services --') }}</option>
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
                    <div>
                        <x-input-label for="heure_debut" :value="__('De (Heure)')" />
                        <x-text-input id="heure_debut" name="heure_debut" type="time" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="heure_fin" :value="__('À (Heure)')" />
                        <x-text-input id="heure_fin" name="heure_fin" type="time" class="mt-1" />
                    </div>
                    <x-primary-button type="submit">{{ __('Accorder / Débloquer') }}</x-primary-button>
                </form>
            </div>

            <div class="bg-white/80 backdrop-blur-xl overflow-hidden shadow-float rounded-2xl border border-white/60">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gradient-to-r from-chuss-green/5 to-chuss-amber/5 border-b border-gray-100">
                            <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Service') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Date & Heures') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Demandée par') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Motif') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Statut') }}</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100/50">
                            @forelse ($derogations as $derogation)
                                <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ $derogation->service->nom }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $derogation->date->format('d/m/Y') }}
                                        @if($derogation->heure_debut || $derogation->heure_fin)
                                            <br>
                                            <span class="text-xs text-gray-500">
                                                {{ $derogation->heure_debut ? \Carbon\Carbon::parse($derogation->heure_debut)->format('H:i') : '...' }} - 
                                                {{ $derogation->heure_fin ? \Carbon\Carbon::parse($derogation->heure_fin)->format('H:i') : '...' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $derogation->demandePar?->name ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $derogation->motif ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <x-derogation-statut-badge :statut="$derogation->statut" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                        @if ($derogation->statut !== 'autorisee')
                                            <form method="POST" action="{{ route('super_admin.derogations.autoriser', $derogation) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">{{ __('Autoriser') }}</button>
                                            </form>
                                        @endif
                                        @if ($derogation->statut !== 'refusee')
                                            <form method="POST" action="{{ route('super_admin.derogations.refuser', $derogation) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Refuser') }}</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('super_admin.derogations.destroy', $derogation) }}" class="inline ml-2" onsubmit="return confirm('Voulez-vous vraiment rebloquer cette dérogation ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-500 hover:text-gray-800 font-semibold">{{ __('Rebloquer') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="hover:bg-white/60 transition-colors duration-200 group">
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
