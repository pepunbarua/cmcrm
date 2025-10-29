<!doctype html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CheckMate Events - Photography & Cinematography CRM' }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <script>
        // Initialize theme before page renders
        if (localStorage.theme === 'light' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="antialiased bg-gray-50 dark:bg-gradient-to-br dark:from-slate-900 dark:via-purple-900 dark:to-slate-900 min-h-screen">
    {{ $slot }}
</body>
</html>
