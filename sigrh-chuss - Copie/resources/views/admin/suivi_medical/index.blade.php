<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Suivi Médical des Agents') }}
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

            <div class="bg-white/80 backdrop-blur-xl overflow-hidden shadow-float rounded-2xl border border-white/60">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Agents de votre service</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gradient-to-r from-chuss-green/5 to-chuss-amber/5 border-b border-gray-100">
                                <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                    <th scope="col" class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">Agent (Matricule)</th>
                                    <th scope="col" class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">Dernière / Prochaine Visite</th>
                                    <th scope="col" class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">Statut</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100/50">
                                @forelse ($agents as $agent)
                                    @php
                                        $derniereVisite = $agent->visitesMedicales->first();
                                    @endphp
                                    <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $agent->prenom }} {{ $agent->nom }}</div>
                                            <div class="text-sm text-gray-500">{{ $agent->matricule }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                            @if ($derniereVisite)
                                                {{ \Carbon\Carbon::parse($derniereVisite->date_programmee)->format('d/m/Y') }}
                                            @else
                                                <span class="text-gray-400 italic">Aucune visite programmée</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if (!$derniereVisite)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Non défini</span>
                                            @elseif ($derniereVisite->statut === 'realisee')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Réalisée</span>
                                            @elseif ($derniereVisite->statut === 'depassee')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Dépassée</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Programmée</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            
                                            <!-- Bouton Programmer Visite -->
                                            <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'programmer-visite-{{ $agent->id }}')" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                Programmer
                                            </button>

                                            <!-- Modal Programmer -->
                                            <x-modal name="programmer-visite-{{ $agent->id }}" focusable>
                                                <form method="POST" action="{{ route('admin.suivi_medical.store') }}" class="p-6 text-left">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $agent->id }}">

                                                    <h2 class="text-lg font-medium text-gray-900">
                                                        Programmer une visite pour {{ $agent->prenom }} {{ $agent->nom }}
                                                    </h2>

                                                    <div class="mt-4">
                                                        <x-input-label for="date_programmee_{{ $agent->id }}" value="Date prévue" />
                                                        <x-text-input id="date_programmee_{{ $agent->id }}" name="date_programmee" type="date" class="mt-1 block w-full" min="{{ now()->toDateString() }}" required />
                                                    </div>

                                                    <div class="mt-6 flex justify-end">
                                                        <x-secondary-button x-on:click="$dispatch('close')">Annuler</x-secondary-button>
                                                        <x-primary-button class="ms-3">Enregistrer</x-primary-button>
                                                    </div>
                                                </form>
                                            </x-modal>

                                            @if ($derniereVisite && in_array($derniereVisite->statut, ['programmee', 'depassee']))
                                                <!-- Bouton Saisir Résultat -->
                                                <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'saisir-resultat-{{ $derniereVisite->id }}')" class="text-green-600 hover:text-green-900">
                                                    Saisir résultat
                                                </button>

                                                <!-- Modal Saisir Résultat -->
                                                <x-modal name="saisir-resultat-{{ $derniereVisite->id }}" focusable>
                                                    <form method="POST" action="{{ route('admin.suivi_medical.update', $derniereVisite->id) }}" class="p-6 text-left">
                                                        @csrf
                                                        @method('PUT')

                                                        <h2 class="text-lg font-medium text-gray-900">
                                                            Résultat de la visite du {{ \Carbon\Carbon::parse($derniereVisite->date_programmee)->format('d/m/Y') }}
                                                        </h2>

                                                        <div class="mt-4">
                                                            <x-input-label for="resultat_{{ $derniereVisite->id }}" value="Observations / Résultat" />
                                                            <textarea id="resultat_{{ $derniereVisite->id }}" name="resultat" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
                                                        </div>

                                                        <div class="mt-6 flex justify-end">
                                                            <x-secondary-button x-on:click="$dispatch('close')">Annuler</x-secondary-button>
                                                            <x-primary-button class="ms-3">Valider la visite</x-primary-button>
                                                        </div>
                                                    </form>
                                                </x-modal>
                                            @endif

                                        </td>
                                    </tr>
                                @empty
                                    <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Aucun agent trouvé dans votre service.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
