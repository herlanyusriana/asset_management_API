<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function exportAssets(Request $request): StreamedResponse
    {
        $format = strtolower((string) $request->query('format', 'excel'));
        $user = $request->user();

        $query = Asset::query()->with(['category', 'custodian'])->orderBy('name');

        if ($user?->department_code) {
            $query->whereHas(
                'category',
                fn ($q) => $q->where('department_code', $user->department_code)
            );
        }

        $assets = $query->get();

        return $format === 'pdf'
            ? $this->exportPdf($assets)
            : $this->exportCsv($assets);
    }

    /**
     * @param \Illuminate\Support\Collection<int,\App\Models\Asset> $assets
     */
    private function exportCsv($assets): StreamedResponse
    {
        $filename = 'assets-' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = [
            'Kode Asset',
            'Nama',
            'Kategori',
            'Departemen',
            'Status',
            'Pemegang',
            'Lokasi',
            'Tanggal Beli',
            'Harga Beli',
            'Processor',
            'RAM',
            'Storage',
            'Catatan',
        ];

        $callback = static function () use ($assets, $columns): void {
            $handle = fopen('php://output', 'wb');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($handle, $columns);

            foreach ($assets as $asset) {
                fputcsv($handle, [
                    $asset->asset_code,
                    $asset->name,
                    $asset->category?->name,
                    $asset->category?->department_code,
                    ucfirst($asset->status),
                    $asset->current_custodian_name ?? $asset->custodian?->name,
                    $asset->location,
                    optional($asset->purchase_date)?->toDateString(),
                    $asset->purchase_price,
                    $asset->processor_name,
                    $asset->ram_capacity,
                    trim(implode(' ', array_filter([
                        $asset->storage_capacity,
                        $asset->storage_type,
                        $asset->storage_brand,
                    ]))),
                    $asset->condition_notes,
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * @param \Illuminate\Support\Collection<int,\App\Models\Asset> $assets
     */
    private function exportPdf($assets): StreamedResponse
    {
        $pdf = Pdf::loadView('reports.assets', [
            'assets' => $assets,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        $filename = 'assets-' . now()->format('Ymd_His') . '.pdf';

        return response()->streamDownload(
            static fn () => print $pdf->output(),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }
}
