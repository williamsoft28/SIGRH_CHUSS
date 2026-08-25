<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Alertes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if ($alertes->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            Aucune alerte reçue. Excellent travail !
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase">
                                        <th class="px-4 py-3">Date</th>
                                        <th class="px-4 py-3">Objet</th>
                                        <th class="px-4 py-3">Bénéficiaire concerné</th>
                                        <th class="px-4 py-3">Statut</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($alertes as $alerte)
                                        <tr class="hover:bg-gray-50 {{ !$alerte->lue ? 'font-bold bg-red-50' : '' }}">
                                            <td class="px-4 py-3 text-sm">{{ $alerte->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $alerte->titre }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $alerte->beneficiaire ? $alerte->beneficiaire->nom : 'Générale' }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                @if(!$alerte->lue)
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Non lue</span>
                                                @else
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Lue</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm text-right space-x-2">
                                                <a href="{{ route('sus.alertes.show', $alerte) }}" class="text-indigo-600 hover:text-indigo-900">Ouvrir</a>
                                                <a href="{{ route('sus.alertes.pdf', $alerte) }}" class="text-red-600 hover:text-red-900">PDF</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $alertes->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
