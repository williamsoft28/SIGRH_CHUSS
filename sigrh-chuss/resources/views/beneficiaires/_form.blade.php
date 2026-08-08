@php
    $beneficiaire ??= null;
@endphp

<div>
    <x-input-label for="nom" :value="__('Nom')" />
    <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full"
        value="{{ old('nom', $beneficiaire?->nom) }}" required autofocus />
    <x-input-error :messages="$errors->get('nom')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="categorie" :value="__('Catégorie')" />
    <select id="categorie" name="categorie" required
        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="">{{ __('Sélectionner...') }}</option>
        @foreach (['Personnel continu', 'Personnel de garde', 'Malade', 'Apprenant'] as $option)
            <option value="{{ $option }}" @selected(old('categorie', $beneficiaire?->categorie) === $option)>
                {{ $option }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('categorie')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="type" :value="__('Type')" />
    <select id="type" name="type" required
        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="regulier" @selected(old('type', $beneficiaire?->type) === 'regulier')>{{ __('Régulier') }}</option>
        <option value="variable" @selected(old('type', $beneficiaire?->type) === 'variable')>{{ __('Variable') }}</option>
    </select>
    <x-input-error :messages="$errors->get('type')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="numero_whatsapp" :value="__('Numéro WhatsApp')" />
    <x-text-input id="numero_whatsapp" name="numero_whatsapp" type="text" class="mt-1 block w-full"
        value="{{ old('numero_whatsapp', $beneficiaire?->numero_whatsapp) }}" />
    <x-input-error :messages="$errors->get('numero_whatsapp')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="email" :value="__('Email (pour l’envoi du bon)')" />
    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
        value="{{ old('email', $beneficiaire?->email) }}" />
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="regime_special_id" :value="__('Régime spécial (optionnel)')" />
    <select id="regime_special_id" name="regime_special_id"
        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="">{{ __('Aucun') }}</option>
        @foreach ($regimesSpeciaux as $regime)
            <option value="{{ $regime->id }}" @selected((int) old('regime_special_id', $beneficiaire?->regime_special_id) === $regime->id)>
                {{ $regime->libelle }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('regime_special_id')" class="mt-2" />
</div>
