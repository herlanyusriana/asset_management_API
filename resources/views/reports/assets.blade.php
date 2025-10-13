<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aset</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #0f172a;
            margin: 24px;
        }
        h1 {
            font-size: 18px;
            margin-bottom: 4px;
            color: #1d4ed8;
        }
        .meta {
            font-size: 11px;
            margin-bottom: 16px;
            color: #475569;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #cbd5f5;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #e0e7ff;
            color: #1e3a8a;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f4f7ff;
        }
    </style>
</head>
<body>
    <h1>Laporan Aset</h1>
    <div class="meta">
        Dicetak pada: {{ $generatedAt->format('d F Y H:i') }}
    </div>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Departemen</th>
                <th>Status</th>
                <th>Pemegang</th>
                <th>Lokasi</th>
                <th>Processor</th>
                <th>RAM</th>
                <th>Storage</th>
                <th>Tanggal Beli</th>
                <th>Harga Beli</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assets as $asset)
                <tr>
                    <td>{{ $asset->asset_code }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category->name ?? '-' }}</td>
                    <td>{{ $asset->department_name ?? $asset->category->department_code ?? '-' }}</td>
                    <td>{{ ucfirst($asset->status) }}</td>
                    <td>{{ $asset->current_custodian_name ?? $asset->custodian->name ?? '-' }}</td>
                    <td>{{ $asset->location ?? '-' }}</td>
                    <td>{{ $asset->processor_name ?? '-' }}</td>
                    <td>{{ $asset->ram_capacity ?? '-' }}</td>
                    <td>
                        {{ trim(implode(' ', array_filter([
                            $asset->storage_capacity,
                            $asset->storage_type,
                            $asset->storage_brand,
                        ]))) ?: '-' }}
                    </td>
                    <td>{{ optional($asset->purchase_date)->format('d-m-Y') ?? '-' }}</td>
                    <td>
                        {{ $asset->purchase_price !== null
                            ? 'Rp ' . number_format((float) $asset->purchase_price, 0, ',', '.')
                            : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align: center">Tidak ada data aset.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
