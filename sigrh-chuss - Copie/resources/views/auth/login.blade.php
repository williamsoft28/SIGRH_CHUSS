<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Connexion - Restauration Hospitalière CHUSS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .animated-bg {
            background: linear-gradient(-45deg, #0f3f2a, #155f3e, #0a2e1f, #1a7a4f);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .grid-pattern {
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        }
        .text-glow {
            text-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
        }
        .input-chic {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f8fafc;
            border: none;
            box-shadow: inset 5px 5px 10px #cbd5e1, inset -5px -5px 10px #ffffff;
        }
        .input-chic:focus {
            background: #f8fafc;
            box-shadow: inset 8px 8px 15px #cbd5e1, inset -8px -8px 15px #ffffff;
            outline: none;
        }
    </style>
</head>
<body class="font-sans antialiased text-chuss-dark min-h-screen flex items-center justify-center relative overflow-hidden bg-[#faf9f6]">

    <!-- Overall Background Orbs -->
    <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-chuss-green/20 rounded-full mix-blend-multiply filter blur-[100px] animate-pulse z-0" style="animation-duration: 8s;"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-chuss-amber/20 rounded-full mix-blend-multiply filter blur-[100px] animate-pulse z-0" style="animation-duration: 12s;"></div>

    <!-- The Ultra-Premium Center Card -->
    <div class="relative z-10 w-full max-w-[1100px] mx-4 my-8 min-h-[650px] flex flex-col md:flex-row bg-white/70 backdrop-blur-3xl rounded-[2.5rem] shadow-[0_30px_60px_-15px_rgba(0,0,0,0.15)] overflow-hidden border border-white/80 transform transition-all duration-500 hover:shadow-[0_40px_70px_-15px_rgba(0,0,0,0.2)]">
        
        <!-- LEFT SIDE: Monumental Branding Inside Card -->
        <div class="w-full md:w-5/12 animated-bg relative flex flex-col justify-center items-center p-10 md:p-14 overflow-hidden shadow-[inset_-20px_0_50px_rgba(0,0,0,0.1)] rounded-r-[6rem] lg:rounded-r-[12rem] z-20">
            
            <!-- Architectural Grid Overlay -->
            <div class="absolute inset-0 grid-pattern z-0 opacity-50"></div>
            
            <!-- Glowing Orbs Inside Card -->
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-chuss-amber/30 rounded-full filter blur-[80px] mix-blend-screen z-0 animate-pulse" style="animation-duration: 6s;"></div>

            <!-- Main Content Centered -->
            <div class="relative z-10 flex flex-col items-center text-center my-auto w-full">
                <div class="w-20 h-20 mb-6 bg-white/10 backdrop-blur-xl rounded-2xl flex items-center justify-center border border-white/20 shadow-xl">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-14 h-14 object-contain" onerror="this.style.display='none'; document.getElementById('fb-icon').style.display='block';">
                    <svg id="fb-icon" class="w-10 h-10 text-white hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                
                <h1 class="text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight mb-4 text-glow">
                    SIGRH <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-chuss-amber to-yellow-200">
                        CHUSS
                    </span>
                </h1>
                
                <div class="h-1 w-12 bg-chuss-amber rounded-full mb-6 mt-2"></div>
                
                <p class="text-lg text-white/90 font-medium leading-relaxed max-w-[250px]">
                    Système Intégré de Gestion de la Restauration Hospitalière.
                </p>
            </div>
        </div>

        <!-- RIGHT SIDE: Form Inside Card -->
        <div class="w-full md:w-7/12 flex flex-col justify-center items-center p-8 md:p-12 lg:p-16 relative z-10 bg-white/50 overflow-hidden">
            
            <!-- Local Background Watermark for the form side -->
            <div class="absolute inset-0 z-0 pointer-events-none flex items-center justify-center opacity-15 overflow-hidden">
                <img src="{{ asset('images/logo.png') }}" alt="Watermark" class="w-[95%] object-contain grayscale transform -translate-y-8">
            </div>

            <div class="w-full max-w-md relative z-10">
                
                <div class="mb-10 text-center md:text-left">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-2 tracking-tight">Bienvenue</h2>
                    <p class="text-gray-500 text-base">Connectez-vous à votre espace sécurisé.</p>
                </div>

                <x-auth-session-status class="mb-6" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Identifiant -->
                    <div class="space-y-2 group">
                        <label for="email" class="block text-xs font-bold text-gray-600 tracking-wider uppercase ml-1">Identifiant ou Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-chuss-green transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                                class="input-chic pl-11 block w-full rounded-2xl text-gray-900 font-medium px-4 py-3.5 sm:text-base outline-none"
                                placeholder="Entrez votre identifiant" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-500 font-medium" />
                    </div>

                    <!-- Mot de passe -->
                    <div class="space-y-2 group pt-1">
                        <div class="flex items-center justify-between ml-1">
                            <label for="password" class="block text-xs font-bold text-gray-600 tracking-wider uppercase">Mot de passe</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-bold text-chuss-green hover:text-chuss-amber transition-colors">
                                    Oublié ?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-chuss-green transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="input-chic pl-11 block w-full rounded-2xl text-gray-900 font-bold px-4 py-3.5 sm:text-base tracking-widest outline-none"
                                placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-500 font-medium" />
                    </div>

                    <!-- Options -->
                    <div class="flex items-center pt-2 ml-1">
                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-chuss-green focus:ring-chuss-green/50 cursor-pointer transition-colors bg-white shadow-sm">
                            </div>
                            <div class="ml-2.5 text-sm">
                                <label for="remember_me" class="font-semibold text-gray-500 cursor-pointer select-none hover:text-gray-800 transition-colors">Se souvenir de moi</label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6">
                        <button type="submit" class="group relative w-full flex justify-center items-center py-4 px-4 rounded-2xl overflow-hidden font-bold text-white bg-gradient-to-r from-[#0f3f2a] to-[#1a6a44] hover:from-[#1a6a44] hover:to-[#0f3f2a] transition-all duration-300 transform hover:-translate-y-1 shadow-[0_12px_25px_-8px_rgba(15,63,42,0.5)] focus:outline-none focus:ring-4 focus:ring-[#0f3f2a]/30">
                            <span class="absolute left-0 w-8 h-32 -mt-12 transition-all duration-1000 transform -translate-x-12 bg-white opacity-10 rotate-12 group-hover:translate-x-[400px] ease"></span>
                            <span class="relative text-base tracking-wide">Se connecter</span>
                            <svg class="relative ml-2 w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </form>
                
                <p class="text-center text-[10px] text-gray-400 mt-12 font-bold tracking-widest uppercase">
                    © {{ date('Y') }} CHUSS Bobo-Dioulasso
                </p>
            </div>
        </div>
    </div>

</body>
</html>
