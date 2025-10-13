<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? trim($__env->yieldContent('title', 'Monitoring Aset')) }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-slate-100 min-h-screen text-slate-900">
    <header class="bg-white shadow-sm">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.4em] text-primary/70">Geumcheon Asset Monitoring</p>
                <h1 class="mt-1 text-xl font-bold text-slate-900">
                    {{ $pageHeading ?? trim($__env->yieldContent('heading', 'Dashboard Monitoring Aset')) }}
                </h1>
            </div>
            <form action="{{ route('monitoring.logout') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary/40">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @isset($breadcrumbs)
            <nav class="mb-6 text-sm text-slate-500">
                {{ $breadcrumbs }}
            </nav>
        @endisset

        @if (session('status'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-4 text-xs text-slate-500 sm:flex-row sm:px-6 lg:px-8">
            <p>&copy; {{ now()->year }} Geumcheon Indonesia. All rights reserved.</p>
            <p class="flex items-center gap-2">
                <span class="inline-flex h-2 w-2 rounded-full bg-primary"></span>
                Monitoring sistem aset internal
            </p>
        </div>
    </footer>
</body>
</html>
