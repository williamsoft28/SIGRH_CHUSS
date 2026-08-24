<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Comptes SUS') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('identifiants'))
                @php $identifiants = session('identifiants'); @endphp
                <div class="bg-amber-50 border border-amber-300 text-amber-900 px-4 py-4 rounded-md space-y-2">
                    <p class="font-semibold">
                        {{ __('Identifiants générés pour') }} {{ $identifiants['nom'] }}
                        &mdash; {{ __('à transmettre maintenant, ils ne seront plus affichés ensuite.') }}
                    </p>
                    <dl class="grid grid-cols-2 gap-2 text-sm max-w-md">
                        <dt class="text-amber-700">{{ __('Identifiant') }}</dt>
                        <dd class="font-mono font-semibold">{{ $identifiants['username'] }}</dd>
                        <dt class="text-amber-700">{{ __('Mot de passe') }}</dt>
                        <dd class="font-mono font-semibold">{{ $identifiants['password'] }}</dd>
                    </dl>
                    @if ($identifiants['email_envoye'] ?? false)
                        <p class="text-sm text-green-700">
                            ✓ {{ __('Envoyé par email à') }} {{ $identifiants['email'] }}.
                        </p>
                    @else
                        <p class="text-sm text-red-700">
                            ⚠ {{ __("L'envoi par email a échoué (vérifie la configuration SMTP) — transmets ces identifiants manuellement.") }}
                        </p>
                    @endif
                </div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('admin.sus.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Créer un compte SUS') }}
                </a>
            </div>

            <div class="bg-white/80 backdrop-blur-xl overflow-hidden shadow-float rounded-2xl border border-white/60">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gradient-to-r from-chuss-green/5 to-chuss-amber/5 border-b border-gray-100">
                            <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Nom') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Matricule') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Service') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Identifiant') }}</th>
                                <th class="px-6 py-4 text-xs font-bold text-chuss-dark uppercase tracking-wider">{{ __('Email de récupération') }}</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100/50">
                            @forelse ($comptes as $compte)
                                <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ $compte->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $compte->matricule ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $compte->service?->nom ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500">{{ $compte->username ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $compte->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('admin.sus.edit', $compte) }}" class="text-chuss-green hover:text-chuss-amber font-semibold transition-colors">
                                                {{ __('Modifier') }}
                                            </a>
                                            <form method="POST" action="{{ route('admin.sus.reinitialiser-mot-de-passe', $compte) }}"
                                                  onsubmit="return confirm('{{ __('Générer un nouveau mot de passe pour ce compte ?') }}');">
                                                @csrf
                                                <button type="submit" class="text-indigo-600 hover:text-indigo-900 font-semibold transition-colors">
                                                    {{ __('Réinitialiser MDP') }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.sus.destroy', $compte) }}"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce compte SUS ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold transition-colors">
                                                    {{ __('Supprimer') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="hover:bg-white/60 transition-colors duration-200 group">
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ __('Aucun compte SUS créé.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
