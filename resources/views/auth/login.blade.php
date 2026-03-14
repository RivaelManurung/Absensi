<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Absensi</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @wireUiScripts
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-sky-900 to-amber-700 text-white" style="font-family: 'Space Grotesk', sans-serif;">
    <x-notifications />

    <div class="min-h-screen grid place-items-center px-4">
        <div class="w-full max-w-md rounded-3xl bg-white text-slate-800 p-7 shadow-2xl border border-slate-200">
            <p class="text-xs uppercase tracking-[0.25em] text-sky-600">Absensi System</p>
            <h1 class="text-3xl font-bold mt-2">Welcome Back</h1>
            <p class="text-sm text-slate-500 mt-1">Sign in using your account credentials.</p>

            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                @csrf

                <x-form.input
                    label="Email"
                    name="email"
                    type="email"
                    placeholder="you@company.com"
                    :value="old('email')"
                    required
                />

                <x-form.input
                    label="Password"
                    name="password"
                    type="password"
                    placeholder="******"
                    required
                />

                <x-button type="submit" primary label="Sign In" class="w-full" />
            </form>
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
                        title: 'Login Failed',
                        description: @json($errors->first()),
                        icon: 'error',
                    });
                @endif
            });
        </script>
    @endif
</body>
</html>
