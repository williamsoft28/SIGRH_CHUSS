<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bon de repas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Bénéficiaire') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $bon->declarationJour->beneficiaire->nom }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Service') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $bon->declarationJour->beneficiaire->service->nom }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Période') }}</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $bon->date_debut->format('d/m/Y') }}
                            @if (! $bon->date_debut->equalTo($bon->date_fin))
                                &rarr; {{ $bon->date_fin->format('d/m/Y') }}
                            @endif
                            ({{ $bon->type_periode }})
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Canal d’envoi') }}</dt>
                        <dd class="font-medium text-gray-900 capitalize">{{ $bon->canal_envoi }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-gray-500">{{ __('Code unique') }}</dt>
                        <dd class="font-mono text-xs text-gray-900 break-all">{{ $bon->code_unique }}</dd>
                    </div>
                    @if($bon->code_court)
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 bg-gray-50">
                        <dt class="text-sm font-medium text-gray-500">Code Court (Manuel)</dt>
                        <dd class="font-mono text-lg font-bold text-gray-900 break-all bg-white px-3 py-1 rounded inline-block">{{ $bon->code_court }}</dd>
                    </div>
                    @endif
                </dl>

                <div class="flex flex-col items-center gap-4 border-t pt-6">
                    <div class="p-4 bg-white border rounded-md">
                        {!! $qr !!}
                    </div>
                    <a href="{{ route($routeTelecharger, $bon) }}"
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        {{ __('Télécharger le QR code') }}
                    </a>

                    <div class="flex flex-wrap justify-center gap-3 w-full border-t pt-4">
                        <form method="POST" action="{{ route($routeEnvoyerEmail, $bon) }}">
                            @csrf
                            <button type="submit"
                                @disabled(empty($bon->declarationJour->beneficiaire->email))
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed">
                                {{ __('Envoyer par email') }}
                            </button>
                        </form>
                        @unless (empty($bon->declarationJour->beneficiaire->email))
                            <p class="text-xs text-gray-400 self-center">{{ $bon->declarationJour->beneficiaire->email }}</p>
                        @else
                            <p class="text-xs text-red-400 self-center">{{ __('Aucun email renseigné') }}</p>
                        @endunless

                        <form method="POST" action="{{ route($routeEnvoyerWhatsapp, $bon) }}">
                            @csrf
                            <button type="submit"
                                @disabled(! $aNumeroWhatsapp)
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 disabled:opacity-40 disabled:cursor-not-allowed">
                                {{ __('Envoyer sur WhatsApp') }}
                            </button>
                        </form>
                        @unless ($aNumeroWhatsapp)
                            <p class="text-xs text-red-400 self-center">{{ __('Aucun numéro WhatsApp renseigné') }}</p>
                        @endunless
                    </div>
                </div>
            </div>

            <a href="{{ $retourUrl }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; {{ __($retourLabel) }}
            </a>
        </div>
    </div>
</x-app-layout>
