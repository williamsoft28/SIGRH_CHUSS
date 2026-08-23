<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pointage du service Hôtellerie') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Filtre de date -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <form method="GET" action="{{ route('admin.controle_service.index') }}" class="flex items-end gap-4">
                    <div>
                        <x-input-label for="date" value="Date du service" />
                        <x-text-input id="date" name="date" type="date" value="{{ $date }}" class="mt-1 block" max="{{ now()->toDateString() }}" onchange="this.form.submit()" />
                    </div>
                    <div class="pb-1 text-sm text-gray-500">
                        @if ($isLocked)
                            <span class="text-red-600 font-semibold flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                Délai de 24h dépassé. Les données sont verrouillées.
                            </span>
                        @else
                            <span class="text-green-600 font-semibold">
                                Modifications autorisées pour cette date.
                            </span>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Liste des zones -->
            <div class="bg-white/80 backdrop-blur-xl overflow-hidden shadow-float rounded-2xl border border-white/60">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gradient-to-r from-chuss-green/5 to-chuss-amber/5 border-b border-gray-100">
                                <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                    <th scope="col" class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">Zone</th>
                                    <th scope="col" class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">Heure service / Observation</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100/50">
                                @foreach ($zones as $zone)
                                    @php
                                        $service = $services->get($zone->id);
                                        $statut = $service ? $service->statut : 'en_attente';
                                    @endphp
                                    <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $zone->nom }}</div>
                                            <div class="text-sm text-gray-500">{{ $zone->emplacement }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($statut === 'servi')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Servi</span>
                                            @elseif ($statut === 'non_servi')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Non Servi</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($statut === 'servi')
                                                <div class="text-sm text-gray-900">{{ Carbon\Carbon::parse($service->heure_service)->format('H:i') }}</div>
                                            @elseif ($statut === 'non_servi')
                                                <div class="text-sm text-red-600 line-clamp-2" title="{{ $service->observation }}">{{ $service->observation }}</div>
                                            @else
                                                <div class="text-sm text-gray-500">-</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if (!$isLocked)
                                                <div class="flex justify-end gap-2">
                                                    <!-- Formulaire Valider -->
                                                    <form action="{{ route('admin.controle_service.valider', $zone->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <input type="hidden" name="date" value="{{ $date }}">
                                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                            Servi
                                                        </button>
                                                    </form>
                                                    
                                                    <!-- Bouton Modal Signaler -->
                                                    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'signaler-zone-{{ $zone->id }}')" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        Non servi
                                                    </button>
                                                </div>

                                                <!-- Modal Signaler -->
                                                <x-modal name="signaler-zone-{{ $zone->id }}" focusable>
                                                    <form method="POST" action="{{ route('admin.controle_service.signaler', $zone->id) }}" class="p-6">
                                                        @csrf
                                                        <input type="hidden" name="date" value="{{ $date }}">

                                                        <h2 class="text-lg font-medium text-gray-900">
                                                            Signaler un manquement pour la zone : {{ $zone->nom }}
                                                        </h2>

                                                        <div class="mt-4">
                                                            <x-input-label for="observation_{{ $zone->id }}" value="Motif du non-service (transmis aux pénalités)" />
                                                            <textarea id="observation_{{ $zone->id }}" name="observation" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
                                                        </div>

                                                        <div class="mt-6 flex justify-end">
                                                            <x-secondary-button x-on:click="$dispatch('close')">
                                                                Annuler
                                                            </x-secondary-button>

                                                            <x-danger-button class="ms-3">
                                                                Signaler le non-service
                                                            </x-danger-button>
                                                        </div>
                                                    </form>
                                                </x-modal>
                                            @else
                                                <span class="text-gray-400 text-xs italic">Verrouillé</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
