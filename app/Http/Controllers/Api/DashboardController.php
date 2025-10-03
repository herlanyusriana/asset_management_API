<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\AssetCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $department = $request->user()?->department_code;

        $assetsQuery = Asset::query();
        $categoriesQuery = AssetCategory::query();
        $assignmentsQuery = AssetAssignment::query()->with(['asset', 'assignedTo']);

        if ($department) {
            $assetsQuery->whereHas('category', fn ($q) => $q->where('department_code', $department));
            $categoriesQuery->where('department_code', $department);
            $assignmentsQuery->where('department_code', $department);
        }

        $totalAssets = $assetsQuery->count();
        $criticalAssets = (clone $assetsQuery)->whereIn('status', ['maintenance', 'retired'])->count();

        $categories = $categoriesQuery
            ->withCount(['assets'])
            ->orderBy('name')
            ->get()
            ->map(fn (AssetCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'department_code' => $category->department_code,
                'asset_count' => $category->assets_count,
            ]);

        $recentActivities = $assignmentsQuery
            ->orderByDesc('assigned_at')
            ->limit(10)
            ->get()
            ->map(fn (AssetAssignment $assignment) => [
                'asset' => [
                    'id' => $assignment->asset_id,
                    'name' => $assignment->asset->name,
                    'asset_code' => $assignment->asset->asset_code,
                    'status' => $assignment->asset->status,
                ],
                'assigned_to' => $assignment->assignedTo?->name,
                'assigned_at' => $assignment->assigned_at,
                'returned_at' => $assignment->returned_at,
                'notes' => $assignment->notes,
            ]);

        return response()->json([
            'summary' => [
                'total_assets' => $totalAssets,
                'critical_assets' => $criticalAssets,
            ],
            'categories' => $categories,
            'recent_activities' => $recentActivities,
        ]);
    }
}
