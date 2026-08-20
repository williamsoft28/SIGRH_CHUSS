<div class="bg-white/90 backdrop-blur-md border-b border-gray-100 shadow-sm z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" class="sm:hidden inline-flex items-center justify-center p-2 rounded-xl text-chuss-dark bg-chuss-amber/10 hover:bg-chuss-amber/20 transition">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div>
                <p class="text-sm font-medium text-chuss-gray">Espace administratif</p>
                <p class="text-lg font-semibold text-chuss-dark">{{ isset($header) ? trim(strip_tags($header->toHtml())) : __('Bienvenue') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="hidden sm:flex flex-col text-right">
                <span class="text-sm font-semibold text-chuss-dark">{{ Auth::user()->name }}</span>
                <span class="text-xs text-chuss-gray">{{ Auth::user()->email }}</span>
            </div>
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-chuss-green text-white text-sm font-medium hover:bg-chuss-green-light transition">
                {{ __('Mon profil') }}
            </a>
        </div>
    </div>
</div>
