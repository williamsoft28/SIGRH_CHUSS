<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Années') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('super_admin.annees.store') }}" class="flex gap-4">
                        @csrf
                        <div>
                            <input type="text" name="nom" placeholder="Ex: 2026-2027" class="shadow appearance-none border rounded py-2 px-3 text-gray-700" required>
                        </div>
                        <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded">Ajouter l'Année</button>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="table-auto w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Année</th>
                                <th class="px-4 py-2 text-left">Statut</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($annees as $annee)
                                <tr>
                                    <td class="border px-4 py-2">{{ $annee->nom }}</td>
                                    <td class="border px-4 py-2">
                                        @if($annee->est_archivee)
                                            <span class="text-red-500">Archivée</span>
                                        @else
                                            <span class="text-green-500">Active</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2">
                                        @if(!$annee->est_archivee)
                                            <form method="POST" action="{{ route('super_admin.annees.archiver', $annee) }}">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900">Archiver</button>
                                            </form>
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
</x-app-layout>
