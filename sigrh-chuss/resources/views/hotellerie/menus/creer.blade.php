<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Composer le menu de la semaine') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($errors->any())
                <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $erreur)
                            <li>{{ $erreur }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('hotellerie.menus.create') }}" class="flex items-end gap-4">
                    <div>
                        <x-input-label for="semaine" :value="__('Semaine du')" />
                        <x-text-input id="semaine" name="semaine" type="date" class="mt-1"
                            value="{{ $lundi->toDateString() }}" />
                    </div>
                    <x-secondary-button type="submit">{{ __('Charger') }}</x-secondary-button>
                    <p class="text-sm text-gray-500">
                        {{ __('Semaine') }} {{ $lundi->weekOfYear }} / {{ $lundi->year }}
                        ({{ $lundi->format('d/m/Y') }} &rarr; {{ $dimanche->format('d/m/Y') }})
                    </p>
                </form>
            </div>

            <form method="POST" action="{{ route('hotellerie.menus.store') }}">
                @csrf
                <input type="hidden" name="date_debut" value="{{ $lundi->toDateString() }}">

                @include('hotellerie.menus._grille', ['lecture' => false])

                <div class="flex items-center justify-end mt-6 gap-4">
                    <a href="{{ route('hotellerie.menus.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        {{ __('Annuler') }}
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        {{ __('Soumettre au prestataire') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
