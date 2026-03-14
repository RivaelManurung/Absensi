<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Absensi App')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @wireUiScripts
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-sky-50 to-amber-50 text-slate-800" style="font-family: 'Space Grotesk', sans-serif;">
    <x-notifications />

    <div class="relative overflow-hidden">
        <div class="pointer-events-none absolute -top-32 -right-20 h-72 w-72 rounded-full bg-sky-300/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-amber-300/20 blur-3xl"></div>

        <div class="relative flex flex-col lg:flex-row">
            <x-layout.sidebar />

            <main class="flex-1 p-4 md:p-8">
                <header class="mb-6 rounded-2xl border border-slate-200 bg-white/80 backdrop-blur px-5 py-4 shadow-sm">
                    <h2 class="text-2xl font-bold tracking-tight">@yield('page_title', 'Dashboard')</h2>
                    <p class="text-sm text-slate-500">@yield('page_description', 'Manage attendance with clean and consistent workflows.')</p>
                </header>

                <section class="rounded-2xl border border-slate-200 bg-white/80 backdrop-blur p-4 md:p-6 shadow-sm">
                    @yield('content')
                </section>
            </main>
        </div>
    </div>

    @livewireScripts

    @if (session('success') || $errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (! window.$wireui) {
                    return;
                }

                @if (session('success'))
                    window.$wireui.notify({
                        title: 'Success',
                        description: @json(session('success')),
                        icon: 'success',
                    });
                @endif

                @if ($errors->any())
                    window.$wireui.notify({
                        title: 'Validation Error',
                        description: 'Please check the form input and try again.',
                        icon: 'error',
                    });
                @endif
            });
        </script>
    @endif
</body>
</html>
