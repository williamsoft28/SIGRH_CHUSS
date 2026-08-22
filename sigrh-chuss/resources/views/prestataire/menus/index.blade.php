<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nouveaux Menus (À examiner)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white/80 backdrop-blur-xl overflow-hidden shadow-float rounded-2xl border border-white/60">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gradient-to-r from-chuss-green/5 to-chuss-amber/5 border-b border-gray-100">
                            <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Semaine') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Période') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Soumis le') }}</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100/50">
                            @forelse ($menus as $menu)
                                <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">S{{ $menu->numero_semaine }} / {{ $menu->annee }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $menu->date_debut->format('d/m/Y') }} &rarr; {{ $menu->date_fin->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $menu->date_soumission?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('prestataire.menus.show', $menu) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ __('Examiner') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ __('Aucun menu en attente.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <a href="{{ route('prestataire.menus.historique') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                {{ __('Voir l’historique de tous les menus') }} &rarr;
            </a>
        </div>
    </div>
</x-app-layout>
