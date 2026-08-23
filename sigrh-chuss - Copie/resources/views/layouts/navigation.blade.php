<nav :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" class="absolute sm:relative z-30 sm:translate-x-0 w-64 h-full bg-white/70 backdrop-blur-2xl shadow-[4px_0_24px_rgba(0,0,0,0.02)] border-r border-white/60 flex flex-col transition-transform duration-300">
    <!-- Logo area -->
    <div class="h-16 flex-shrink-0 flex items-center justify-center border-b border-gray-100/80 px-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group w-full">
            <div class="flex items-center justify-center rounded-xl w-10 h-10 bg-chuss-amber shadow-md transform group-hover:rotate-6 transition-transform duration-300 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path>
                    <path d="M7 2v20"></path>
                    <path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path>
                </svg>
            </div>
            <span class="font-bold text-lg text-chuss-dark tracking-tight truncate">SIGRH <span class="font-light text-chuss-amber">CHUSS</span></span>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 overflow-y-auto py-6 px-3 flex flex-col space-y-2">
        @role('sus')
            <x-sidebar-link :href="route('beneficiaires.index')" :active="request()->routeIs('beneficiaires.index')">
                {{ __('Bénéficiaires') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('declarations.index')" :active="request()->routeIs('declarations.*')">
                {{ __('Déclarations') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('beneficiaires.declarations-patients.index')" :active="request()->routeIs('beneficiaires.declarations-patients.*')">
                {{ __('Patients Malades') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('derogations.index')" :active="request()->routeIs('derogations.*')">
                {{ __('Dérogations') }}
            </x-sidebar-link>
        @endrole

        @role('super_administrateur')
            <x-sidebar-link :href="route('super_admin.users.index')" :active="request()->routeIs('super_admin.users.*')">
                {{ __('Gestion Utilisateurs') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('super_admin.annees.index')" :active="request()->routeIs('super_admin.annees.*')">
                {{ __('Gestion Années') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('super_admin.derogations.index')" :active="request()->routeIs('super_admin.derogations.*')">
                {{ __('Dérogations') }}
            </x-sidebar-link>
        @endrole

        @role('administrateur')
            <x-sidebar-link :href="route('admin.declarations.index')" :active="request()->routeIs('admin.declarations.*') || request()->routeIs('admin.bons.*')">
                {{ __('Déclarations à valider') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.declarations_patients.index')" :active="request()->routeIs('admin.declarations_patients.*')">
                {{ __('Patients Malades') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.beneficiaires.jour')" :active="request()->routeIs('admin.beneficiaires.jour')">
                {{ __('Bénéficiaires du jour') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.beneficiaires.index')" :active="request()->routeIs('admin.beneficiaires.*') && ! request()->routeIs('admin.beneficiaires.jour')">
                {{ __('Bénéficiaires') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.sus.index')" :active="request()->routeIs('admin.sus.*')">
                {{ __('Comptes SUS') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.zones.index')" :active="request()->routeIs('admin.zones.*')">
                {{ __('Zones Hôtellerie') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.controle_service.index')" :active="request()->routeIs('admin.controle_service.*')">
                {{ __('Contrôle Hôtellerie') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('admin.suivi_medical.index')" :active="request()->routeIs('admin.suivi_medical.*')">
                {{ __('Suivi Médical') }}
            </x-sidebar-link>
        @endrole

        @role('controleur')
            <x-sidebar-link :href="route('controleur.scanner')" :active="request()->routeIs('controleur.*')">
                {{ __('Scanner') }}
            </x-sidebar-link>
        @endrole

        @role('service_hotellerie')
            <x-sidebar-link :href="route('hotellerie.menus.index')" :active="request()->routeIs('hotellerie.*')">
                {{ __('Menus') }}
            </x-sidebar-link>
        @endrole

        @role('prestataire')
            <x-sidebar-link :href="route('prestataire.menus.index')" :active="request()->routeIs('prestataire.menus.index') || request()->routeIs('prestataire.menus.show')">
                {{ __('Menus à examiner') }}
            </x-sidebar-link>
            <x-sidebar-link :href="route('prestataire.menus.historique')" :active="request()->routeIs('prestataire.menus.historique')">
                {{ __('Historique') }}
            </x-sidebar-link>
        @endrole
    </div>

    <!-- Bottom Actions / Logout -->
    <div class="flex-shrink-0 p-4 border-t border-white/40 mt-auto bg-gradient-to-b from-transparent to-white/50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center justify-center gap-2 w-full px-4 py-3 text-red-500 bg-red-500/5 hover:bg-red-500/15 hover:text-red-600 rounded-xl font-bold transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>{{ __('Se déconnecter') }}</span>
            </button>
        </form>
    </div>
</nav>
