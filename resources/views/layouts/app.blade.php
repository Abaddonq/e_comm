<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Critical CSS -->
        <style>
            body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; }
            .page-loader { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: #f3f4f6; z-index: 9998; display: flex; align-items: center; justify-content: center; }
            .page-loader.hidden { opacity: 0; pointer-events: none; transition: opacity 0.3s; }
            .loader-spinner { width: 40px; height: 40px; border: 3px solid #e5e7eb; border-top: 3px solid #374151; border-radius: 50%; animation: spin 1s linear infinite; }
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        </style>

        <!-- Page Loader Script -->
        <script>
            window.addEventListener('load', function() {
                var loader = document.getElementById('pageLoader');
                if (loader) {
                    loader.classList.add('hidden');
                    setTimeout(function() { loader.remove(); }, 300);
                }
            });
        </script>
    </head>
    <body class="font-sans antialiased">
    <!-- Page Loader -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-spinner"></div>
    </div>
    
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
