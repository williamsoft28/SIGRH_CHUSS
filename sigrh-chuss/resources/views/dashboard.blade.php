<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-3xl text-chuss-dark leading-tight tracking-tight">
                {{ __('Tableau de Bord') }}
            </h2>
            <p class="text-sm font-medium text-chuss-gray mt-1">Aperçu général de votre espace</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @hasrole('administrateur')
                <!-- Admin Dashboard Section -->
                
                <!-- Welcome Banner -->
                <div class="bg-gradient-to-r from-chuss-green to-chuss-green-light rounded-3xl p-8 mb-8 text-white relative overflow-hidden shadow-float">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
                    <div class="relative z-10">
                        <h3 class="text-2xl md:text-3xl font-bold mb-2">Bonjour, {{ Auth::user()->name }} 👋</h3>
                        <p class="text-chuss-cream text-lg opacity-90 max-w-2xl">
                            Bienvenue sur votre tableau de bord administrateur. Voici un aperçu des activités récentes de l'application SIGRH CHUSS.
                        </p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Stat Card 1 -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow group relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-chuss-amber/10 rounded-full blur-xl group-hover:bg-chuss-amber/20 transition-colors"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-sm font-semibold text-chuss-gray uppercase tracking-wider mb-1">Bénéficiaires</p>
                                <h4 class="text-3xl font-bold text-chuss-dark">
                                    {{ class_exists('\App\Models\Beneficiaire') ? \App\Models\Beneficiaire::count() : '...' }}
                                </h4>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-chuss-amber/10 flex items-center justify-center text-chuss-amber">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                  <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm text-green-600 font-medium">
                            <span>Total enregistrés</span>
                        </div>
                    </div>

                    <!-- Stat Card 2 -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow group relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-red-500/10 rounded-full blur-xl group-hover:bg-red-500/20 transition-colors"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-sm font-semibold text-chuss-gray uppercase tracking-wider mb-1">Déclarations</p>
                                <h4 class="text-3xl font-bold text-chuss-dark">
                                    {{ class_exists('\App\Models\Declaration') ? \App\Models\Declaration::count() : '...' }}
                                </h4>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm text-chuss-gray font-medium">
                            <span>Déclarations reçues</span>
                        </div>
                    </div>

                    <!-- Stat Card 3 -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow group relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/10 rounded-full blur-xl group-hover:bg-blue-500/20 transition-colors"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-sm font-semibold text-chuss-gray uppercase tracking-wider mb-1">Comptes SUS</p>
                                <h4 class="text-3xl font-bold text-chuss-dark">
                                    {{ class_exists('\App\Models\Sus') ? \App\Models\Sus::count() : '...' }}
                                </h4>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                  <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm text-chuss-gray font-medium">
                            <span>Services d'urgence</span>
                        </div>
                    </div>

                    <!-- Stat Card 4 -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow group relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-500/10 rounded-full blur-xl group-hover:bg-purple-500/20 transition-colors"></div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-sm font-semibold text-chuss-gray uppercase tracking-wider mb-1">Zones</p>
                                <h4 class="text-3xl font-bold text-chuss-dark">
                                    {{ class_exists('\App\Models\Zone') ? \App\Models\Zone::count() : '...' }}
                                </h4>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-sm text-chuss-gray font-medium">
                            <span>Zones configurées</span>
                        </div>
                    </div>
                </div>

                <!-- Actions & Recent -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Actions -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 col-span-1 flex flex-col">
                        <h4 class="text-lg font-bold text-chuss-dark mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-chuss-amber" viewBox="0 0 20 20" fill="currentColor">
                              <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                            </svg>
                            Actions Rapides
                        </h4>
                        
                        <div class="space-y-3 flex-grow">
                            <a href="{{ route('admin.beneficiaires.create') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-colors group">
                                <div class="w-10 h-10 rounded-full bg-chuss-green/10 flex items-center justify-center text-chuss-green group-hover:scale-110 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <span class="font-medium text-chuss-dark">Ajouter un bénéficiaire</span>
                            </a>
                            
                            <a href="{{ route('admin.sus.create') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-colors group">
                                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <span class="font-medium text-chuss-dark">Créer un compte SUS</span>
                            </a>
                            
                            <a href="{{ route('admin.zones.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-colors group">
                                <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-500 group-hover:scale-110 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                                    </svg>
                                </div>
                                <span class="font-medium text-chuss-dark">Gérer les zones</span>
                            </a>
                        </div>
                    </div>

                    <!-- Activité -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 col-span-1 lg:col-span-2">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-bold text-chuss-dark flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-chuss-green" viewBox="0 0 20 20" fill="currentColor">
                                  <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                </svg>
                                Rappels & Activité
                            </h4>
                            <a href="{{ route('admin.declarations.index') }}" class="text-sm font-medium text-chuss-green hover:underline">Voir tout</a>
                        </div>
                        
                        <div class="bg-gray-50 rounded-xl p-8 text-center flex flex-col items-center justify-center border border-dashed border-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-chuss-gray font-medium">Pour l'instant, c'est calme.</p>
                            <p class="text-sm text-gray-400 mt-1">Les dernières activités apparaîtront ici.</p>
                        </div>
                    </div>
                </div>

            @else
                <!-- Default Dashboard for other roles (SUS, Prestataire, etc.) -->
                <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-soft border border-white/20 overflow-hidden relative group">
                    <!-- Decorative element -->
                    <div class="absolute -right-20 -top-20 w-40 h-40 bg-chuss-green/5 rounded-full blur-2xl group-hover:bg-chuss-green/10 transition-colors duration-500 pointer-events-none"></div>

                    <div class="p-8 md:p-12 relative z-10">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-chuss-amber/10 flex items-center justify-center text-chuss-amber">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-chuss-dark">Bienvenue, {{ Auth::user()->name }} !</h3>
                        </div>
                        
                        <p class="text-chuss-gray leading-relaxed text-lg font-medium">
                            {{ __("Vous êtes connecté avec succès à l'application SIGRH CHUSS.") }}
                        </p>

                        <div class="mt-8 pt-8 border-t border-gray-100 flex flex-wrap gap-4">
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl shadow-sm text-sm font-bold text-white bg-chuss-green hover:bg-chuss-green-light hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-chuss-green transition-all duration-200">
                                Configurer mon profil
                            </a>
                        </div>
                    </div>
                </div>
            @endhasrole

        </div>
    </div>
</x-app-layout>
