<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NEXOPS') }}</title>

        <!-- Font Inter -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-surface">

            <!-- Sidebar -->
            <x-sidebar />

            <!-- Main Content (offset by sidebar width) -->
            <div class="ml-60">

                <!-- Topbar -->
                <x-topbar />

                <!-- Page Content -->
                <main class="p-6">
                    {{ $slot }}
                </main>

            </div>
        </div>

        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
        <script>lucide.createIcons();</script>
    </body>
</html>
