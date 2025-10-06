<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('finance-logo.png') }}" type="image/x-icon">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
    <meta name="theme-color" content="#111827">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    {{-- Theme Management Script --}}
    <script>
        // Set theme on page load
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @stack('head')
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <div class="min-h-screen">
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- Page Content --}}
        <main class="py-6 px-4">
            {{ $slot }}
        </main>
    </div>

    <footer class="mt-7 pb-5">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 border-t border-gray-200 dark:border-gray-700 text-center text-sm text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
        </div>
    </footer>

    @stack('modals')

    @stack('scripts')

    {{-- Theme Toggle Function --}}
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            
            // Toggle icon visibility
            updateThemeIcons();
        }
        
        function updateThemeIcons() {
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            const darkIconMobile = document.getElementById('theme-toggle-dark-icon-mobile');
            const lightIconMobile = document.getElementById('theme-toggle-light-icon-mobile');
            const isDark = document.documentElement.classList.contains('dark');
            
            if (isDark) {
                darkIcon?.classList.remove('hidden');
                lightIcon?.classList.add('hidden');
                darkIconMobile?.classList.remove('hidden');
                lightIconMobile?.classList.add('hidden');
            } else {
                darkIcon?.classList.add('hidden');
                lightIcon?.classList.remove('hidden');
                darkIconMobile?.classList.add('hidden');
                lightIconMobile?.classList.remove('hidden');
            }
        }
        
        // Update icons on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateThemeIcons();
        });
    </script>
</body>

</html>
