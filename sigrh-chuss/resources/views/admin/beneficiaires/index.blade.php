<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tous les bénéficiaires') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex justify-between items-center gap-4">
                <form method="GET" action="{{ route('admin.beneficiaires.index') }}" class="flex items-end gap-2">
                    <div>
                        <x-input-label for="service_id" :value="__('Filtrer par service')" />
                        <select id="service_id" name="service_id" onchange="this.form.submit()"
                            class="mt-1 block border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('Tous les services') }}</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" @selected(request('service_id') == $service->id)>
                                    {{ $service->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <a href="{{ route('admin.beneficiaires.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Ajouter un bénéficiaire') }}
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nom') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Service') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Catégorie') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Type') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Régime spécial') }}</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($beneficiaires as $beneficiaire)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $beneficiaire->nom }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $beneficiaire->service?->nom ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $beneficiaire->categorie }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $beneficiaire->type === 'regulier' ? 'Régulier' : 'Variable' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $beneficiaire->regimeSpecial?->libelle ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('admin.beneficiaires.edit', $beneficiaire) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ __('Modifier') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ __('Aucun bénéficiaire enregistré.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $beneficiaires->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
