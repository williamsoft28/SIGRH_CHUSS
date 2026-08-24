<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier un compte SUS') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="text-sm text-gray-500 mb-6">
                    {{ __("Vous modifiez les informations de") }} <strong>{{ $sus->name }}</strong>.
                </p>

                <form method="POST" action="{{ route('admin.sus.update', $sus) }}">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="prenom" :value="__('Prénom')" />
                        <x-text-input id="prenom" name="prenom" type="text" class="mt-1 block w-full"
                            value="{{ old('prenom', $sus->prenom) }}" required autofocus />
                        <x-input-error :messages="$errors->get('prenom')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="nom" :value="__('Nom')" />
                        <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full"
                            value="{{ old('nom', $sus->nom) }}" required />
                        <x-input-error :messages="$errors->get('nom')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="matricule" :value="__('Matricule')" />
                        <x-text-input id="matricule" name="matricule" type="text" class="mt-1 block w-full"
                            value="{{ old('matricule', $sus->matricule) }}" required />
                        <x-input-error :messages="$errors->get('matricule')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="service_id" :value="__('Service / unité')" />
                        <select id="service_id" name="service_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id', $sus->service_id) == $service->id ? 'selected' : '' }}>
                                    {{ $service->nom }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('service_id')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="email" :value="__('Adresse email personnelle (pour la récupération du mot de passe)')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                            value="{{ old('email', $sus->email) }}" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('admin.sus.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
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
