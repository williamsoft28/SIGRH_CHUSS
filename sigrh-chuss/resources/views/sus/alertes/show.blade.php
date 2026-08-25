<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails de l\'alerte') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-8">
                
                <div class="border-b pb-4 mb-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $alerte->titre }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Reçue le {{ $alerte->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <a href="{{ route('sus.alertes.pdf', $alerte) }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                        Télécharger en PDF
                    </a>
                </div>

                <div class="mb-6">
                    <p class="text-sm font-medium text-gray-700">Concerne :</p>
                    <p class="text-gray-900">{{ $alerte->beneficiaire ? 'Le bénéficiaire ' . $alerte->beneficiaire->nom : 'Le service dans sa globalité' }}</p>
                </div>

                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded text-gray-800 whitespace-pre-line">
                    {{ $alerte->message }}
                </div>
                
                <div class="mt-8 flex justify-end">
                    <a href="{{ route('sus.alertes.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                        &larr; Retour aux alertes
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
