<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier le bénéficiaire') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('beneficiaires.update', $beneficiaire) }}">
                    @csrf
                    @method('PUT')

                    @include('beneficiaires._form')

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('beneficiaires.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            {{ __('Mettre à jour') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
