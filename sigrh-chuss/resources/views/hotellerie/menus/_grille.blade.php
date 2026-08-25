@php
    $lecture ??= false;
    $libellesRepas = [
        'petit_dejeuner' => 'Petit-déjeuner',
        'dejeuner' => 'Déjeuner',
        'diner' => 'Dîner',
    ];
@endphp

<div class="space-y-6">
    @foreach ($jours as $jour)
        @php $dateStr = $jour->toDateString(); @endphp
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
            <h4 class="font-medium text-gray-900 mb-3">
                <span class="capitalize">{{ $jour->locale('fr')->translatedFormat('l') }}</span>
                <span class="text-gray-400 font-normal">{{ $jour->format('d/m/Y') }}</span>
            </h4>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse no-grid">
                    <thead class="bg-gradient-to-r from-chuss-green/5 to-chuss-amber/5 border-b border-gray-100">
                        <tr class="hover:bg-white/60 transition-colors duration-200 group">
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Repas') }}</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Plat') }}</th>
                            
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Viande') }}</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Dessert') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100/50">
                        @foreach ($libellesRepas as $typeRepas => $libelle)
                            @php
                                $selection = $repasExistant[$dateStr][$typeRepas] ?? [];
                                $platsDisponibles = $typeRepas === 'petit_dejeuner' ? $platsPetitDej : $platsBase;
                            @endphp
                            <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                <td class="px-3 py-2 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $libelle }}</td>

                                @if ($lecture)
                                    <td class="px-3 py-2 text-sm text-gray-700">{{ $platsDisponibles->firstWhere('id', $selection['plat_id'] ?? null)->nom ?? '—' }}</td>
                                    @if ($typeRepas === 'petit_dejeuner')
                                        <td class="px-3 py-2 text-sm text-gray-400 italic text-center bg-gray-50">—</td>
                                        <td class="px-3 py-2 text-sm text-gray-400 italic text-center bg-gray-50">—</td>
                                    @else
                                        <td class="px-3 py-2 text-sm text-gray-700">{{ $viandes->firstWhere('id', $selection['viande_id'] ?? null)->nom ?? '—' }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-700">{{ $desserts->firstWhere('id', $selection['dessert_id'] ?? null)->nom ?? '—' }}</td>
                                    @endif
                                @else
                                    <td class="px-3 py-2">
                                        <select name="repas[{{ $dateStr }}][{{ $typeRepas }}][plat_id]"
                                            class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="">Sélectionnez un plat...</option>
                                            @foreach ($platsDisponibles as $plat)
                                                <option value="{{ $plat->id }}" @selected(old("repas.{$dateStr}.{$typeRepas}.plat_id", $selection['plat_id'] ?? null) == $plat->id)>
                                                    {{ $plat->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    @if ($typeRepas === 'petit_dejeuner')
                                        <td class="px-3 py-2 text-sm text-gray-400 italic text-center bg-gray-50">N/A</td>
                                        <td class="px-3 py-2 text-sm text-gray-400 italic text-center bg-gray-50">N/A</td>
                                    @else
                                        <td class="px-3 py-2">
                                            <select name="repas[{{ $dateStr }}][{{ $typeRepas }}][viande_id]"
                                                class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                <option value="">—</option>
                                                @foreach ($viandes as $viande)
                                                    <option value="{{ $viande->id }}" @selected(old("repas.{$dateStr}.{$typeRepas}.viande_id", $selection['viande_id'] ?? null) == $viande->id)>
                                                        {{ $viande->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <select name="repas[{{ $dateStr }}][{{ $typeRepas }}][dessert_id]"
                                                class="block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                <option value="">—</option>
                                                @foreach ($desserts as $dessert)
                                                    <option value="{{ $dessert->id }}" @selected(old("repas.{$dateStr}.{$typeRepas}.dessert_id", $selection['dessert_id'] ?? null) == $dessert->id)>
                                                        {{ $dessert->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endif
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
