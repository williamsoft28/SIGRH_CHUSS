<x-guest-layout>
    <div class="text-center space-y-4">
        <h1 class="text-lg font-semibold text-gray-900">{{ __('Votre bon de repas') }}</h1>

        <p class="text-sm bg-indigo-50 text-indigo-900 rounded-md px-3 py-2">
            {{ __('Montrez cet écran (ou le QR ci-dessous) au réfectoire pour chaque repas concerné.') }}
        </p>

        <div class="flex justify-center py-2">
            <div class="p-4 bg-white border-2 border-gray-200 rounded-md">
                {!! $qr !!}
            </div>
        </div>

        <a href="{{ route('bons.public.telecharger', $bon->code_unique) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            {{ __('Enregistrer le QR code') }}
        </a>

        <dl class="text-sm text-left space-y-1 border-t pt-4 mt-4">
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('Bénéficiaire') }}</dt>
                <dd class="font-medium text-gray-900">{{ $bon->declarationJour->beneficiaire->nom }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('Service') }}</dt>
                <dd class="font-medium text-gray-900">{{ $bon->declarationJour->beneficiaire->service?->nom ?? '—' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('Période') }}</dt>
                <dd class="font-medium text-gray-900">
                    {{ $bon->date_debut->format('d/m/Y') }}
                    @if (! $bon->date_debut->equalTo($bon->date_fin))
                        &rarr; {{ $bon->date_fin->format('d/m/Y') }}
                    @endif
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">{{ __('Repas') }}</dt>
                <dd class="font-medium text-gray-900">
                    {{ collect($bon->declarationJour->repas ?? [])->map(fn ($r) => str_replace('_', ' ', $r))->implode(', ') }}
                </dd>
            </div>
        </dl>

        <p class="text-xs font-mono text-gray-400 break-all">
            {{ $bon->code_unique }}
        </p>
        @if($bon->code_court)
            <div class="mt-4 text-center">
                <p class="text-sm font-semibold text-gray-500">Ou code d'identification manuelle :</p>
                <div class="inline-block mt-1 px-4 py-2 bg-gray-100 rounded-lg text-2xl font-black tracking-widest text-chuss-dark border-2 border-gray-200">
                    {{ $bon->code_court }}
                </div>
            </div>
        @endif
    </div>
</x-guest-layout>
