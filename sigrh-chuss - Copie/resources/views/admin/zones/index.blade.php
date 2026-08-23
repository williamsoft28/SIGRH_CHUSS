<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Zones à Servir') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Messages de succès/erreur -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ajouter une nouvelle zone</h3>
                    <form action="{{ route('admin.zones.store') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                        @csrf
                        <div class="w-full md:w-1/3">
                            <x-input-label for="nom" value="Nom de la zone *" />
                            <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" required placeholder="Ex: Maternité" />
                        </div>
                        <div class="w-full md:w-1/3">
                            <x-input-label for="emplacement" value="Emplacement (Optionnel)" />
                            <x-text-input id="emplacement" name="emplacement" type="text" class="mt-1 block w-full" placeholder="Ex: Bâtiment A, RDC" />
                        </div>
                        <div>
                            <x-primary-button>Ajouter</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-xl overflow-hidden shadow-float rounded-2xl border border-white/60">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Liste des zones</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gradient-to-r from-chuss-green/5 to-chuss-amber/5 border-b border-gray-100">
                                <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                    <th scope="col" class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">Nom de la zone</th>
                                    <th scope="col" class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">Emplacement</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100/50">
                                @forelse ($zones as $zone)
                                    <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $zone->nom }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $zone->emplacement ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <!-- Bouton Supprimer -->
                                            <form action="{{ route('admin.zones.destroy', $zone->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette zone ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Aucune zone n'a été créée pour le moment.</td>
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
