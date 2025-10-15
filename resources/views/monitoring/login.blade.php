<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Monitoring Aset</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100">
    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl shadow-slate-200/60">
        <div class="mb-6 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.4em] text-blue-600">Geum Cheon Assets</p>
            <h1 class="mt-2 text-2xl font-bold text-blue-700">Monitoring Aset</h1>
            <p class="mt-1 text-sm text-slate-500">Masuk untuk melihat ringkasan aset perusahaan.</p>
        </div>

        <form method="POST" action="{{ route('monitoring.login.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="block w-full rounded-lg border-slate-200 text-slate-900 shadow-sm focus:border-blue-600 focus:ring-blue-600" />
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                <input type="password" id="password" name="password" required
                    class="block w-full rounded-lg border-slate-200 text-slate-900 shadow-sm focus:border-blue-600 focus:ring-blue-600" />
                @error('password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600">
                    Ingat saya
                </label>
                <span class="text-xs text-slate-400">Akses internal Geumcheon</span>
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600/40">
                Masuk
            </button>
        </form>

        <p class="mt-8 text-center text-xs text-slate-400">
            Hubungi tim IT jika mengalami kendala akses.
        </p>
    </div>
</body>
</html>
