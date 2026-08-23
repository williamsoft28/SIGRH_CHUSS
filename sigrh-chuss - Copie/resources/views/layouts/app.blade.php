<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIGRH CHUSS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased text-chuss-dark bg-chuss-cream relative h-screen overflow-hidden">
        
        <!-- Decorative Background Blob -->
        <div class="fixed top-[-10%] left-[-10%] w-[50%] h-[50%] bg-chuss-green/5 rounded-full mix-blend-multiply filter blur-3xl pointer-events-none z-0"></div>
        <div class="fixed bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-chuss-amber/5 rounded-full mix-blend-multiply filter blur-3xl pointer-events-none z-0"></div>

        <!-- Logo Background Watermark -->
        <div class="fixed inset-0 z-0 pointer-events-none flex items-center justify-center overflow-hidden opacity-[0.12]">
            <img src="{{ asset('images/logo.png') }}" alt="Background Watermark" class="w-[90vw] max-w-[900px] object-contain filter blur-[1px]">
        </div>

        <div class="relative z-10 h-screen flex overflow-hidden" x-data="{ sidebarOpen: false }">
            
            <!-- Sidebar Navigation -->
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-y-auto">
                <!-- Top Header Bar -->
                @include('layouts.topbar')

                <!-- Page Heading -->
                @isset($header)
                    <header class="max-w-7xl mx-auto w-full pt-8 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center justify-between">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-grow max-w-7xl mx-auto w-full py-8 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
                
                <!-- Simple Footer -->
                <footer class="mt-auto py-6 text-center text-sm text-chuss-gray">
                    &copy; {{ date('Y') }} Centre Hospitalier Universitaire Sourou Sanou - Tous droits réservés.
                </footer>
            </div>
        </div>
    </body>
</html>
