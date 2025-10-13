@extends('monitoring.layout', [
    'pageTitle' => 'Monitoring Aset',
    'pageHeading' => 'Dashboard Monitoring Aset',
])

@section('content')
    @php
        $statusOptions = [
            'available' => 'Available',
            'assigned' => 'Assigned',
            'maintenance' => 'Maintenance',
            'needs_check' => 'Needs Check',
            'retired' => 'Retired',
        ];
    @endphp

    <section>
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Ringkasan</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl bg-white p-6 shadow-sm shadow-slate-200/60">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Total Aset</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ number_format($totals['assets']) }}</p>
            </div>
            <div class="rounded-2xl bg-white p-6 shadow-sm shadow-slate-200/60">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Sedang Digunakan</p>
                <p class="mt-3 text-3xl font-semibold text-amber-600">{{ number_format($totals['assigned']) }}</p>
            </div>
            <div class="rounded-2xl bg-white p-6 shadow-sm shadow-slate-200/60">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Tersedia</p>
                <p class="mt-3 text-3xl font-semibold text-emerald-600">{{ number_format($totals['available']) }}</p>
            </div>
            <div class="rounded-2xl bg-white p-6 shadow-sm shadow-slate-200/60">
                <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Perlu Perhatian</p>
                <p class="mt-3 text-3xl font-semibold text-rose-600">{{ number_format($totals['maintenance']) }}</p>
            </div>
        </div>
    </section>

    <section class="mt-10 rounded-2xl bg-white p-6 shadow-sm shadow-slate-200/60">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Daftar Aset</h2>
                <p class="text-sm text-slate-500">Pantau status aset terkini beserta lokasi dan penanggung jawab.</p>
            </div>
            <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
                <div class="flex items-center gap-2">
                    <input type="search" name="search" value="{{ $filters['search'] }}"
                        placeholder="Cari nama, barcode, atau lokasi..."
                        class="w-full rounded-lg border-slate-200 text-sm text-slate-900 shadow-sm focus:border-primary focus:ring-primary md:w-64">
                    <select name="status"
                        class="rounded-lg border-slate-200 text-sm text-slate-900 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Semua Status</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90">
                    Terapkan
                </button>
            </form>
        </div>

        <div class="mt-6 overflow-hidden rounded-xl border border-slate-200">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Aset</th>
                        <th class="px-4 py-3">Departemen</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Penanggung Jawab</th>
                        <th class="px-4 py-3">Terakhir Diperbarui</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-slate-700">
                    @forelse ($assets as $asset)
                        <tr class="hover:bg-primary/5">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-slate-900">{{ $asset->name }}</p>
                                <p class="text-xs text-slate-500">Kode: {{ $asset->asset_code }}</p>
                                @if ($asset->serial_number)
                                    <p class="text-xs text-slate-400">SN: {{ $asset->serial_number }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                {{ $asset->department_name ?? $asset->category->department_code ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $asset->category->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold">
                                    {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                {{ $asset->current_custodian_name ?? optional($asset->custodian)->name ?? 'Belum ditetapkan' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ optional($asset->updated_at)->timezone(config('app.timezone'))->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                Tidak ada data aset yang cocok dengan filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $assets->links('monitoring.partials.pagination') }}
        </div>
    </section>

    <section class="mt-10 rounded-2xl bg-white p-6 shadow-sm shadow-slate-200/60">
        <h2 class="text-lg font-semibold text-slate-900">Distribusi Status</h2>
        <div class="mt-4 grid gap-3 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($statuses as $status => $count)
                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </p>
                    <p class="mt-2 text-2xl font-semibold text-slate-800">{{ number_format($count) }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endsection
